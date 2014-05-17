<?php

require_once("../../../global/library.php");
ft_init_module_page();
require_once("../library.php");

$section = ft_load_module_field("submission_accounts", "section", "section", "about");

// ------------------------------------------------------------------------------------------------

$page_vars = array();
$page_vars["head_title"] = $L["module_name"];

ft_display_module_page("templates/admin/help.tpl", $page_vars);