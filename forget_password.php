<?php

require_once("../../global/library.php");
session_start();
header("Cache-control: private");
header("Content-Type: text/html; charset=utf-8");

$request = array_merge($_POST, $_GET);
require_once("library.php");

// for errors that prevent the usage of this page
$main_error = false;
$error = "";

$module_settings = ft_get_module_settings("", "submission_accounts");
$L = ft_get_module_lang_file_contents("submission_accounts");

// get the default settings
$settings = ft_get_settings();
$g_theme  = $settings["default_theme"];
$g_swatch = $settings["default_client_swatch"];

// now, if there's a form ID available (e.g. passed to the page via GET or POST), see if the form has been
// configured with submission accounts and if so, use the theme associated with the form
$form_id = ft_load_module_field("submission_accounts", "form_id", "form_id", "");
$submission_account = array();
if (!empty($form_id))
{
  $submission_account = sa_get_submission_account($form_id);
  if (isset($submission_account["form_id"]) && $submission_account["submission_account_is_active"] == "yes")
  {
    $g_theme = $submission_account["theme"];
    $g_swatch = $submission_account["swatch"];
  }
  else if (isset($submission_account["submission_account_is_active"]) && $submission_account["submission_account_is_active"] == "no")
  {
    $main_error = true;
    $error = $L["notify_submission_account_inactive"];
  }
  else
  {
    $main_error = true;
    $error = $L["validation_login_invalid_form_id"];
  }
}
else
{
  $main_error = true;
  $error = $L["notify_login_no_form_id"];
}

// if trying to send password
if (isset($_POST["send_password"]))
  list($g_success, $g_message) = sa_send_password($form_id, $_POST);

$admin_info = ft_get_admin_info();
$admin_email = $admin_info["email"];

$replacements = array("site_admin_email" => "<a href=\"mailto:$admin_email\">$admin_email</a>");

// ------------------------------------------------------------------------------------------------

$page_vars = array();
$page_vars["text_forgot_password"] = ft_eval_smarty_string($L["text_forgot_password"], $replacements);
$page_vars["error"] = $error;
$page_vars["submission_account"] = $submission_account;
$page_vars["main_error"] = $main_error; // an error SO BAD it prevents the login form from appearing
$page_vars["module_settings"] = $module_settings;
$page_vars["head_js"] =<<< END
var rules = [];
rules.push("required,email,{$L["validation_no_email"]}");
END;

ft_display_module_page("templates/forget_password.tpl", $page_vars, $g_theme, $g_swatch);