<?php

$STRUCTURE = array();
$STRUCTURE["tables"] = array();
$STRUCTURE["tables"]["module_submission_accounts"] = array(
  array(
    "Field"   => "form_id",
    "Type"    => "mediumint(8) unsigned",
    "Null"    => "NO",
    "Key"     => "PRI",
    "Default" => ""
  ),
  array(
    "Field"   => "view_id",
    "Type"    => "mediumint(8) unsigned",
    "Null"    => "YES",
    "Key"     => "",
    "Default" => ""
  ),
  array(
    "Field"   => "theme",
    "Type"    => "varchar(255)",
    "Null"    => "NO",
    "Key"     => "",
    "Default" => ""
  ),
  array(
    "Field"   => "is_active",
    "Type"    => "enum('yes','no')",
    "Null"    => "NO",
    "Key"     => "",
    "Default" => "yes"
  ),
  array(
    "Field"   => "inactive_login_message",
    "Type"    => "mediumtext",
    "Null"    => "YES",
    "Key"     => "",
    "Default" => ""
  ),
  array(
    "Field"   => "email_field_id",
    "Type"    => "mediumint(9)",
    "Null"    => "YES",
    "Key"     => "",
    "Default" => ""
  ),
  array(
    "Field"   => "username_field_id",
    "Type"    => "mediumint(9)",
    "Null"    => "YES",
    "Key"     => "",
    "Default" => ""
  ),
  array(
    "Field"   => "password_field_id",
    "Type"    => "mediumint(9)",
    "Null"    => "YES",
    "Key"     => "",
    "Default" => ""
  )
);
$STRUCTURE["tables"]["module_submission_accounts_data"] = array(
  array(
    "Field"   => "form_id",
    "Type"    => "mediumint(9)",
    "Null"    => "NO",
    "Key"     => "PRI",
    "Default" => ""
  ),
  array(
    "Field"   => "submission_id",
    "Type"    => "mediumint(9)",
    "Null"    => "NO",
    "Key"     => "PRI",
    "Default" => ""
  ),
  array(
    "Field"   => "last_logged_in",
    "Type"    => "datetime",
    "Null"    => "NO",
    "Key"     => "",
    "Default" => ""
  )
);
$STRUCTURE["tables"]["module_submission_accounts_menus"] = array(
  array(
    "Field"   => "form_id",
    "Type"    => "mediumint(8) unsigned",
    "Null"    => "NO",
    "Key"     => "",
    "Default" => ""
  ),
  array(
    "Field"   => "display_text",
    "Type"    => "varchar(255)",
    "Null"    => "NO",
    "Key"     => "",
    "Default" => ""
  ),
  array(
    "Field"   => "url",
    "Type"    => "varchar(255)",
    "Null"    => "NO",
    "Key"     => "",
    "Default" => ""
  ),
  array(
    "Field"   => "page_identifier",
    "Type"    => "varchar(255)",
    "Null"    => "NO",
    "Key"     => "",
    "Default" => ""
  ),
  array(
    "Field"   => "is_submenu",
    "Type"    => "enum('yes','no')",
    "Null"    => "NO",
    "Key"     => "",
    "Default" => "no"
  ),
  array(
    "Field"   => "list_order",
    "Type"    => "tinyint(4)",
    "Null"    => "NO",
    "Key"     => "",
    "Default" => ""
  )
);
$STRUCTURE["tables"]["module_submission_accounts_view_override"] = array(
  array(
    "Field"   => "override_id",
    "Type"    => "mediumint(8) unsigned",
    "Null"    => "NO",
    "Key"     => "PRI",
    "Default" => ""
  ),
  array(
    "Field"   => "form_id",
    "Type"    => "mediumint(8) unsigned",
    "Null"    => "NO",
    "Key"     => "",
    "Default" => ""
  ),
  array(
    "Field"   => "field_id",
    "Type"    => "mediumint(9)",
    "Null"    => "NO",
    "Key"     => "",
    "Default" => ""
  ),
  array(
    "Field"   => "match_values",
    "Type"    => "varchar(255)",
    "Null"    => "NO",
    "Key"     => "",
    "Default" => ""
  ),
  array(
    "Field"   => "view_id",
    "Type"    => "mediumint(8) unsigned",
    "Null"    => "NO",
    "Key"     => "",
    "Default" => ""
  ),
  array(
    "Field"   => "process_order",
    "Type"    => "smallint(5) unsigned",
    "Null"    => "NO",
    "Key"     => "",
    "Default" => ""
  )
);

$HOOKS = array();

$FILES = array(
  "admin/",
  "admin/add.php",
  "admin/edit.php",
  "admin/help.php",
  "admin/settings.php",
  "admin/tab_html.php",
  "admin/tab_main.php",
  "admin/tab_menu.php",
  "admin/tab_users.php",
  "database_integrity.php",
  "forget_password.php",
  "global/",
  "global/code/",
  "global/code/actions.php",
  "global/code/admin.php",
  "global/code/users.php",
  "global/css/",
  "global/css/styles.css",
  "global/scripts/",
  "global/scripts/generate_custom_login_form.js",
  "global/scripts/manage_submission_account.js",
  "global/scripts/manage_user_menu.js",
  "images/",
  "images/icon.psd",
  "images/icon_submission_accounts.gif",
  "images/icon_submission_accounts.jpg",
  "index.php",
  "lang/",
  "lang/en_us.php",
  "library.php",
  "login.php",
  "logout.php",
  "module.php",
  "module_config.php",
  "smarty/",
  "smarty/function.user_pages_dropdown.php",
  "templates/",
  "templates/admin/",
  "templates/admin/add.tpl",
  "templates/admin/edit.tpl",
  "templates/admin/help.tpl",
  "templates/admin/settings.tpl",
  "templates/admin/tab_html.tpl",
  "templates/admin/tab_main.tpl",
  "templates/admin/tab_menu.tpl",
  "templates/admin/tab_users.tpl",
  "templates/emails/",
  "templates/emails/forget_password.tpl",
  "templates/emails/forget_password_subject.tpl",
  "templates/forget_password.tpl",
  "templates/index.tpl",
  "templates/login.tpl",
  "templates/users/",
  "templates/users/index.tpl",
  "users/",
  "users/index.php"
);
