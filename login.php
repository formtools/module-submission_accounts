<?php

require_once("../../global/library.php");

use FormTools\Core;
use FormTools\Modules;
use FormTools\Settings;
use FormTools\Modules\SubmissionAccounts\Admin;
use FormTools\Modules\SubmissionAccounts\Users;

$module = Modules::initModulePage();
$module_settings = $module->getSettings();
$L = $module->getLangStrings();

$main_error = false;
$error = "";

// get the default settings
$settings = Settings::get();
$g_theme = $settings["default_theme"];
$g_swatch = $settings["default_client_swatch"];

// now, if there's a form ID available (e.g. passed to the page via GET or POST), see if the form has been
// configured with submission accounts and if so, use the theme & swatch associated with the form
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

$username = "";
if (isset($_POST["login"])) {
    $_POST["form_id"] = $form_id;
    $username = strip_tags($_POST["username"]);
    $error = Users::login($_POST, $L);
}

$page_vars = array(
    "error" => $error,
    "username" => $username,
    "submission_account" => $submission_account,
    "main_error" => $main_error, // an error SO BAD it prevents the login form from appearing
    "module_settings" => $module_settings
);

// Urgh. Should be refactored along with User Roles
Core::$user->setTheme($g_theme);
Core::$user->setSwatch($g_swatch);

$module->displayPage("templates/login.tpl", $page_vars, $g_theme, $g_swatch);
