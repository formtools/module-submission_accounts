<?php

namespace FormTools\Modules\SubmissionAccounts;

use FormTools\Core;
use FormTools\Fields;
use FormTools\General;
use FormTools\Sessions;
use FormTools\Settings;
use FormTools\Submissions;
use PDO, Exception;


class Users
{
    /**
     * This function attempts to log a user in. If it succeeds, they're simply redirected to their user
     * accounts (i.e. submission) page. If not, it either returns the error for display on the login page
     * or - if the "invalid_login_redirect_url" key is set with a redirect URL, sends the login error
     * code (flag) and the login .
     *
     * This option lets users embed a fully functional login form in their own pages. See the documentation
     * for more information.
     *
     * @param array $info
     * @return string an error message if unsuccessful, or redirects to the appropriate login page.
     */
    public static function login($info, $L)
    {
        $db = Core::$db;
        $root_url = Core::getRootUrl();

        $redirect_url = (isset($info["invalid_login_redirect_url"])) ? $info["invalid_login_redirect_url"] : "";

        $error_codes = array();
        if (!isset($info["form_id"])) {
            $error_codes[] = "no_form_id";
        }
        if (!isset($info["username"]) || empty($info["username"])) {
            $error_codes[] = "no_username";
        }
        if (!isset($info["password"]) || empty($info["password"])) {
            $error_codes[] = "no_password";
        }

        // if there are any problems at this juncture, just return / display the error
        if (!empty($error_codes)) {
            if (empty($redirect_url)) {
                // if there isn't a form
                if (in_array("no_form_id", $error_codes)) {
                    return $L["validation_no_form_id"];
                } else {
                    if (in_array("no_username", $error_codes)) {
                        return $L["validation_no_username"];
                    } else {
                        if (in_array("no_password", $error_codes)) {
                            return $L["validation_no_password"];
                        }
                    }
                }
            } else {
                if (!empty($error_codes)) {
                    $params = array();
                    $params[] = "error_codes=" . join(",", $error_codes);
                    if (isset($info["form_id"])) {
                        $params[] = "form_id={$info["form_id"]}";
                    }
                    if (isset($info["username"])) {
                        $params[] = "username={$info["username"]}";
                    }

                    $query_str = join("&", $params);
                    header("location: $redirect_url?$query_str");
                    exit;
                }
            }
        }

        $form_id = $info["form_id"];

        // now do a couple more checks: confirm the submission exists and that the password is correct
        $submission_account = Admin::getSubmissionAccount($form_id);

        if (!isset($submission_account["form_id"])) {
            if (empty($redirect_url)) {
                return $L["validation_login_invalid_form_id"];
            } else {
                header("location: $redirect_url?error_code=invalid_form_id");
                exit;
            }
        }

        if ($submission_account["is_active"] == "no") {
            if (empty($redirect_url)) {
                return $L["notify_submission_account_inactive"];
            } else {
                header("location: $redirect_url?error_code=form_inactive");
                exit;
            }
        }


        // get the db column name which is acting as the username
        $field_info = Fields::getFormField($submission_account["username_field_id"]);
        $username_col = $field_info["col_name"];

        $field_info = Fields::getFormField($submission_account["password_field_id"]);
        $password_col = $field_info["col_name"];

        // now let's take a look at the database table and see if this user exists
        $db->query("
            SELECT *
            FROM   {PREFIX}form_{$form_id}
            WHERE  $username_col = :username
        ");
        $db->bind("username", $info["username"]);
        $db->execute();
        $submissions = $db->fetchAll();

        // since there may be multiple users with the same username (we're relying on the administrator to enforce it, we'll
        // assume they've been a little lapse in their duties...), loop through all results found and log them in under the
        // FIRST user that matches the exact username-password combo.
        $account_found = false;
        $submission_info = array();
        foreach ($submissions as $submission) {
            if ($submission[$password_col] == $info["password"]) {
                $account_found = true;
                $submission_info = $submission;
                break;
            }
        }

        if (!$account_found) {
            if (empty($redirect_url)) {
                return $L["validation_login_incorrect"];
            } else {
                header("location: $redirect_url?error_code=login_incorrect");
                exit;
            }
        }

        $submission_id = $submission_info["submission_id"];

        // finally, we're good to go! Log this user as having logged in, then Log the user in. Note that we empty sessions before
        // doing so. This just prevents any conflicts in the sessions
        $db->query("
            SELECT *
            FROM {PREFIX}module_submission_accounts_data
            WHERE form_id = :form_id AND
                  submission_id = :submission_id
        ");
        $db->bindAll(array(
            "form_id" => $form_id,
            "submission_id" => $submission_id
        ));
        $db->execute();
        $num_results = $db->numRows();

        $now = General::getCurrentDatetime();
        if ($num_results == 0) {
            $db->query("
                INSERT INTO {PREFIX}module_submission_accounts_data (form_id, submission_id, last_logged_in)
                VALUES (:form_id, :submission_id, :last_logged_in)
            ");
            $db->bindAll(array(
                "form_id" => $form_id,
                "submission_id" => $submission_id,
                "last_logged_in" => $now
            ));
            $db->execute();
        } else {
            $db->query("
                UPDATE {PREFIX}module_submission_accounts_data
                SET    last_logged_in = :last_logged_in
                WHERE  form_id = :form_id AND
                       submission_id = :submission_id
            ");
            $db->bindAll(array(
                "last_logged_in" => $now,
                "form_id" => $form_id,
                "submission_id" => $submission_id,
            ));
            $db->execute();
        }

        // now figure out what View the user's supposed to see
        $view_id = self::getSubmissionView($form_id, $submission_id);

        $settings = Settings::get();

        Sessions::clearAll();
        Sessions::set("account", array(
            "is_logged_in" => true,

            // the secret sauce to allow the User->checkAuth method to accept this user. The whole auth is in need of refactoring.
            "account_id" => "user",
            "account_type" => "user",

            "theme" => $submission_account["theme"],
            "swatch" => $submission_account["swatch"],
            "ui_language" => "en_us", // always in English right now
            "timezone_offset" => $settings["default_timezone_offset"],
            "form_id" => $form_id,
            "view_id" => $view_id,
            "submission_id" => $submission_info["submission_id"],
            "settings" => $settings
        ));

        $menu_template_info = self::cacheAccountMenu($form_id);

        $login_url = "$root_url/modules/submission_accounts/users/";
        if (count($menu_template_info) > 0) {
            $login_url = $menu_template_info[0]["url"];
        }

        session_write_close();
        header("Location: $login_url");
        exit;
    }


    /**
     * This function is called whenever a user logs in. It determines the exact content of a menu for the form
     * they are assigned to and caches it in the "menu" session key. On each page, to draw the
     * menu, call the ft_build_menu function to actually draw the page.
     *
     * @param integer $form_id
     */
    public static function cacheAccountMenu($form_id)
    {
        $root_url = Core::getRootUrl();

        $menu_info = Admin::getFormMenu($form_id);

        $menu_template_info = array();
        for ($i = 0; $i < count($menu_info); $i++) {
            $curr_item = $menu_info[$i];

            $url = (preg_match("/^http/", $curr_item["url"])) ? $curr_item["url"] : $root_url . $curr_item["url"];

            $menu_template_info[] = array(
                "url" => $url,
                "display_text" => $curr_item["display_text"],
                "page_identifier" => $curr_item["page_identifier"],
                "is_submenu" => $curr_item["is_submenu"]
            );
        }

        Sessions::set("menu.menu_items", $menu_template_info);

        return $menu_template_info;
    }


    /**
     * Used by the "forget password?" page to have a client's login information sent to them.
     *
     * @param array $info the $_POST containing a "username" key. That value is used to find the user
     *      account information to email them.
     * @return array [0]: true/false (success / failure)
     *               [1]: message string
     */
    public static function sendPassword($form_id, $info, $L)
    {
        $db = Core::$db;
        $LANG = Core::$L;
        $root_url = Core::getRootUrl();
        $root_dir = Core::getRootDir();

        $submission_account = Admin::getSubmissionAccount($form_id);

        // this should never occur, since the user can only get to the page that calls this function IF
        // the email field has been set, but just in case...
        $email_field_id = $submission_account["email_field_id"];
        if (empty($email_field_id)) {
            return array(false, $L["notify_email_field_not_configured"]);
        }

        $field_info = Fields::getFormField($email_field_id);
        $email_col = $field_info["col_name"];

        // confirm the email address has been included
        $email = isset($info["email"]) ? $info["email"] : "";
        $email = trim($email);

        if (empty($email)) {
            $success = false;
            $message = $L["validation_no_email"];
            return array($success, $message);
        }

        // now see if we can find this email address in this form
        $db->query("
            SELECT *
            FROM   {PREFIX}form_{$form_id}
            WHERE  $email_col = :email
            LIMIT 1
        ");
        $db->bind("email", $email);
        $db->execute();

        if ($db->numRows() == 0) {
            return array(false, $L["validation_email_not_found"]);
        }

        // okay - validation over, let's send the email
        $submission_info = $db->fetch();

        $field_info = Fields::getFormField($submission_account["username_field_id"]);
        $username_col = $field_info["col_name"];
        $username = $submission_info[$username_col];

        $field_info = Fields::getFormField($submission_account["password_field_id"]);
        $password_col = $field_info["col_name"];
        $password = $submission_info[$password_col];


        // 1. build the email content
        $placeholders = array(
            "login_url" => "$root_url/modules/submission_accounts/login.php?form_id=$form_id",
            "email" => $email,
            "username" => $username,
            "password" => $password
        );

        $smarty_email_content = file_get_contents("$root_dir/modules/submission_accounts/templates/emails/forget_password.tpl");
        $email_content = General::evalSmartyString($smarty_email_content, $placeholders);

        // 2. build the email subject line
        $placeholders = array(
            "phrase_password_reminder" => $L["phrase_password_reminder"]
        );
        $smarty_email_subject = file_get_contents("$root_dir/modules/submission_accounts/templates/emails/forget_password_subject.tpl");
        $email_subject = trim(General::evalSmartyString($smarty_email_subject, $placeholders));

        // send email [note: the double quotes around the email recipient and content are intentional:
        // some systems fail without it]
        if (!@mail("$email", $email_subject, $email_content)) {
            $success = false;
            $message = $LANG["notify_email_not_sent"];
            return array($success, $message);
        }

        return array(true, $LANG["notify_email_sent"]);
    }


    /**
     * With the introduction of the View Override function in 1.1.0, the form View that the user
     * sees when they log in is dependant on the contents of their submission. This function determines
     * what View should be seen. It's called when they first log in and
     */
    public static function getSubmissionView($form_id, $submission_id)
    {
        $submission_account = Admin::getSubmissionAccount($form_id);

        // if there aren't any View overrides defined for this Submission Account, just return the default View ID
        $view_id = $submission_account["view_id"];
        if (empty($submission_account["view_overrides"])) {
            return $view_id;
        }

        $submission_info = Submissions::getSubmission($form_id, $submission_id);

        // loop through the (ordered) view overrides and
        foreach ($submission_account["view_overrides"] as $view_override_info) {
            $has_matched_view_override = false;
            foreach ($submission_info as $field_info) {
                if ($field_info["field_id"] == $view_override_info["field_id"]) {
                    $match_values = explode("|", $view_override_info["match_values"]);
                    if (in_array($field_info["content"], $match_values)) {
                        $has_matched_view_override = true;
                        $view_id = $view_override_info["view_id"];
                    }
                    break;
                }
            }

            if ($has_matched_view_override) {
                break;
            }
        }

        return $view_id;
    }
}
