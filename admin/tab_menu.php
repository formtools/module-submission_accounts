<?php

if (isset($request["update"]))
{
  $request["tab"] = "menu";
  list($g_success, $g_message) = sa_update_submission_account($form_id, $request);
}

$submission_account = sa_get_submission_account($form_id);

$form_fields = ft_get_form_fields($form_id);

// ------------------------------------------------------------------------------------------------

$page_vars = array();
$page_vars["submission_account"] = $submission_account;
$page_vars["form_id"] = $form_id;
$page_vars["tabs"] = $tabs;
$page_vars["page"] = $page;
$page_vars["form_fields"] = $form_fields;
$page_vars["js_messages"] = array("phrase_please_select", "phrase_please_select_form", "word_na", "word_remove");
$page_vars["head_string"] = "<script type=\"text/javascript\" src=\"../global/scripts/manage_submission_account.js\"></script>
<script type=\"text/javascript\" src=\"../global/scripts/manage_user_menu.js\"></script>";

ft_display_module_page("templates/admin/edit.tpl", $page_vars);