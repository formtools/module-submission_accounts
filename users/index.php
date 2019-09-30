<?php

require_once("../../../global/library.php");

use FormTools\Core;
use FormTools\FieldTypes;
use FormTools\FieldValidation;
use FormTools\Forms;
use FormTools\General;
use FormTools\Modules;
use FormTools\Pages;
use FormTools\Sessions;
use FormTools\Settings;
use FormTools\Submissions;
use FormTools\Views;
use FormTools\ViewFields;
use FormTools\ViewTabs;

$module = Modules::initModulePage("user");

$root_url = Core::getRootUrl();

$form_id = Sessions::get("account.form_id");
$view_id = Sessions::get("account.view_id");
$submission_id = Sessions::get("account.submission_id");

// store the form ID as the current form ID. This is used in a few places, including the delete file Ajax function
Sessions::set("curr_form_id", $form_id);
Sessions::set("form_{$form_id}_view_id", $view_id);
$tab_number = Modules::loadModuleField("submission_accounts", "tab", "view_{$view_id}_current_tab", 1);

// store this submission ID
Sessions::set("last_submission_id", $submission_id);

$tab_number = General::loadField("tab", "view_{$view_id}_current_tab", 1);
$grouped_views = Views::getGroupedViews($form_id, array("omit_hidden_views" => true, "omit_empty_groups" => true));

// get a list of all editable fields in the View. This is used both for security purposes
// for the update function and to determine whether the page contains any editable fields
$editable_field_ids = ViewFields::getEditableViewFields($view_id);

// get the tabs for this View
$view_tabs = ViewTabs::getViewTabs($view_id, true);

// handle POST requests
$success = true;
$message = "";
if (isset($_POST) && !empty($_POST)) {
    // add the view ID to the request hash, for use by the ft_update_submission function
    $request["view_id"] = $view_id;
    $request["editable_field_ids"] = $editable_field_ids;
    $request["context"] = "submission_accounts";
    list($success, $message) = Submissions::updateSubmission($form_id, $submission_id, $request);

    // required. The reason being, this setting determines whether the submission IDs in the current form-view-search
    // are cached. Any time the data changes, the submission may then belong to different Views, so we need to re-cache it
    Sessions::set("new_search", "yes");
}

$form_info = Forms::getForm($form_id);
$view_info = Views::getView($view_id);
$grouped_fields = ViewFields::getGroupedViewFields($view_id, $tab_number, $form_id, $submission_id);

$page_field_ids = array();
$page_field_type_ids = array();
foreach ($grouped_fields as $group) {
    foreach ($group["fields"] as $field_info) {
        $page_field_ids[] = $field_info["field_id"];
        if (!in_array($field_info["field_type_id"], $page_field_type_ids)) {
            $page_field_type_ids[] = $field_info["field_type_id"];
        }
    }
}
$page_field_types = FieldTypes::get(true, $page_field_type_ids);

// construct the tab list
$view_tabs = ViewTabs::getViewTabs($view_id, true);
$tabs = array();
$same_page = General::getCleanPhpSelf();
while (list($key, $value) = each($view_tabs)) {
    $tabs[$key] = array(
        "tab_label" => $value["tab_label"],
        "tab_link" => "{$same_page}?tab=$key&form_id=$form_id&submission_id={$submission_id}"
    );
}

// get a list of editable fields on this tab
$editable_tab_fields = array_intersect($page_field_ids, $editable_field_ids);

// construct the page label
$submission_placeholders = General::getSubmissionPlaceholders($form_id, $submission_id, "submission_accounts"); // TODO check third param
$edit_submission_page_label = General::evalSmartyString($form_info["edit_submission_page_label"], $submission_placeholders);

// get all the shared resources
$settings = Settings::get();
$shared_resources_list = $settings["edit_submission_onload_resources"];
$shared_resources_array = explode("|", $shared_resources_list);
$shared_resources = "";
foreach ($shared_resources_array as $resource) {
    $shared_resources .= General::evalSmartyString($resource, array("g_root_url" => $root_url)) . "\n";
}

$validation_js = FieldValidation::generateSubmissionJsValidation($grouped_fields);

$page_vars = array(
    "g_success" => $success,
    "g_message" => $message,
    "page" => "client_edit_submission",
    "page_url" => Pages::getPageUrl("client_edit_submission"),
    "tabs" => $tabs,
    "settings" => $settings,
    "grouped_views" => $grouped_views,
    "tab_number" => $tab_number,
    "grouped_fields" => $grouped_fields,
    "field_types" => $page_field_types,
    "head_title" => $edit_submission_page_label,
    "submission_id" => $submission_id,
    "tab_has_editable_fields" => count($editable_tab_fields) > 0,
    "view_info" => $view_info,
    "form_id" => $form_id,
    "view_id" => $view_id,
    "edit_submission_page_label" => $edit_submission_page_label,
    "page_field_ids" => $page_field_ids,
    "page_field_ids_str" => implode(",", $page_field_ids),
    "js_messages" => array(
        "confirm_delete_submission",
        "notify_no_email_template_selected",
        "confirm_delete_submission_file",
        "phrase_please_confirm",
        "word_no",
        "word_yes",
        "phrase_validation_error",
        "word_close"
    ),
    "password_type_id" => FieldTypes::getFieldTypeIdByIdentifier("password")
);
$page_vars["head_string"] = <<< END
  <script type="text/javascript" src="$root_url/global/scripts/manage_submissions.js"></script>
  <script type="text/javascript" src="$root_url/global/scripts/field_types.php"></script>
  <link rel="stylesheet" href="$root_url/global/css/field_types.php" type="text/css" />
$shared_resources
END;

$page_vars["head_js"] = <<< END
$validation_js
END;

$module->displayPage("templates/users/index.tpl", $page_vars);
