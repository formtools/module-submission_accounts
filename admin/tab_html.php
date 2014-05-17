<?php

$submission_account = sa_get_submission_account($form_id);
$module_settings = ft_get_module_settings("", "submission_accounts");

// ------------------------------------------------------------------------------------------------

$page_vars = array();
$page_vars["module_settings"] = $module_settings;
$page_vars["submission_account"] = $submission_account;
$page_vars["form_id"] = $form_id;
$page_vars["tabs"] = $tabs;
$page_vars["page"] = $page;
$page_vars["head_string"] = "<script type=\"text/javascript\" src=\"../global/scripts/generate_custom_login_form.js\"></script>
  <script type=\"text/javascript\" src=\"$g_root_url/global/codemirror/js/codemirror.js\"></script>";

ft_display_module_page("templates/admin/edit.tpl", $page_vars);