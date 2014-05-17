<?php

/**
 * This file defines all functions for managing the submission accounts.
 *
 * @copyright Encore Web Studios 2009
 * @author Encore Web Studios <formtools@encorewebstudios.com>
 */


// -------------------------------------------------------------------------------------------------


/**
 * Adds a submission account to the database.
 *
 * @param array $info the POST contents
 */
function sa_add_submission_account($info)
{
  global $g_table_prefix, $L;

  $info = ft_sanitize($info);

  $form_id = $info["form_id"];
  $view_id = $info["view_id"];
  $theme   = $info["theme"];
  $email_field_id = (isset($info["email_field_id"]) && !empty($info["email_field_id"])) ? $info["email_field_id"] : "NULL";
  $username_field_id = $info["username_field_id"];
  $password_field_id = $info["password_field_id"];

  $result = mysql_query("
    INSERT INTO {$g_table_prefix}module_submission_accounts (form_id, view_id, theme, is_active,
      email_field_id, username_field_id, password_field_id)
    VALUES ($form_id, $view_id, '$theme', 'yes', $email_field_id, $username_field_id, $password_field_id)
      ");

  $num_view_override_rows = $info["num_view_override_rows"];
  $order = 1;
  for ($i=1; $i<=$num_view_override_rows; $i++)
  {
    if (!isset($info["view_override_field_{$i}"]) || empty($info["view_override_field_{$i}"]) ||
        !isset($info["view_override_values_{$i}"]) || empty($info["view_override_values_{$i}"]) ||
        !isset($info["view_override_view_{$i}"]) || empty($info["view_override_view_{$i}"]))
      continue;

    $view_override_field_id = $info["view_override_field_{$i}"];
    $view_override_values   = $info["view_override_values_{$i}"];
    $view_override_view_id  = $info["view_override_view_{$i}"];

    mysql_query("
      INSERT INTO {$g_table_prefix}module_submission_accounts_view_override (form_id, field_id, match_values, view_id, process_order)
      VALUES ($form_id, $view_override_field_id, '$view_override_values', $view_override_view_id, $order)
        ") or die(mysql_error());

    $order++;
  }

  // finally, add a couple of default menu items
  mysql_query("
    INSERT INTO {$g_table_prefix}module_submission_accounts_menus (form_id, display_text, url, page_identifier, is_submenu, list_order)
    VALUES ($form_id, 'Edit Submission', '/modules/submission_accounts/users/index.php', 'edit_submission', 'no', 1)
  ");
  mysql_query("
    INSERT INTO {$g_table_prefix}module_submission_accounts_menus (form_id, display_text, url, page_identifier, is_submenu, list_order)
    VALUES ($form_id, 'Logout', '/modules/submission_accounts/logout.php', 'logout', 'no', 2)
  ");

  if (!$result)
    return array(false, $L["notify_error_configuring_form"]);
  else
    return array(true, $L["notify_form_configured"]);
}


/**
 * Returns a list of all forms which have submission accounts configured. This is really basic; for
 * the initial release, I'm not going to bother with separate pages, sorting, searching, etc. That can
 * be added later. The assumption being, most users won't be using this module for more than a handful
 * of forms, so they can all appear on the same page.
 *
 * @return array a hash of submission account configurations
 */
function sa_get_submission_accounts()
{
  global $g_table_prefix;

  $query = mysql_query("
    SELECT *, msa.is_active as submission_account_is_active
    FROM   {$g_table_prefix}module_submission_accounts msa, {$g_table_prefix}forms f
    WHERE  f.form_id = msa.form_id
    ORDER BY f.form_name
      ");

  $results = array();
  while ($row = mysql_fetch_assoc($query))
    $results[] = $row;

  return $results;
}


/**
 * Returns everything about a submission account.
 */
function sa_get_submission_account($form_id)
{
  global $g_table_prefix;

  if (!is_numeric($form_id))
    return array();

  $query = mysql_query("
    SELECT *, msa.is_active as submission_account_is_active
    FROM   {$g_table_prefix}module_submission_accounts msa, {$g_table_prefix}forms f
    WHERE  f.form_id = msa.form_id AND
           f.form_id = $form_id
           ");

  $info = mysql_fetch_assoc($query);
  $info["menu_items"]     = sa_get_form_menu($form_id);
  $info["view_overrides"] = sa_get_view_overrides($form_id);

  return $info;
}


function sa_get_view_overrides($form_id)
{
  global $g_table_prefix;

  $query = mysql_query("
    SELECT *
    FROM   {$g_table_prefix}module_submission_accounts_view_override
    WHERE  form_id = $form_id
    ORDER BY process_order
      ");

  $rows = array();
  while ($row = mysql_fetch_assoc($query))
    $rows[] = $row;

  return $rows;
}


/**
 * Returns the menu for a particular form.
 *
 * @param integer $form_id
 */
function sa_get_form_menu($form_id)
{
  global $g_table_prefix;

  $menu_query = mysql_query("
    SELECT *
    FROM   {$g_table_prefix}module_submission_accounts_menus
    WHERE  form_id = $form_id
    ORDER BY list_order
      ");

  $menu_items = array();
  while ($row = mysql_fetch_assoc($menu_query))
    $menu_items[] = $row;

  return $menu_items;
}


/**
 * This function returns a string of JS containing the list of forms and fom Views in the page_ns
 * namespace.
 *
 * Its tightly coupled with the calling page, which is kind of crumby; but it can be refactored later
 * as the need arises.
 */
function sa_get_form_view_mapping_js()
{
  $forms = ft_get_forms();

  $js_rows = array();
  $js_rows[] = "var page_ns = {}";
  $js_rows[] = "page_ns.forms = []";
  $views_js_rows = array("page_ns.form_views = []");

  // convert ALL form and View info into Javascript, for use in the page
  foreach ($forms as $form_info)
  {
    // ignore those forms that aren't set up
    if ($form_info["is_complete"] == "no")
      continue;

    $form_id = $form_info["form_id"];
    $form_name = htmlspecialchars($form_info["form_name"]);
    $js_rows[] = "page_ns.forms.push([$form_id, \"$form_name\"])";

    $form_views = ft_get_views($form_id, "all");

    $v = array();
    foreach ($form_views["results"] as $form_view)
    {
      $view_id   = $form_view["view_id"];
      $view_name = htmlspecialchars($form_view["view_name"]);
      $v[] = "[$view_id, \"$view_name\"]";
    }
    $views = join(",", $v);

    $views_js_rows[] = "page_ns.form_views.push([$form_id,[$views]])";
  }

  $js = array_merge($js_rows, $views_js_rows);
  $js = join(";\n", $js);

  return $js;
}


/**
 * Updates the submission account. This function is called for all three of the tabs.
 *
 * @param integer $form_id
 * @param array $info
 */
function sa_update_submission_account($form_id, $info)
{
  global $g_table_prefix, $L;

  $tab = $info["tab"];

  $success = "";
  $message = "";

  switch ($tab)
  {
    case "main":
      $view_id   = $info["view_id"];
      $theme     = $info["theme"];
      $is_active = $info["is_active"];
      $email_field_id    = (!empty($info["email_field_id"])) ? $info["email_field_id"] : "NULL";
      $username_field_id = (!empty($info["username_field_id"])) ? $info["username_field_id"] : "NULL";
      $password_field_id = (!empty($info["password_field_id"])) ? $info["password_field_id"] : "NULL";

      $query = mysql_query("
        UPDATE {$g_table_prefix}module_submission_accounts
        SET    view_id = $view_id,
               theme = '$theme',
               is_active = '$is_active',
               email_field_id = $email_field_id,
               username_field_id = $username_field_id,
               password_field_id = $password_field_id
        WHERE  form_id = $form_id
          ") or die(mysql_error());

      mysql_query("DELETE FROM {$g_table_prefix}module_submission_accounts_view_override WHERE form_id = $form_id");

      $num_view_override_rows = $info["num_view_override_rows"];
      $order = 1;

      for ($i=1; $i<=$num_view_override_rows; $i++)
      {
        if (!isset($info["view_override_field_{$i}"]) || empty($info["view_override_field_{$i}"]) ||
            !isset($info["view_override_values_{$i}"]) || empty($info["view_override_values_{$i}"]) ||
            !isset($info["view_override_view_{$i}"]) || empty($info["view_override_view_{$i}"]))
          continue;

        $view_override_field_id = $info["view_override_field_{$i}"];
        $view_override_values   = $info["view_override_values_{$i}"];
        $view_override_view_id  = $info["view_override_view_{$i}"];

        mysql_query("
          INSERT INTO {$g_table_prefix}module_submission_accounts_view_override (form_id, field_id, match_values, view_id, process_order)
          VALUES ($form_id, $view_override_field_id, '$view_override_values', $view_override_view_id, $order)
            ") or die(mysql_error());

        $order++;
      }

      $success = true;
      $message = $L["notify_submission_account_updated"];
      break;

    case "menu":
      $info = ft_sanitize($info);

      $menu_items = array();
      for ($i=1; $i<=$info["num_rows"]; $i++)
      {
        // if this row doesn't have a page identifier, just ignore it
        if (!isset($info["page_identifier_$i"]) || empty($info["page_identifier_$i"]))
          continue;

        $page_identifier = $info["page_identifier_$i"];
        $display_text    = ft_sanitize($info["display_text_$i"]);
        $custom_options  = isset($info["custom_options_$i"]) ? ft_sanitize($info["custom_options_$i"]) : "";
        $is_submenu      = isset($info["submenu_$i"]) ? "yes" : "no";
        $list_order      = isset($info["menu_row_{$i}_order"]) ? $info["menu_row_{$i}_order"] : "";

        // construct the URL for this menu item
        $url = sa_construct_page_url($page_identifier, $custom_options);
        $menu_items[$list_order] = array(
          "url" => $url,
          "page_identifier" => $page_identifier,
          "display_text" => $display_text,
          "is_submenu" => $is_submenu
            );
      }

      ksort($menu_items);

      mysql_query("DELETE FROM {$g_table_prefix}module_submission_accounts_menus WHERE form_id = $form_id");

      $order = 1;
      foreach ($menu_items as $key => $hash)
      {
        $url             = $hash["url"];
        $page_identifier = $hash["page_identifier"];
        $display_text    = $hash["display_text"];
        $is_submenu      = $hash["is_submenu"];

        mysql_query("
          INSERT INTO {$g_table_prefix}module_submission_accounts_menus
             (form_id, display_text, page_identifier, url, is_submenu, list_order)
          VALUES ($form_id, '$display_text', '$page_identifier', '$url', '$is_submenu', $order)
            ") or die(mysql_error());
        $order++;
      }

      $success = true;
      $message = $L["notify_menu_updated"];
       break;

    case "users":
      break;
  }

  return array($success, $message);
}


/**
 * Constructs a URL for a menu item in the submission account module.
 *
 * @param string $page_identifier
 * @param string $custom_options
 */
function sa_construct_page_url($page_identifier, $custom_options = "")
{
  // ack! Magic! For some reason that I can't currently fathom, when I place these as top level globals
  // at the top of the page, they can't be accessed in this function - or the calling function. I'm stumped.
  $g_submission_account_pages = array(
    "edit_submission" => "/modules/submission_accounts/users/index.php",
    "logout"          => "/modules/submission_accounts/logout.php"
      );

  $url = "";
  switch ($page_identifier)
  {
    case "custom_url":
      $url = $custom_options;
      break;

    default:
      if (preg_match("/^page_(\d+)/", $page_identifier, $matches))
      {
        $page_id = $matches[1];
        $url = "/modules/pages/page.php?id=$page_id";
      }
      else
      {
        $url = $g_submission_account_pages["$page_identifier"];
      }
      break;
  }

  return $url;
}


/**
 * This returns a little information about those users who've logged in. Namely: their username, submission ID
 * and (if it's defined) email field, submission date, last modified date. This info is displayed in the admin's
 * UI to give them an idea of whose been logging in.
 *
 * Since when someone deletes a submission, this modules data table isn't updates, this function does the job of
 * removing now non-existent records.
 *
 * @param integer $form_id
 * @param integer $page
 * @return array
 */
function sa_get_submission_account_data($form_id, $page = 1, $username_col)
{
  global $g_table_prefix, $L;

  $module_settings = ft_get_module_settings("", "submission_accounts");
  $per_page = $module_settings["num_logged_in_users_per_page"];

  // first, remove those submission ID rows where the original submission no longer exists
  $deleted_submission_query = mysql_query("
    SELECT submission_id
    FROM   {$g_table_prefix}module_submission_accounts_data ms
    WHERE NOT EXISTS (
      SELECT submission_id
      FROM   {$g_table_prefix}form_{$form_id} f
      WHERE  f.submission_id = ms.submission_id
        )
      ");

  while ($row = mysql_fetch_assoc($deleted_submission_query))
  {
    $submission_id = $row["submission_id"];
    mysql_query("DELETE FROM {$g_table_prefix}module_submission_accounts_data WHERE submission_id = $submission_id");
  }

  // determine the LIMIT clause
  $limit_clause = "";
  $first_item = ($page - 1) * $per_page;
  $limit_clause = "LIMIT $first_item, $per_page";

  $query = mysql_query("
    SELECT ms.*, f.$username_col
    FROM   {$g_table_prefix}module_submission_accounts_data ms, {$g_table_prefix}form_{$form_id} f
    WHERE  ms.form_id = $form_id AND
           ms.submission_id = f.submission_id
    ORDER BY last_logged_in DESC
       $limit_clause
    ");

   $info = array();
   while ($row = mysql_fetch_assoc($query))
     $info[] = $row;

   $count_result = mysql_query("
    SELECT count(*) as c
    FROM 	 {$g_table_prefix}module_submission_accounts_data
      ");
   $count_hash = mysql_fetch_assoc($count_result);

  $return_hash["results"]     = $info;
  $return_hash["num_results"] = $count_hash["c"];

  return $return_hash;
}


/**
 * Called on the Settings page. This updates the general settings for the Submission Accounts module.
 *
 * @param array $info
 * @return array [0] T/F, [1] a message
 */
function sa_update_settings($info)
{
  global $L;

  $settings = array(
    "login_form_heading"      => $info["login_form_heading"],
    "login_form_welcome_text" => $info["login_form_welcome_text"],
    "username_field_label"    => $info["username_field_label"],
    "password_field_label"    => $info["password_field_label"],
    "login_button_label"      => $info["login_button_label"],
    "logout_url"              => $info["logout_url"],
    "num_logged_in_users_per_page" => $info["num_logged_in_users_per_page"]
  );

  ft_set_module_settings($settings);

  return array(true, $L["notify_settings_updated"]);
}


/**
 * Deletes everything about a submission account.
 */
function sa_delete_submission_account($form_id)
{
  global $g_table_prefix;

  mysql_query("DELETE FROM {$g_table_prefix}module_submission_accounts_view_override WHERE form_id = $form_id");
  mysql_query("DELETE FROM {$g_table_prefix}module_submission_accounts_menus WHERE form_id = $form_id");
  mysql_query("DELETE FROM {$g_table_prefix}module_submission_accounts WHERE form_id = $form_id");

  sa_delete_submission_account_data($form_id);
}


/**
 * Deletes all the info about logged in users in the database.
 *
 * @param integer $form_id
 * @return array [0] T/F [1] message
 */
function sa_delete_submission_account_data($form_id)
{
  global $g_table_prefix, $L;

  mysql_query("
    DELETE FROM {$g_table_prefix}module_submission_accounts_data
    WHERE  form_id = $form_id
      ");

  return array(true, $L["notify_submission_account_data_deleted"]);
}