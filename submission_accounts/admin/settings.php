<?php

require_once("../../../global/library.php");
ft_init_module_page();
require_once("../library.php");
$request = array_merge($_POST, $_GET);

if (isset($request["update_settings"]))
  list ($g_success, $g_message) = sa_update_settings($request);


$module_settings = ft_get_module_settings();

// ------------------------------------------------------------------------------------------------

$page_vars = array();
$page_vars["head_title"] = $L["module_name"];
$page_vars["module_settings"] = $module_settings;
$page_vars["head_js"] = "";

ft_display_module_page("templates/admin/settings.tpl", $page_vars);