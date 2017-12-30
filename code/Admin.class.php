<?php

namespace FormTools\Modules\SubmissionAccounts;

use FormTools\Core;
use FormTools\Forms;
use FormTools\Views;
use FormTools\Modules;
use FormTools\Settings;
use PDO, Exception;


class Admin
{
    /**
     * Adds a submission account to the database.
     *
     * @param array $info the POST contents
     */
    public static function addSubmissionAccount($info, $L)
    {
        $db = Core::$db;

        $form_id = $info["form_id"];
        $theme = $info["theme"];
        $swatch = "";
        if (isset($info["{$theme}_theme_swatches"])) {
            $swatch = $info["{$theme}_theme_swatches"];
        }

        try {
            $db->query("
                INSERT INTO {PREFIX}module_submission_accounts (form_id, view_id, theme, swatch, is_active, email_field_id,
                    username_field_id, password_field_id)
                VALUES (:form_id, :view_id, :theme, :swatch, :is_active, :email_field_id, :username_field_id, :password_field_id)
            ");
            $db->bindAll(array(
                "form_id" => $form_id,
                "view_id" => $info["view_id"],
                "theme" => $theme,
                "swatch" => $swatch,
                "is_active" => "yes",
                "email_field_id" => (isset($info["email_field_id"]) && !empty($info["email_field_id"])) ? $info["email_field_id"] : null,
                "username_field_id" => $info["username_field_id"],
                "password_field_id" => $info["password_field_id"]
            ));
            $db->execute();

            $num_view_override_rows = $info["num_view_override_rows"];
            $order = 1;
            for ($i = 1; $i <= $num_view_override_rows; $i++) {
                if (!isset($info["view_override_field_{$i}"]) || empty($info["view_override_field_{$i}"]) ||
                    !isset($info["view_override_values_{$i}"]) || empty($info["view_override_values_{$i}"]) ||
                    !isset($info["view_override_view_{$i}"]) || empty($info["view_override_view_{$i}"])) {
                    continue;
                }

                $db->query("
                    INSERT INTO {PREFIX}module_submission_accounts_view_override (form_id, field_id, match_values, view_id, process_order)
                    VALUES (:form_id, :view_override_field_id, :view_override_values, :view_override_view_id, :process_order)
                ");
                $db->bindAll(array(
                    "form_id" => $form_id,
                    "view_override_field_id" => $info["view_override_field_{$i}"],
                    "view_override_values" => $info["view_override_values_{$i}"],
                    "view_override_view_id" => $info["view_override_view_{$i}"],
                    "process_order" => $order
                ));
                $db->execute();

                $order++;
            }

            // finally, add a couple of default menu items
            $db->query("
                INSERT INTO {PREFIX}module_submission_accounts_menus (form_id, display_text, url, page_identifier, is_submenu, list_order)
                VALUES (:form_id, 'Edit Submission', '/modules/submission_accounts/users/index.php', 'edit_submission', 'no', 1)
            ");
            $db->bind("form_id", $form_id);
            $db->execute();

            $db->query("
                INSERT INTO {PREFIX}module_submission_accounts_menus (form_id, display_text, url, page_identifier, is_submenu, list_order)
                VALUES (:form_id, 'Logout', '/modules/submission_accounts/logout.php', 'logout', 'no', 2)
            ");
            $db->bind("form_id", $form_id);
            $db->execute();

        } catch (Exception $e) {
            return array(false, $L["notify_error_configuring_form"]);
        }

        return array(true, $L["notify_form_configured"]);
    }


    /**
     * Returns a list of all forms which have submission accounts configured. This is really basic; for
     * the initial release, I'm not going to bother with separate pages, sorting, searching, etc. That can
     * be added later. The assumption being, most users won't be using this module for more than a handful
     * of forms, so they can all appear on the same page.
     *
     * @return array a hash of submission account configurations
     */
    public static function getSubmissionAccounts($params = array())
    {
        $db = Core::$db;

        $params["include_view_overrides"] = (isset($params["include_view_overrides"])) ? $params["include_view_overrides"] : false;

        $db->query("
            SELECT *, msa.is_active as submission_account_is_active
            FROM   {PREFIX}module_submission_accounts msa, {PREFIX}forms f
            WHERE  f.form_id = msa.form_id
            ORDER BY f.form_name
        ");
        $db->execute();
        $rows = $db->fetchAll();

        $results = array();
        foreach ($rows as $row) {
            if ($params["include_view_overrides"]) {
                $form_id = $row["form_id"];
                $row["view_overrides"] = self::getViewOverrides($form_id);
            }
            $results[] = $row;
        }

        return $results;
    }


    /**
     * Returns everything about a submission account.
     */
    public static function getSubmissionAccount($form_id)
    {
        $db = Core::$db;

        if (!is_numeric($form_id)) {
            return array();
        }

        $db->query("
            SELECT *, msa.is_active as submission_account_is_active
            FROM   {PREFIX}module_submission_accounts msa, {PREFIX}forms f
            WHERE  f.form_id = msa.form_id AND
                   f.form_id = :form_id
        ");
        $db->bind("form_id", $form_id);
        $db->execute();

        $info = $db->fetch();
        $info["menu_items"] = self::getFormMenu($form_id);
        $info["view_overrides"] = self::getViewOverrides($form_id);

        return $info;
    }


    public static function getViewOverrides($form_id)
    {
        $db = Core::$db;

        $db->query("
            SELECT *
            FROM   {PREFIX}module_submission_accounts_view_override
            WHERE  form_id = :form_id
            ORDER BY process_order
        ");
        $db->bind("form_id", $form_id);
        $db->execute();

        return $db->fetchAll();
    }


    /**
     * Returns the menu for a particular form.
     *
     * @param integer $form_id
     */
    public static function getFormMenu($form_id)
    {
        $db = Core::$db;

        $db->query("
            SELECT *
            FROM   {PREFIX}module_submission_accounts_menus
            WHERE  form_id = :form_id
            ORDER BY list_order
        ");
        $db->bind("form_id", $form_id);
        $db->execute();

        return $db->fetchAll();
    }


    /**
     * This function returns a string of JS containing the list of forms and fom Views in the page_ns
     * namespace.
     *
     * Its tightly coupled with the calling page, which is kind of crumby; but it can be refactored later
     * as the need arises.
     */
    public static function getFormViewMappingJs()
    {
        $forms = Forms::getForms();

        $js_rows = array();
        $js_rows[] = "var page_ns = {}";
        $js_rows[] = "page_ns.forms = []";
        $views_js_rows = array("page_ns.form_views = []");

        // convert ALL form and View info into Javascript, for use in the page
        foreach ($forms as $form_info) {

            // ignore those forms that aren't set up
            if ($form_info["is_complete"] == "no") {
                continue;
            }

            $form_id = $form_info["form_id"];
            $form_name = htmlspecialchars($form_info["form_name"]);
            $js_rows[] = "page_ns.forms.push([$form_id, \"$form_name\"])";

            $form_views = Views::getViews($form_id);

            $v = array();
            foreach ($form_views["results"] as $form_view) {
                $view_id = $form_view["view_id"];
                $view_name = htmlspecialchars($form_view["view_name"]);
                $v[] = "[$view_id, \"$view_name\"]";
            }
            $views = join(",", $v);

            $views_js_rows[] = "page_ns.form_views.push([$form_id,[$views]])";
        }

        $js = array_merge($js_rows, $views_js_rows);
        $js = join(";\n", $js);

        return $js;
    }


    /**
     * Updates the submission account. This function is called for all three of the tabs.
     *
     * @param integer $form_id
     * @param array $info
     */
    public static function updateSubmissionAccount($form_id, $info, $L)
    {
        $db = Core::$db;

        $tab = $info["tab"];

        $success = "";
        $message = "";

        switch ($tab) {
            case "main":
                $view_id = $info["view_id"];
                $theme = $info["theme"];
                $swatch = "";
                if (isset($info["{$theme}_theme_swatches"])) {
                    $swatch = $info["{$theme}_theme_swatches"];
                }

                $db->query("
                    UPDATE {PREFIX}module_submission_accounts
                    SET    view_id = :view_id,
                           theme = :theme,
                           swatch = :swatch,
                           is_active = :is_active,
                           email_field_id = :email_field_id,
                           username_field_id = :username_field_id,
                           password_field_id = :password_field_id
                    WHERE  form_id = :form_id
                ");
                $db->bindAll(array(
                    "view_id" => $view_id,
                    "theme" => $theme,
                    "swatch" => $swatch,
                    "is_active" => $info["is_active"],
                    "email_field_id" => (!empty($info["email_field_id"])) ? $info["email_field_id"] : null,
                    "username_field_id" => (!empty($info["username_field_id"])) ? $info["username_field_id"] : null,
                    "password_field_id" => (!empty($info["password_field_id"])) ? $info["password_field_id"] : null,
                    "form_id" => $form_id
                ));
                $db->execute();

                $db->query("DELETE FROM {PREFIX}module_submission_accounts_view_override WHERE form_id = :form_id");
                $db->bind("form_id", $form_id);
                $db->execute();

                $num_view_override_rows = $info["num_view_override_rows"];
                $order = 1;

                for ($i = 1; $i <= $num_view_override_rows; $i++) {
                    if (!isset($info["view_override_field_{$i}"]) || empty($info["view_override_field_{$i}"]) ||
                        !isset($info["view_override_values_{$i}"]) || empty($info["view_override_values_{$i}"]) ||
                        !isset($info["view_override_view_{$i}"]) || empty($info["view_override_view_{$i}"])) {
                        continue;
                    }

                    $db->query("
                        INSERT INTO {PREFIX}module_submission_accounts_view_override (form_id, field_id, match_values, view_id, process_order)
                        VALUES (:form_id, :field_id, :match_values, :view_id, :process_order)
                    ");
                    $db->bindAll(array(
                        "form_id" => $form_id,
                        "field_id" => $info["view_override_field_{$i}"],
                        "match_values" => $info["view_override_values_{$i}"],
                        "view_id" => $info["view_override_view_{$i}"],
                        "process_order" => $order
                    ));
                    $db->execute();

                    $order++;
                }

                $success = true;
                $message = $L["notify_submission_account_updated"];
                break;

            case "menu":
                $sortable_id = $info["sortable_id"];
                $sortable_rows = explode(",", $info["{$sortable_id}_sortable__rows"]);

                $menu_items = array();
                foreach ($sortable_rows as $i) {
                    // if this row doesn't have a page identifier, just ignore the row altogether
                    if (!isset($info["page_identifier_$i"]) || empty($info["page_identifier_$i"])) {
                        continue;
                    }

                    $page_identifier = $info["page_identifier_$i"];
                    $custom_options = isset($info["custom_options_$i"]) ? $info["custom_options_$i"] : "";

                    $url = self::constructPageUrl($page_identifier, $custom_options);
                    $menu_items[] = array(
                        "url" => $url,
                        "page_identifier" => $page_identifier,
                        "display_text" => $info["display_text_$i"],
                        "is_submenu" => isset($info["submenu_$i"]) ? "yes" : "no"
                    );
                }

                $db->query("DELETE FROM {PREFIX}module_submission_accounts_menus WHERE form_id = :form_id");
                $db->bind("form_id", $form_id);
                $db->execute();

                $order = 1;
                foreach ($menu_items as $menu_item_info) {
                    $db->query("
                        INSERT INTO {PREFIX}module_submission_accounts_menus
                            (form_id, display_text, page_identifier, url, is_submenu, list_order)
                        VALUES (:form_id, :display_text, :page_identifier, :url, :is_submenu, :list_order)
                    ");
                    $db->bindAll(array(
                        "form_id" => $form_id,
                        "display_text" => $menu_item_info["display_text"],
                        "page_identifier" => $menu_item_info["page_identifier"],
                        "url" => $menu_item_info["url"],
                        "is_submenu" => $menu_item_info["is_submenu"],
                        "list_order" => $order
                    ));
                    $db->execute();
                    $order++;
                }

                $success = true;
                $message = $L["notify_menu_updated"];
                break;

            case "users":
                break;
        }

        return array($success, $message);
    }


    /**
     * Constructs a URL for a menu item in the submission account module.
     *
     * @param string $page_identifier
     * @param string $custom_options
     */
    public static function constructPageUrl($page_identifier, $custom_options = "")
    {
        // ack! Magic! For some reason that I can't currently fathom, when I place these as top level globals
        // at the top of the page, they can't be accessed in this function - or the calling function. I'm stumped.
        $g_submission_account_pages = array(
            "edit_submission" => "/modules/submission_accounts/users/index.php",
            "logout" => "/modules/submission_accounts/logout.php"
        );

        if ($page_identifier == "custom_url") {
            $url = $custom_options;
        } else {
            if (preg_match("/^page_(\d+)/", $page_identifier, $matches)) {
                $page_id = $matches[1];
                $url = "/modules/pages/page.php?id=$page_id";
            } else {
                $url = $g_submission_account_pages["$page_identifier"];
            }
        }

        return $url;
    }


    /**
     * This returns a little information about those users who've logged in. Namely: their username, submission ID
     * and (if it's defined) email field, submission date, last modified date. This info is displayed in the admin's
     * UI to give them an idea of whose been logging in.
     *
     * Since when someone deletes a submission, this modules data table isn't updates, this function does the job of
     * removing now non-existent records.
     *
     * @param integer $form_id
     * @param integer $page
     * @return array
     */
    public static function getSubmissionAccountData($form_id, $page = 1, $username_col, $L)
    {
        $db = Core::$db;

        $module_settings = Modules::getModuleSettings("", "submission_accounts");
        $per_page = $module_settings["num_logged_in_users_per_page"];

        // first, remove those submission ID rows where the original submission no longer exists
        $db->query("
            SELECT submission_id
            FROM   {PREFIX}module_submission_accounts_data ms
            WHERE NOT EXISTS (
                SELECT submission_id
                FROM   {PREFIX}form_{$form_id} f
                WHERE  f.submission_id = ms.submission_id
            )
        ");
        $db->execute();
        $submission_ids = $db->fetchAll(PDO::FETCH_COLUMN);

        foreach ($submission_ids as $submission_id) {
            $db->query("DELETE FROM {PREFIX}module_submission_accounts_data WHERE submission_id = :submission_id");
            $db->bind("submission_id", $submission_id);
            $db->execute();
        }

        // determine the LIMIT clause
        $first_item = ($page - 1) * $per_page;
        $limit_clause = "LIMIT $first_item, $per_page";

        $db->query("
            SELECT ms.*, f.$username_col
            FROM   {PREFIX}module_submission_accounts_data ms, {PREFIX}form_{$form_id} f
            WHERE  ms.form_id = :form_id AND
                   ms.submission_id = f.submission_id
            ORDER BY last_logged_in DESC
            $limit_clause
        ");
        $db->bind("form_id", $form_id);
        $db->execute();
        $results = $db->fetchAll();

        $db->query("
            SELECT count(*)
            FROM   {PREFIX}module_submission_accounts_data
            WHERE  form_id = :form_id
        ");
        $db->bind("form_id", $form_id);
        $db->execute();

        return array(
            "results" => $results,
            "num_results" => $db->fetch(PDO::FETCH_COLUMN)
        );
    }


    /**
     * Called on the Settings page. This updates the general settings for the Submission Accounts module.
     *
     * @param array $info
     * @return array [0] T/F, [1] a message
     */
    public static function updateSettings($info, $L)
    {
        $settings = array(
            "login_form_heading" => $info["login_form_heading"],
            "login_form_welcome_text" => $info["login_form_welcome_text"],
            "username_field_label" => $info["username_field_label"],
            "password_field_label" => $info["password_field_label"],
            "login_button_label" => $info["login_button_label"],
            "logout_location" => $info["logout_location"],
            "logout_url" => $info["logout_url"],
            "num_logged_in_users_per_page" => $info["num_logged_in_users_per_page"]
        );

        Settings::set($settings, "submission_accounts");

        return array(true, $L["notify_settings_updated"]);
    }


    /**
     * Deletes everything about a submission account.
     */
    public static function deleteSubmissionAccount($form_id, $L)
    {
        $db = Core::$db;

        $db->query("DELETE FROM {PREFIX}module_submission_accounts_view_override WHERE form_id = :form_id");
        $db->bind("form_id", $form_id);
        $db->execute();

        $db->query("DELETE FROM {PREFIX}module_submission_accounts_menus WHERE form_id = :form_id");
        $db->bind("form_id", $form_id);
        $db->execute();

        $db->query("DELETE FROM {PREFIX}module_submission_accounts WHERE form_id = :form_id");
        $db->bind("form_id", $form_id);
        $db->execute();

        self::deleteSubmissionAccount($form_id, $L);
    }


    /**
     * Deletes all the info about logged in users in the database.
     *
     * @param integer $form_id
     * @return array [0] T/F [1] message
     */
    public static function deleteSubmissionAccountData($form_id, $L)
    {
        $db = Core::$db;

        $db->query("
            DELETE FROM {PREFIX}module_submission_accounts_data
            WHERE  form_id = :form_id
        ");
        $db->bind("form_id", $form_id);
        $db->execute();

        return array(true, $L["notify_submission_account_data_deleted"]);
    }
}
