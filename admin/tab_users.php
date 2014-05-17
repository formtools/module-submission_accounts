<?php

$submission_account = sa_get_submission_account($form_id);
$module_settings = ft_get_module_settings("", "submission_accounts");

if (isset($request["clear_results"]))
  list ($g_success, $g_message) = sa_delete_submission_account_data($form_id);


// get the db column name which is acting as the username
$field_info = ft_get_form_field($submission_account["username_field_id"]);
$username_col = $field_info["col_name"];

$users_page = ft_load_module_field("submission_accounts", "users_page", "users_page", 1);
$account_data = sa_get_submission_account_data($form_id, $users_page, $username_col);

$num_results = $account_data["num_results"];
$results     = $account_data["results"];


// ------------------------------------------------------------------------------------------------

$page_vars = array();
$page_vars["submission_account"] = $submission_account;
$page_vars["form_id"] = $form_id;
$page_vars["tabs"] = $tabs;
$page_vars["page"] = $page;
$page_vars["pagination"] = ft_get_page_nav($num_results, $module_settings["num_logged_in_users_per_page"], $users_page, "", "users_page");
$page_vars["results"] = $results;
$page_vars["num_results"] = $num_results;
$page_vars["username_col"] = $username_col;
$page_vars["module_settings"] = $module_settings;
$page_vars["js_messages"] = array("phrase_please_select", "phrase_please_select_form");
$page_vars["head_string"] = "<script type=\"text/javascript\" src=\"../global/scripts/manage_submission_account.js\"></script>";

ft_display_module_page("templates/admin/edit.tpl", $page_vars);