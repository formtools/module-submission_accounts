<?php

/**
 * Module file: Submission Accounts
 */

$MODULE["author"]          = "Ben Keen";
$MODULE["author_email"]    = "ben.keen@gmail.com";
$MODULE["author_link"]     = "http://modules.formtools.org";
$MODULE["version"]         = "1.2.9";
$MODULE["date"]            = "2012-04-11";
$MODULE["origin_language"] = "en_us";

// define the module navigation - the keys are keys defined in the language file. This lets
// the navigation - like everything else - be customized to the users language
$MODULE["nav"] = array(
  "module_name"   => array("{\$module_dir}/index.php", false),
  "word_settings" => array("{\$module_dir}/admin/settings.php", true),
  "word_help"     => array("{\$module_dir}/admin/help.php", true)
);