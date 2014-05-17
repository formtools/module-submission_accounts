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

$submission_accounts = sa_get_submission_accounts(array("include_view_overrides" => true));

// ------------------------------------------------------------------------------------------------

$page_vars = array();
$page_vars["js_messages"] = array("word_edit");
$page_vars["submission_accounts"] = $submission_accounts;
$page_vars["head_js"] =<<<EOF

var page_ns = {};
page_ns.delete_form = function(form_id) {
  ft.create_dialog({
    title:      "{$LANG["phrase_please_confirm"]}",
    content:    "{$L["confirm_delete_form"]}",
    popup_type: "warning",
    buttons: [{
      text:  "{$LANG["word_yes"]}",
      click: function() {
        window.location = "index.php?delete=" + form_id;
      }
    },
    {
      text:  "{$LANG["word_no"]}",
      click: function() {
        $(this).dialog("close");
      }
    }]
  });

  return false;
}
EOF;

ft_display_module_page("templates/index.tpl", $page_vars);
