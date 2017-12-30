<?php

use FormTools\Fields;
use FormTools\Modules\SubmissionAccounts\Admin;

$sortable_id = "edit_sa_menu";

$success = true;
$message = "";
if (isset($request["update"])) {
    $request["tab"] = "menu";
    $request["sortable_id"] = $sortable_id;
    list($success, $message) = Admin::updateSubmissionAccount($form_id, $request, $L);
}

$submission_account = Admin::getSubmissionAccount($form_id);
$form_fields = Fields::getFormFields($form_id);

$page_vars = array(
    "g_success" => $success,
    "g_message" => $message,
    "submission_account" => $submission_account,
    "sortable_id" => $sortable_id,
    "form_id" => $form_id,
    "tabs" => $tabs,
    "page" => $page,
    "form_fields" => $form_fields,
    "js_messages" => array("phrase_please_select", "phrase_please_select_form", "word_na", "word_remove"),
    "js_files" => array(
        "$root_url/modules/submission_accounts/scripts/manage_user_menu.js",
        "$root_url/global/scripts/sortable.js"
    )
);

$page_vars["head_js"] = <<< END
$(function() {
  $(".col2 select").live("keyup change", function() {
    var list_order = parseInt($(this).closest(".row_group").find(".sr_order").val(), 10);
    sa_ns.change_page(list_order, this.value);
  });
});

END;

$module->displayPage("templates/admin/edit.tpl", $page_vars);
