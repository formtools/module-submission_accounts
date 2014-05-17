<?php

require_once("../../global/library.php");
ft_init_module_page();
$folder = dirname(__FILE__);
require_once("$folder/library.php");
$request = array_merge($_POST, $_GET);

if (isset($request["add_form"]))
  list($g_success, $g_message) = sa_add_submission_account($request);
else if (isset($request["delete"]))
  list($g_success, $g_message) = sa_delete_submission_account($request["delete"]);

$submission_accounts = sa_get_submission_accounts();

// ------------------------------------------------------------------------------------------------

$page_vars = array();
$page_vars["submission_accounts"] = $submission_accounts;
$page_vars["head_js"] =<<<EOF

var page_ns = {};
page_ns.delete_form = function(form_id)
{
    if (confirm("{$L["confirm_delete_form"]}"))
    {
        window.location = "index.php?delete=" + form_id;
    }

    return false;
}

EOF;

ft_display_module_page("templates/index.tpl", $page_vars);