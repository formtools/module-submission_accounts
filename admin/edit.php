<?php

require_once("../../../global/library.php");
ft_init_module_page();
require_once("../library.php");
$request = array_merge($_POST, $_GET);

$page    = ft_load_module_field("submission_accounts", "page", "tab", "main");
$form_id = ft_load_module_field("submission_accounts", "form_id", "form_id");

$tabs = array(
  "main" => array(
      "tab_label" => $LANG["word_main"],
      "tab_link" => "{$_SERVER["PHP_SELF"]}?page=main"
        ),
  "menu" => array(
      "tab_label" => $LANG["word_menu"],
      "tab_link" => "{$_SERVER["PHP_SELF"]}?page=menu"
        ),
  "users" => array(
      "tab_label" => $L["word_users"],
      "tab_link" => "{$_SERVER["PHP_SELF"]}?page=users"
        ),
  "html" => array(
      "tab_label" => $L["phrase_custom_login"],
      "tab_link" => "{$_SERVER["PHP_SELF"]}?page=html"
        )
     );

// load the appropriate code pages
switch ($page)
{
  case "main":
    require("tab_main.php");
    break;
  case "menu":
    require("tab_menu.php");
    break;
  case "users":
    require("tab_users.php");
    break;
  case "html":
    require("tab_html.php");
    break;
  default:
    require("tab_main.php");
    break;
}
