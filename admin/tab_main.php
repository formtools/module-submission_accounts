<?php

use FormTools\Core;
use FormTools\Fields;
use FormTools\Modules\SubmissionAccounts\Admin;

$root_url = Core::getRootUrl();

$success = true;
$message = "";
if (isset($request["update"])) {
    $request["tab"] = "main";
    list ($success, $message) = Admin::updateSubmissionAccount($form_id, $request, $L);
}

// get a list of forms that already have a submission account configured. These are omitted from the
// list of available forms
$submission_accounts = Admin::getSubmissionAccounts();
$omit_forms = array();
foreach ($submission_accounts as $configured_form) {
    if ($configured_form["form_id"] != $form_id) {
        $omit_forms[] = $configured_form["form_id"];
    }
}

$js = Admin::getFormViewMappingJs();
$submission_account = Admin::getSubmissionAccount($form_id);
$form_fields = Fields::getFormFields($form_id);

$page_vars = array(
    "g_success" => $success,
    "g_message" => $message,
    "submission_account" => $submission_account,
    "omit_forms" => $omit_forms,
    "form_id" => $form_id,
    "tabs" => $tabs,
    "page" => $page,
    "form_fields" => $form_fields,
    "js_messages" => array(
        "phrase_please_select", "phrase_please_select_form", "word_delete"
    ),
    "js_files" => array(
        "{$root_url}/modules/submission_accounts/scripts/manage_submission_account.js"
    )
);

$page_vars["head_js"] = <<< END
$js

var rules = [];
rules.push("required,view_id,{$L["validation_no_view_id"]}");
rules.push("required,theme,{$LANG["validation_no_theme"]}");
rules.push("function,validate_swatch");
rules.push("required,username_field_id,{$L["validation_no_username_field"]}");
rules.push("required,password_field_id,{$L["validation_no_password_field"]}");

function validate_swatch() {
  var theme     = $("#theme").val();
  var swatch_id = "#" + theme + "_theme_swatches";
  if ($(swatch_id).length > 0 && $(swatch_id).val() == "") {
    return [[$(swatch_id)[0], "{$LANG["validation_no_theme_swatch"]}"]];
  }
  return true;
}

if (typeof sa_ns == undefined) {
  sa_ns = {};
}
sa_ns.page_type = "edit";

$(sa_ns.init_configure_form_page);
END;

$module->displayPage("templates/admin/edit.tpl", $page_vars);
