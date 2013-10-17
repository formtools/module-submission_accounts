<?php

require_once("../../../global/library.php");
ft_init_module_page();
require_once("../library.php");

// get a list of forms that already have a submission account configured. These are omitted from the
// list of available forms
$submission_accounts = sa_get_submission_accounts();
$omit_forms = array();
foreach ($submission_accounts as $configured_form)
  $omit_forms[] = $configured_form["form_id"];

$js = sa_get_form_view_mapping_js();

// ------------------------------------------------------------------------------------------------

$page_vars = array();
$page_vars["omit_forms"] = $omit_forms;
$page_vars["js_messages"] = array("phrase_please_select", "phrase_please_select_form", "word_delete");
$page_vars["head_string"] = "<script src=\"../global/scripts/manage_submission_account.js?v=2\"></script>";
$page_vars["head_js"] =<<< EOF
$js

var rules = [];
rules.push("required,form_id,{$L["validation_no_form_id"]}");
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

$(sa_ns.init_configure_form_page);
EOF;

ft_display_module_page("templates/admin/add.tpl", $page_vars);
