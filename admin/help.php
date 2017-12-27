<?php

require_once("../../../global/library.php");

use FormTools\Modules;

$module = Modules::initModulePage("admin");
$L = $module->getLangStrings();

$section = Modules::loadModuleField("submission_accounts", "section", "section", "about");

$page_vars = array(
    "head_title" => $L["module_name"]
);

$module->displayPage("templates/admin/help.tpl", $page_vars);
