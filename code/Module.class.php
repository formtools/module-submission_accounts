<?php

namespace FormTools\Modules\SubmissionAccounts;

use FormTools\Core;
use FormTools\General;
use FormTools\Module as FormToolsModule;
use FormTools\Settings;
use Exception;


class Module extends FormToolsModule
{
    protected $moduleName = "Submission Accounts";
    protected $moduleDesc = "This module converts a form submission into a simple user account, letting the individual who submitted the form log in and edit their values.";
    protected $author = "Ben Keen";
    protected $authorEmail = "ben.keen@gmail.com";
    protected $authorLink = "https://formtools.org";
    protected $version = "2.0.1";
    protected $date = "2018-02-17";
    protected $originLanguage = "en_us";
    protected $cssFiles = array("{MODULEROOT}/css/styles.css");

    protected $nav = array(
        "module_name"   => array("index.php", false),
        "word_settings" => array("admin/settings.php", true),
        "word_help"     => array("admin/help.php", true)
    );

    public function install($module_id)
    {
        $db = Core::$db;

        try {
            $db->query("
                CREATE TABLE {PREFIX}module_submission_accounts (
                  form_id mediumint(8) unsigned NOT NULL,
                  view_id mediumint(8) unsigned default NULL,
                  theme varchar(255) NOT NULL,
                  swatch varchar(255) NULL,
                  is_active enum('yes','no') NOT NULL default 'yes',
                  inactive_login_message mediumtext,
                  email_field_id MEDIUMINT default NULL,
                  username_field_id MEDIUMINT default NULL,
                  password_field_id MEDIUMINT default NULL,
                  PRIMARY KEY (form_id)
                ) DEFAULT CHARSET=utf8
            ");
            $db->execute();

            $db->query("
                CREATE TABLE {PREFIX}module_submission_accounts_menus (
                  form_id mediumint(8) unsigned NOT NULL,
                  display_text varchar(255) NOT NULL,
                  url varchar(255) NOT NULL,
                  page_identifier varchar(255) NOT NULL,
                  is_submenu ENUM ('yes','no') NOT NULL default 'no',
                  list_order tinyint(4) NOT NULL
                ) DEFAULT CHARSET=utf8
            ");
            $db->execute();

            $db->query("
                CREATE TABLE {PREFIX}module_submission_accounts_data (
                  form_id mediumint(9) NOT NULL,
                  submission_id mediumint(9) NOT NULL,
                  last_logged_in datetime NOT NULL,
                  PRIMARY KEY (form_id,submission_id)
                ) DEFAULT CHARSET=utf8
            ");
            $db->execute();

            $settings = array(
                "login_form_heading" => "Please Log In",
                "login_form_welcome_text" => "",
                "username_field_label" => "Email",
                "password_field_label" => "Password",
                "login_button_label" => "LOGIN",
                "logout_location" => "login_page",
                "logout_url" => "",
                "num_logged_in_users_per_page" => 10
            );
            Settings::set($settings, "submission_accounts");

            $db->query("
                CREATE TABLE IF NOT EXISTS {PREFIX}module_submission_accounts_view_override (
                  override_id mediumint(8) unsigned NOT NULL auto_increment,
                  form_id mediumint(8) unsigned NOT NULL,
                  field_id mediumint(9) NOT NULL,
                  match_values varchar(255) NOT NULL,
                  view_id mediumint(8) unsigned NOT NULL,
                  process_order smallint(5) unsigned NOT NULL,
                  PRIMARY KEY (override_id)
                ) DEFAULT CHARSET=utf8
            ");
            $db->execute();
        } catch (Exception $e) {
            $this->clearTables();

            $L = $this->getLangStrings();
            $message = General::evalSmartyString($L["notify_problem_installing"], array("error" => $e->getMessage()));

            return array(false, $message);
        }

        return array(true, "");
    }


    public function uninstall($module_id)
    {
        $this->clearTables();
        return array(true, "");
    }


    public function clearTables()
    {
        $db = Core::$db;

        $db->query("DROP TABLE IF EXISTS {PREFIX}module_submission_accounts");
        $db->execute();

        $db->query("DROP TABLE IF EXISTS {PREFIX}module_submission_accounts_data");
        $db->execute();

        $db->query("DROP TABLE IF EXISTS {PREFIX}module_submission_accounts_menus");
        $db->execute();

        $db->query("DROP TABLE IF EXISTS {PREFIX}module_submission_accounts_view_override");
        $db->execute();

        $db->query("DELETE FROM {PREFIX}settings WHERE module = 'submission_accounts'");
        $db->execute();
    }


    public function upgrade($old_version_info, $new_version_info)
    {
        $db = Core::$db;

        $old_version_date = date("Ymd", General::convertDatetimeToTimestamp($old_version_info["module_date"]));

        if ($old_version_date < 20091121) {
            $db->query("
                CREATE TABLE IF NOT EXISTS {PREFIX}module_submission_accounts_view_override (
                    override_id mediumint(8) unsigned NOT NULL auto_increment,
                    form_id mediumint(8) unsigned NOT NULL,
                    field_id mediumint(9) NOT NULL,
                    match_values varchar(255) NOT NULL,
                    view_id mediumint(8) unsigned NOT NULL,
                    process_order smallint(5) unsigned NOT NULL,
                    PRIMARY KEY (override_id)
                ) DEFAULT CHARSET=utf8
            ");
            $db->execute();
        }

        if (!self::checkColumnExists("module_submission_accounts", "swatch")) {
            $db->query("ALTER TABLE {PREFIX}module_submission_accounts ADD swatch VARCHAR(255) NULL AFTER theme");
            $db->execute();
        }

        if ($old_version_date < 20110930) {
            $db->query("UPDATE {PREFIX}module_submission_accounts SET swatch = 'green' WHERE theme = 'default'");
            $db->execute();
        }

        return array(true, "");
    }

    // move to core
    private static function checkColumnExists($table_name, $column_name)
    {
        $db = Core::$db;

        $db->query("SHOW COLUMNS FROM {PREFIX}$table_name LIKE '$column_name'");
        $db->execute();

        return !empty($db->fetch());
    }
}
