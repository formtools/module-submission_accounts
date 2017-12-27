<?php

require_once("../../../global/library.php");

use FormTools\Modules;

$module = Modules::initModulePage("admin");
$L = $module->getLangStrings();

$success = true;
$message = "";
if (isset($request["update_settings"])) {
    list ($success, $message) = sa_update_settings($request);
}

$page_vars = array(
    "g_success" => $success,
    "g_message" => $message,
    "head_title" => $L["module_name"],
    "module_settings" => $module->getSettings(),
    "head_js" => ""
);

$module->displayPage("templates/admin/settings.tpl", $page_vars);
