<?php

require_once("../../../global/library.php");

use FormTools\Modules;
use FormTools\Modules\SubmissionAccounts\Admin;

$module = Modules::initModulePage("admin");
$L = $module->getLangStrings();

$success = true;
$message = "";
if (isset($request["update_settings"])) {
    list ($success, $message) = Admin::updateSettings($request, $L);
}

$page_vars = array(
    "g_success" => $success,
    "g_message" => $message,
    "head_title" => $L["module_name"],
    "module_settings" => $module->getSettings()
);

$module->displayPage("templates/admin/settings.tpl", $page_vars);
