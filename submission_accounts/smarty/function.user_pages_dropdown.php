<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.user_pages_dropdown
 * Type:     function
 * Name:     user_pages_dropdown
 * Purpose:  generates a list of available pages for use in a submission account menu. This includes
 *           any pages in the Pages module in addition to the hardcoded available user pages (logout, edit_submission and
 *           custom_url).
 * -------------------------------------------------------------
 */
function smarty_function_user_pages_dropdown($params, &$smarty)
{
  global $LANG;

	if (empty($params["name_id"]))
  {
	  $smarty->trigger_error("assign: missing 'name_id' parameter. This is used to give the select field a name and id value.");
    return;
  }
  $selected = (isset($params["selected"])) ? $params["selected"] : "";
  $onchange = (isset($params["onchange"])) ? $params["onchange"] : "";

  $attributes = array(
    "id"   => $params["name_id"],
    "name" => $params["name_id"],
    "onchange" => $onchange
      );

	$attribute_str = "";
  while (list($key, $value) = each($attributes))
  {
  	if (!empty($value))
  	  $attribute_str .= " $key=\"$value\"";
  }

  $dropdown_str = "<select {$attribute_str}>
                     <option value=\"\">{$LANG["phrase_please_select"]}</option>
                     <option value=\"custom_url\" " . (($selected == "custom_url") ? "selected" : "") . ">{$LANG["phrase_custom_url"]}</option>
                     <option value=\"edit_submission\" " . (($selected == "edit_submission") ? "selected" : "") . ">{$LANG["phrase_edit_submission"]}</option>
                     <option value=\"logout\" " . (($selected == "logout") ? "selected" : "") . ">{$LANG["word_logout"]}</option>";

  // if the Pages module is enabled and has options, display that too
  if (ft_check_module_enabled("pages"))
  {
    ft_include_module("pages");

    $pages_info = pg_get_pages("all");
    $pages = $pages_info["results"];

    if (!empty($pages))
    {
	  	$dropdown_str .= "<optgroup label=\"{$LANG["phrase_pages_module"]}\">\n";
	  	foreach ($pages as $page)
	  	{
	  		$page_id   = $page["page_id"];
	  		$page_name = $page["page_name"];
	  		$dropdown_str .= "<option value=\"page_{$page_id}\" " . (($selected == "page_{$page_id}") ? "selected" : "") . ">$page_name</option>\n";
	  	}
	  	$dropdown_str .= "</optgroup>\n";
    }
  }
  $dropdown_str .= "</select>";

  return $dropdown_str;
}

