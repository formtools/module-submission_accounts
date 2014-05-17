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

// get a list of all editable fields in the View. This is used both for security purposes
// for the update function and o determine whether the page contains any editable fields
$editable_field_ids = _ft_get_editable_view_fields($view_id);

// get the tabs for this View
$view_tabs = ft_get_view_tabs($view_id, true);


// handle POST requests
if (isset($_POST) && !empty($_POST))
{
  // add the view ID to the request hash, for use by the ft_update_submission function
  $request["view_id"] = $view_id;
  $request["editable_field_ids"] = $editable_field_ids;
  list($g_success, $g_message) = ft_update_submission($form_id, $submission_id, $request);

  // if required, remove a file or image
  $file_deleted = false;
  if (isset($_POST['delete_file_type']) && $_POST['delete_file_type'] == "file")
  {
    list($g_success, $g_message) = ft_delete_file_submission($form_id, $submission_id, $_POST['field_id']);
    $file_deleted = true;
  }
  else if (isset($_POST['delete_file_type']) && $_POST['delete_file_type'] == "image")
  {
    list($g_success, $g_message) = img_delete_image_file_submission($form_id, $submission_id, $_POST['field_id']);
    $file_deleted = true;
  }
  else if (isset($_POST['email_user']) && !empty($_POST['email_user']))
  {
    $g_success = ft_send_email("user", $form_id, $submission_id);
    if ($g_success)
      $g_message = $LANG["notify_email_sent_to_user"];
  }

  // if the View just changed, re-set the
  $new_view_id = sa_get_submission_view($form_id, $submission_id);
  if ($view_id != $new_view_id)
  {
    $_SESSION["ft"]["account"]["view_id"] = $new_view_id;
    header("location: index.php");
    exit;
  }
}


$form_info       = ft_get_form($form_id);
$view_info       = ft_get_view($view_id);
$submission_info = ft_get_submission($form_id, $submission_id, $view_id);

// get the subset of fields (and IDs) from $submission_info that appear on the current tab (or tab-less page)
$submission_tab_fields    = array();
$submission_tab_field_ids = array();
$wysiwyg_field_ids        = array();
$image_field_info         = array();

for ($i=0; $i<count($submission_info); $i++)
{
  // if this view has tabs, ignore those fields that aren't on the current tab.
  if (count($view_tabs) > 0 && (!isset($submission_info[$i]["tab_number"]) || $submission_info[$i]["tab_number"] != $tab_number))
    continue;

  $curr_field_id = $submission_info[$i]["field_id"];

  if ($submission_info[$i]["field_type"] == "wysiwyg")
    $wysiwyg_field_ids[] = "field_{$curr_field_id}_wysiwyg";

  // if this is an image field, keep track of its extended image settings. These are passed to the image rendering Smarty
  // plugin function to let it know how to display it
  if ($submission_info[$i]["field_type"] == "image")
    $image_field_info[$curr_field_id] = ft_get_extended_field_settings($curr_field_id, "image_manager");

  $submission_tab_field_ids[] = $curr_field_id;
  $submission_tab_fields[]    = $submission_info[$i];
}

$wysiwyg_field_id_list = join(",", $wysiwyg_field_ids);

// get a list of editable fields on this tab
$editable_tab_fields = array_intersect($submission_tab_field_ids, $editable_field_ids);

$tabs = array();
while (list($key, $value) = each($view_tabs))
{
  $tabs[$key] = array(
    "tab_label" => $value["tab_label"],
    "tab_link" => "{$_SERVER["PHP_SELF"]}?tab=$key"
    );
}

$image_manager_enabled = ft_check_module_enabled("image_manager");

// ------------------------------------------------------------------------------------------------

// compile the header information
$page_vars = array();
$page_vars["page"]   = "client_edit_submission";
$page_vars["page_url"] = ft_get_page_url("client_edit_submission");
$page_vars["tabs"] = $tabs;
$page_vars["submission_id"] = $submission_id;
$page_vars["submission_info"] = $submission_info;
$page_vars["tab_has_editable_fields"] = count($editable_tab_fields) > 0;
$page_vars["view_info"] = $view_info;
$page_vars["image_field_info"] = $image_field_info;
$page_vars["form_id"] = $form_id;
$page_vars["form_info"] = $form_info;
$page_vars["view_id"] = $view_id;
$page_vars["submission_tab_fields"] = $submission_tab_fields;
$page_vars["submission_tab_field_id_str"] = join(",", $submission_tab_field_ids);
$page_vars["tab_number"] = $tab_number;
$page_vars["head_title"] = "{$LANG['phrase_edit_submission']} - $submission_id";
$page_vars["head_string"] = "<script type=\"text/javascript\" src=\"$g_root_url/global/tiny_mce/tiny_mce.js\"></script>
  <script type=\"text/javascript\" src=\"$g_root_url/global/scripts/wysiwyg_settings.js\"></script>
  <script type=\"text/javascript\" src=\"$g_root_url/global/scripts/manage_submissions.js\"></script>
  <link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"{$g_root_url}/global/jscalendar/skins/aqua/theme.css\" title=\"Aqua\" />
  <script type=\"text/javascript\" src=\"{$g_root_url}/global/jscalendar/calendar.js\"></script>
  <script type=\"text/javascript\" src=\"{$g_root_url}/global/jscalendar/calendar-setup.js\"></script>
  <script type=\"text/javascript\" src=\"{$g_root_url}/global/jscalendar/lang/calendar-en.js\"></script>";

// if the image manager is enabled, there's a good chance the user wants to use the Lightbox. Include it!
if ($image_manager_enabled)
{
  $page_vars["head_string"] .= "
  <script type=\"text/javascript\" src=\"{$g_root_url}/global/scripts/lightbox.js\"></script>
  <link rel=\"stylesheet\" href=\"$g_root_url/global/css/lightbox.css\" type=\"text/css\" media=\"screen\" />";
}

$tiny_resize = ($_SESSION["ft"]["settings"]["tinymce_resize"] == "yes") ? "true" : "false";
$content_css = "$g_root_url/global/css/tinymce.css";

  $page_vars["head_js"] = "

// load up any WYWISYG editors in the page
g_content_css = \"$content_css\";
editors[\"{$_SESSION["ft"]["settings"]["tinymce_toolbar"]}\"].elements = \"$wysiwyg_field_id_list\";
editors[\"{$_SESSION["ft"]["settings"]["tinymce_toolbar"]}\"].theme_advanced_toolbar_location = \"{$_SESSION["ft"]["settings"]["tinymce_toolbar_location"]}\";
editors[\"{$_SESSION["ft"]["settings"]["tinymce_toolbar"]}\"].theme_advanced_toolbar_align = \"{$_SESSION["ft"]["settings"]["tinymce_toolbar_align"]}\";
editors[\"{$_SESSION["ft"]["settings"]["tinymce_toolbar"]}\"].theme_advanced_path_location = \"{$_SESSION["ft"]["settings"]["tinymce_path_info_location"]}\";
editors[\"{$_SESSION["ft"]["settings"]["tinymce_toolbar"]}\"].theme_advanced_resizing = $tiny_resize;
editors[\"{$_SESSION["ft"]["settings"]["tinymce_toolbar"]}\"].content_css = \"$content_css\";
tinyMCE.init(editors[\"{$_SESSION["ft"]["settings"]["tinymce_toolbar"]}\"]);

var page_ns = {};

/**
 * file_type can be \"image\" or \"file\".
 */
page_ns.delete_submission_file = function(file_type, field_id)
{
  f = document.edit_submission_form;
  f.field_id.value = field_id;
  f.delete_file_type.value = file_type;
  f.submit();
}
";

ft_display_module_page("templates/users/index.tpl", $page_vars);