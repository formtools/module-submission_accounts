<?php

use FormTools\Fields;
use FormTools\General;
use FormTools\Modules;
use FormTools\Modules\SubmissionAccounts\Admin;

$submission_account = Admin::getSubmissionAccount($form_id);
$module_settings = $module->getSettings();

if (isset($request["clear_results"]))
  list ($g_success, $g_message) = Admin::deleteSubmissionAccountData($form_id, $L);


// get the db column name which contains the username
$field_info = Fields::getFormField($submission_account["username_field_id"]);
$username_col = $field_info["col_name"];

$users_page = Modules::loadModuleField("submission_accounts", "users_page", "users_page", 1);
$account_data = Admin::getSubmissionAccountData($form_id, $users_page, $username_col);

$num_results = $account_data["num_results"];
$results     = $account_data["results"];

$page_vars = array(
    "submission_account" => $submission_account,
    "form_id" => $form_id,
    "tabs" => $tabs,
    "page" => $page,
    "pagination" => General::getPageNav($num_results, $module_settings["num_logged_in_users_per_page"], $users_page, "", "users_page"),
    "results" => $results,
    "num_results" => $num_results,
    "username_col" => $username_col,
    "module_settings" => $module_settings,
    "js_messages" => array(
        "phrase_please_select", "phrase_please_select_form"
    )
);

$module->displayPage("templates/admin/edit.tpl", $page_vars);
