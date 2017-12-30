<?php

require("../../global/library.php");

use FormTools\Core;
use FormTools\Modules;
use FormTools\Sessions;

$module = Modules::initModulePage();
$root_url = Core::getRootUrl();

$form_id = Sessions::getWithFallback("account.form_id", "");

$module_settings = $module->getSettings();
$logout_location = $module_settings["logout_location"];
if ($logout_location == "custom_url" || empty($logout_location)) {
    $logout_url = $module_settings["logout_url"];
} else {
    $logout_url = "$root_url/modules/submission_accounts/login.php";
    if (!empty($form_id)) {
        $logout_url .= "?form_id=" . $form_id;
    }
}

// empty sessions
Sessions::clearAll();

// redirect to login page
header("location: $logout_url");
