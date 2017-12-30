<?php

require_once("../../global/library.php");

use FormTools\Administrator;
use FormTools\General;
use FormTools\Modules;
use FormTools\Settings;
use FormTools\Modules\SubmissionAccounts\Admin;
use FormTools\Modules\SubmissionAccounts\Users;

$module = Modules::initModulePage();

// for errors that prevent the usage of this page
$main_error = false;
$error = "";

$module_settings = $module->getSettings();
$L = $module->getLangStrings();

// get the default settings
$settings = Settings::get();
$g_theme = $settings["default_theme"];
$g_swatch = $settings["default_client_swatch"];

// now, if there's a form ID available (e.g. passed to the page via GET or POST), see if the form has been
// configured with submission accounts and if so, use the theme associated with the form
$form_id = Modules::loadModuleField("submission_accounts", "form_id", "form_id", "");

$submission_account = array();
if (!empty($form_id)) {
    $submission_account = Admin::getSubmissionAccount($form_id);

    if (isset($submission_account["form_id"]) && $submission_account["submission_account_is_active"] == "yes") {
        $g_theme = $submission_account["theme"];
        $g_swatch = $submission_account["swatch"];
    } else {
        if (isset($submission_account["submission_account_is_active"]) && $submission_account["submission_account_is_active"] == "no") {
            $main_error = true;
            $error = $L["notify_submission_account_inactive"];
        } else {
            $main_error = true;
            $error = $L["validation_login_invalid_form_id"];
        }
    }
} else {
    $main_error = true;
    $error = $L["notify_login_no_form_id"];
}

// if trying to send password
$success = true;
$message = "";
if (isset($_POST["send_password"])) {
    list($success, $message) = Users::sendPassword($form_id, $_POST, $L);
}

$admin_info = Administrator::getAdminInfo();
$admin_email = $admin_info["email"];

$replacements = array("site_admin_email" => "<a href=\"mailto:$admin_email\">$admin_email</a>");

$page_vars = array(
    "g_success" => $success,
    "g_message" => $message,
    "text_forgot_password" => General::evalSmartyString($L["text_forgot_password"], $replacements),
    "error" => $error,
    "submission_account" => $submission_account,
    "main_error" => $main_error, // an error SO BAD it prevents the login form from appearing
    "module_settings" => $module_settings
);

$page_vars["head_js"] = <<< END
var rules = [];
rules.push("required,email,{$L["validation_no_email"]}");
END;

$module->displayPage("templates/forget_password.tpl", $page_vars, $g_theme, $g_swatch);
