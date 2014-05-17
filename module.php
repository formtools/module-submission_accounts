<?php

/**
 * Module file: Submission Accounts
 */

$MODULE["author"]          = "Encore Web Studios";
$MODULE["author_email"]    = "formtools@encorewebstudios.com";
$MODULE["author_link"]     = "http://modules.formtools.org";
$MODULE["version"]         = "1.1.1";
$MODULE["date"]            = "2010-04-23";
$MODULE["origin_language"] = "en_us";
$MODULE["supports_ft_versions"] = "2.0.0";

// define the module navigation - the keys are keys defined in the language file. This lets
// the navigation - like everything else - be customized to the users language
$MODULE["nav"] = array(
  "module_name"   => array("{\$module_dir}/index.php", false),
  "word_settings" => array("{\$module_dir}/admin/settings.php", false),
  "word_help"     => array("{\$module_dir}/admin/help.php", false)
);