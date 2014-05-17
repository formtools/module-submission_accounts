<?php

$folder = dirname(__FILE__);
require_once("$folder/global/code/admin.php");
require_once("$folder/global/code/users.php");

function submission_accounts__install($module_id)
{
  global $g_table_prefix, $g_root_url, $LANG;

  $queries = array();
  $queries[] = "
    CREATE TABLE {$g_table_prefix}module_submission_accounts (
      form_id mediumint(8) unsigned NOT NULL,
      view_id mediumint(8) unsigned default NULL,
      theme varchar(255) NOT NULL,
      is_active enum('yes','no') NOT NULL default 'yes',
      inactive_login_message mediumtext,
      email_field_id MEDIUMINT default NULL,
      username_field_id MEDIUMINT default NULL,
      password_field_id MEDIUMINT default NULL,
      PRIMARY KEY  (form_id)
    ) TYPE=MyISAM DEFAULT CHARSET=utf8
      ";
  $queries[] = "
    CREATE TABLE {$g_table_prefix}module_submission_accounts_menus (
      form_id mediumint(8) unsigned NOT NULL,
      display_text varchar(255) NOT NULL,
      url varchar(255) NOT NULL,
      page_identifier varchar(255) NOT NULL,
      is_submenu ENUM ('yes','no') NOT NULL default 'no',
      list_order tinyint(4) NOT NULL
    ) TYPE=MyISAM DEFAULT CHARSET=utf8
      ";
  $queries[] = "
    CREATE TABLE {$g_table_prefix}module_submission_accounts_data (
      form_id mediumint(9) NOT NULL,
      submission_id mediumint(9) NOT NULL,
      last_logged_in datetime NOT NULL,
      PRIMARY KEY  (form_id,submission_id)
    ) TYPE=MyISAM DEFAULT CHARSET=utf8
      ";

  $queries[] = "INSERT INTO {$g_table_prefix}settings (setting_name, setting_value, module) VALUES ('login_form_heading', 'Please Log In', 'submission_accounts')";
  $queries[] = "INSERT INTO {$g_table_prefix}settings (setting_name, setting_value, module) VALUES ('login_form_welcome_text', '', 'submission_accounts')";
  $queries[] = "INSERT INTO {$g_table_prefix}settings (setting_name, setting_value, module) VALUES ('username_field_label', 'Email', 'submission_accounts')";
  $queries[] = "INSERT INTO {$g_table_prefix}settings (setting_name, setting_value, module) VALUES ('password_field_label', 'Password', 'submission_accounts')";
  $queries[] = "INSERT INTO {$g_table_prefix}settings (setting_name, setting_value, module) VALUES ('login_button_label', 'LOGIN', 'submission_accounts')";
  $queries[] = "INSERT INTO {$g_table_prefix}settings (setting_name, setting_value, module) VALUES ('logout_url', '$g_root_url', 'submission_accounts')";
  $queries[] = "INSERT INTO {$g_table_prefix}settings (setting_name, setting_value, module) VALUES ('num_logged_in_users_per_page', 10, 'submission_accounts')";

  $queries[] = "
    CREATE TABLE IF NOT EXISTS {$g_table_prefix}module_submission_accounts_view_override (
      override_id mediumint(8) unsigned NOT NULL auto_increment,
      form_id mediumint(8) unsigned NOT NULL,
      field_id mediumint(9) NOT NULL,
      match_values varchar(255) NOT NULL,
      view_id mediumint(8) unsigned NOT NULL,
      process_order smallint(5) unsigned NOT NULL,
      PRIMARY KEY (override_id)
    ) TYPE=MyISAM DEFAULT CHARSET=utf8
      ";

  $has_problem = false;
  foreach ($queries as $query)
  {
    $result = mysql_query($query);
    if (!$result)
    {
      $has_problem = true;
      break;
    }
  }

  // if there was a problem, remove all the old tables and return an error
  if ($has_problem)
  {
    @mysql_query("DROP TABLE {$g_table_prefix}module_submission_accounts");
    @mysql_query("DROP TABLE {$g_table_prefix}module_submission_accounts_data");
    @mysql_query("DROP TABLE {$g_table_prefix}module_submission_accounts_menus");
    @mysql_query("DROP TABLE {$g_table_prefix}module_submission_accounts_view_override");
    $message = ft_eval_smarty_string($LANG["submission_accounts"]["notify_problem_installing"], array("error" => mysql_error()));

    return array(false, $message);
  }

  return array(true, "");
}


function submission_accounts__uninstall($module_id)
{
  global $g_table_prefix;

  @mysql_query("DROP TABLE {$g_table_prefix}module_submission_accounts");
  @mysql_query("DROP TABLE {$g_table_prefix}module_submission_accounts_data");
  @mysql_query("DROP TABLE {$g_table_prefix}module_submission_accounts_menus");
  @mysql_query("DROP TABLE {$g_table_prefix}module_submission_accounts_view_override");
  @mysql_query("DELETE FROM {$g_table_prefix}settings WHERE module = 'submission_accounts'");

  return array(true, "");
}


function submission_accounts__upgrade($old_version, $new_version)
{
  global $g_table_prefix;

  $old_version_info = ft_get_version_info($old_version);

  if ($old_version_info["release_date"] < 20091121)
  {
    mysql_query("
      CREATE TABLE IF NOT EXISTS {$g_table_prefix}module_submission_accounts_view_override (
        override_id mediumint(8) unsigned NOT NULL auto_increment,
        form_id mediumint(8) unsigned NOT NULL,
        field_id mediumint(9) NOT NULL,
        match_values varchar(255) NOT NULL,
        view_id mediumint(8) unsigned NOT NULL,
        process_order smallint(5) unsigned NOT NULL,
        PRIMARY KEY (override_id)
      ) TYPE=MyISAM DEFAULT CHARSET=utf8
        ");
  }
  if ($old_version_info["release_date"] < 20100911)
  {
  	@mysql_query("ALTER TABLE {$g_table_prefix}module_submission_accounts TYPE=MyISAM");
  	@mysql_query("ALTER TABLE {$g_table_prefix}module_submission_accounts_data TYPE=MyISAM");
  	@mysql_query("ALTER TABLE {$g_table_prefix}module_submission_accounts_menus TYPE=MyISAM");
  	@mysql_query("ALTER TABLE {$g_table_prefix}module_submission_accounts_view_override TYPE=MyISAM");
  }
}
