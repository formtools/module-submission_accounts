<?php

require_once("../../global/library.php");
ft_init_module_page();
$folder = dirname(__FILE__);
require_once("$folder/library.php");
$request = array_merge($_POST, $_GET);

if (isset($request["add_form"]))
  list($g_success, $g_message) = sa_add_submission_account($request);

$submission_accounts = sa_get_submission_accounts();

// ------------------------------------------------------------------------------------------------

$page_vars = array();
$page_vars["submission_accounts"] = $submission_accounts;

ft_display_module_page("templates/index.tpl", $page_vars);