<?php

session_start();
header("Cache-control: private");
require("../../global/library.php");

$form_id = "";
if (isset($_SESSION["ft"]["account"]["form_id"]))
  $form_id = $_SESSION["ft"]["account"]["form_id"];

$module_settings = ft_get_module_settings("", "submission_accounts");
$logout_location = $module_settings["logout_location"];
if ($logout_location == "custom_url" || empty($logout_location))
{
  $logout_url = $module_settings["logout_url"];
}
else
{
  $logout_url = "$g_root_url/modules/submission_accounts/login.php";
  if (!empty($form_id))
    $logout_url .= "?form_id=" . $form_id;
}

// empty sessions
$_SESSION["ft"] = array();

// redirect to login page
header("location: $logout_url");