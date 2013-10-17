<?php

require_once("../../../global/library.php");
session_start();
header("Cache-control: private");
header("Content-Type: text/html; charset=utf-8");
ft_check_permission("user");

require_once("../library.php");

// blur the GET and POST variables into a single variable for easy reference
$request = array_merge($_GET, $_POST);
$form_id       = $_SESSION["ft"]["account"]["form_id"];
$view_id       = $_SESSION["ft"]["account"]["view_id"];
$submission_id = $_SESSION["ft"]["account"]["submission_id"];

// store the form ID as the current form ID. This is used in a few places, including the delete file Ajax
// function
$_SESSION["ft"]["curr_form_id"] = $form_id;

$tab_number = ft_load_module_field("submission_accounts", "tab", "view_{$view_id}_current_tab", 1);

// store this submission ID
$_SESSION["ft"]["last_submission_id"] = $submission_id;

$tab_number = ft_load_field("tab", "view_{$view_id}_current_tab", 1);
$grouped_views = ft_get_grouped_views($form_id, array("omit_hidden_views" => true, "omit_empty_groups" => true));

// get a list of all editable fields in the View. This is used both for security purposes
// for the update function and to determine whether the page contains any editable fields
$editable_field_ids = _ft_get_editable_view_fields($view_id);

// get the tabs for this View
$view_tabs = ft_get_view_tabs($view_id, true);

// handle POST requests
if (isset($_POST) && !empty($_POST))
{
  // add the view ID to the request hash, for use by the ft_update_submission function
  $request["view_id"] = $view_id;
  $request["editable_field_ids"] = $editable_field_ids;
  $request["context"] = "submission_accounts";
  list($g_success, $g_message) = ft_update_submission($form_id, $submission_id, $request);

  // required. The reason being, this setting determines whether the submission IDs in the current form-view-search
  // are cached. Any time the data changes, the submission may then belong to different Views, so we need to re-cache it
  $_SESSION["ft"]["new_search"] = "yes";
}

$form_info      = ft_get_form($form_id);
$view_info      = ft_get_view($view_id);
$grouped_fields = ft_get_grouped_view_fields($view_id, $tab_number, $form_id, $submission_id);

$page_field_ids      = array();
$page_field_type_ids = array();
foreach ($grouped_fields as $group)
{
  foreach ($group["fields"] as $field_info)
  {
    $page_field_ids[] = $field_info["field_id"];
    if (!in_array($field_info["field_type_id"], $page_field_type_ids))
      $page_field_type_ids[] = $field_info["field_type_id"];
  }
}
$page_field_types = ft_get_field_types(true, $page_field_type_ids);

// construct the tab list
$view_tabs = ft_get_view_tabs($view_id, true);
$tabs      = array();
$same_page = ft_get_clean_php_self();
while (list($key, $value) = each($view_tabs))
{
  $tabs[$key] = array(
    "tab_label" => $value["tab_label"],
    "tab_link"  => "{$same_page}?tab=$key&form_id=$form_id&submission_id={$submission_id}"
  );
}

// get a list of editable fields on this tab
$editable_tab_fields = array_intersect($page_field_ids, $editable_field_ids);

// construct the page label
$submission_placeholders = ft_get_submission_placeholders($form_id, $submission_id);
$edit_submission_page_label = ft_eval_smarty_string($form_info["edit_submission_page_label"], $submission_placeholders);

// get all the shared resources
$settings = ft_get_settings("", "core");
$shared_resources_list = $settings["edit_submission_onload_resources"];
$shared_resources_array = explode("|", $shared_resources_list);
$shared_resources = "";
foreach ($shared_resources_array as $resource)
{
  $shared_resources .= ft_eval_smarty_string($resource, array("g_root_url" => $g_root_url)) . "\n";
}

$validation_js = ft_generate_submission_js_validation($grouped_fields);

// ------------------------------------------------------------------------------------------------

// compile the header information
$page_vars = array();
$page_vars["page"]   = "client_edit_submission";
$page_vars["page_url"] = ft_get_page_url("client_edit_submission");
$page_vars["tabs"] = $tabs;
$page_vars["settings"] = $settings;
$page_vars["grouped_views"] = $grouped_views;
$page_vars["tab_number"] = $tab_number;
$page_vars["page_field_ids"] = $page_field_ids;
$page_vars["grouped_fields"] = $grouped_fields;
$page_vars["field_types"] = $page_field_types;
$page_vars["head_title"] = $edit_submission_page_label;
$page_vars["submission_id"] = $submission_id;
$page_vars["settings"] = $settings;
$page_vars["tab_has_editable_fields"] = count($editable_tab_fields) > 0;
$page_vars["view_info"] = $view_info;
$page_vars["form_id"] = $form_id;
$page_vars["view_id"] = $view_id;
$page_vars["edit_submission_page_label"] = $edit_submission_page_label;
$page_vars["page_field_ids"] = $page_field_ids;
$page_vars["page_field_ids_str"] = implode(",", $page_field_ids);
$page_vars["js_messages"] = array("confirm_delete_submission", "notify_no_email_template_selected", "confirm_delete_submission_file",
  "phrase_please_confirm", "word_no", "word_yes", "phrase_validation_error", "word_close");
$page_vars["head_string"] =<<< EOF
  <script type="text/javascript" src="$g_root_url/global/scripts/manage_submissions.js"></script>
  <script type="text/javascript" src="$g_root_url/global/scripts/field_types.php"></script>
  <link rel="stylesheet" href="$g_root_url/global/css/field_types.php" type="text/css" />
$shared_resources
EOF;
$page_vars["head_js"] =<<< END
$validation_js
END;

ft_display_module_page("templates/users/index.tpl", $page_vars);
