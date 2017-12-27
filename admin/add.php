<?php

require_once("../../../global/library.php");

use FormTools\Core;
use FormTools\Modules;
use FormTools\Modules\SubmissionAccounts\Admin;

$module = Modules::initModulePage("admin");
$L = $module->getLangStrings();
$LANG = Core::$L;

// get a list of forms that already have a submission account configured. These are omitted from the
// list of available forms
$submission_accounts = Admin::getSubmissionAccounts();
$omit_forms = array();
foreach ($submission_accounts as $configured_form) {
    $omit_forms[] = $configured_form["form_id"];
}

$js = Admin::getFormViewMappingJs();

$page_vars = array(
    "omit_forms" => $omit_forms,
    "js_messages" => array("phrase_please_select", "phrase_please_select_form", "word_delete")
);

$page_vars["head_js"] = <<< END
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
END;

$module->displayPage("templates/admin/add.tpl", $page_vars);
