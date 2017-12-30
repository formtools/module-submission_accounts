<?php

require_once("../../global/library.php");

use FormTools\Core;
use FormTools\Modules;
use FormTools\Modules\SubmissionAccounts\Admin;

$module = Modules::initModulePage("admin");
$LANG = Core::$L;
$L = $module->getLangStrings();

$success = true;
$message = "";
if (isset($request["add_form"])) {
    list($success, $message) = Admin::addSubmissionAccount($request, $L);
} else {
    if (isset($request["delete"])) {
        list($success, $message) = sa_delete_submission_account($request["delete"]);
    }
}

$submission_accounts = Admin::getSubmissionAccounts(array("include_view_overrides" => true));

$page_vars = array(
    "g_success" => $success,
    "g_message" => $message,
    "js_messages" => array("word_edit"),
    "submission_accounts" => $submission_accounts
);

$page_vars["head_js"] =<<< END

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
END;

$module->displayPage("templates/index.tpl", $page_vars);
