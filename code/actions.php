<?php

/**
 * Actions.php
 *
 * This file handles all server-side responses for Ajax requests. As of 2.0.0, it returns information
 * in JSON format to be handled by JS.
 */

require_once("../../../global/library.php");

use FormTools\Core;
use FormTools\Fields;

Core::init();

if ($request["action"] == "get_form_fields") {
    $form_id = $request["form_id"];
    $form_fields = Fields::getFormFields($form_id);

    $js_info = array();
    foreach ($form_fields as $field_info) {
        $js_info[] = array($field_info["field_id"], htmlspecialchars($field_info["field_title"], ENT_QUOTES));
    }

    echo returnJSON(array(
        "success" => true,
        "form_id" => $form_id,
        "fields" => $js_info
    ));
}


function returnJSON($php)
{
    header("Content-Type: application/json");
    return json_encode($php);
}
