<?php

if (isset($request["update"]))
{
  $request["tab"] = "main";
  list ($g_success, $g_message) = sa_update_submission_account($form_id, $request);
}

// get a list of forms that already have a submission account configured. These are omitted from the
// list of available forms
$submission_accounts = sa_get_submission_accounts();
$omit_forms = array();
foreach ($submission_accounts as $configured_form)
{
  if ($configured_form["form_id"] != $form_id)
    $omit_forms[] = $configured_form["form_id"];
}

$js = sa_get_form_view_mapping_js();
$submission_account = sa_get_submission_account($form_id);
$form_fields = ft_get_form_fields($form_id);

// ------------------------------------------------------------------------------------------------

$page_vars = array();
$page_vars["submission_account"] = $submission_account;
$page_vars["omit_forms"] = $omit_forms;
$page_vars["form_id"] = $form_id;
$page_vars["tabs"] = $tabs;
$page_vars["page"] = $page;
$page_vars["form_fields"] = $form_fields;
$page_vars["js_messages"] = array("phrase_please_select", "phrase_please_select_form", "word_delete");
$page_vars["head_string"] = "<script src=\"../global/scripts/manage_submission_account.js?v=3\"></script>";
$page_vars["head_js"] =<<< EOF
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
EOF;

ft_display_module_page("templates/admin/edit.tpl", $page_vars);
