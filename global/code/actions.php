<?php

/**
 * Actions.php
 *
 * This file handles all server-side responses for Ajax requests. As of 2.0.0, it returns information
 * in JSON format to be handled by JS.
 */

// -------------------------------------------------------------------------------------------------

require_once("../../../../global/library.php");
ft_init_module_page();


// the action to take and the ID of the page where it will be displayed (allows for
// multiple calls on same page to load content in unique areas)
$request = array_merge($_POST, $_GET);
$action  = $request["action"];

// Find out if we need to return anything back with the response. This mechanism allows us to pass any information
// between the Ajax submit function and the Ajax return function. Usage:
//   "return_vals[]=question1:answer1&return_vals[]=question2:answer2&..."
$return_val_str = "";
if (isset($request["return_vals"]))
{
  $vals = array();
  foreach ($request["return_vals"] as $pair)
  {
    list($key, $value) = split(":", $pair);
    $vals[] = "$key: \"$value\"";
  }
  $return_val_str = ", " . join(", ", $vals);
}


switch ($action)
{
  case "get_form_fields":
    $form_id = $request["form_id"];
    $form_fields = ft_get_form_fields($form_id);

    $js_info = array();
    foreach ($form_fields as $field_info)
      $js_info[] = "[{$field_info["field_id"]}, \"" . htmlspecialchars($field_info["field_title"], ENT_QUOTES) . "\"]";

    $js_array = "[" . join(", ", $js_info) . "]";

    echo "{ success: true, form_id: $form_id, fields: " . $js_array . " }";
    break;
}

