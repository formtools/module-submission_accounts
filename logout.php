<?php

session_start();
header("Cache-control: private");

require("../../global/library.php");

$form_id_str = "";
if (isset($_SESSION["ft"]["account"]["form_id"]))
  $form_id_str = "?form_tools_form_id=" . $_SESSION["ft"]["account"]["form_id"];

$module_settings = ft_get_module_settings("", "submission_accounts");
$logout_url = $module_settings["logout_url"];

// empty sessions
$_SESSION["ft"] = array();

// redirect to login page
header("location: $logout_url{$form_id_str}");