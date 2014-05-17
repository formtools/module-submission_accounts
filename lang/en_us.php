<?php

/*
Form Tools - Module Language File
---------------------------------

File created: Oct 24th, 2:46 AM

If you would like to help translate this module, please visit:
http://www.formtools.org/translations/
*/


$L = array();

// required
$L["module_name"] = "Submission Accounts";
$L["module_description"] = "This module converts a form submission into a simple user account, letting the individual who submitted the form log in and edit their values.";

$L["word_help"] = "Help";
$L["word_settings"] = "Settings";
$L["word_users"] = "Users";

$L["notify_no_submission_accounts"] = "There are no submission accounts configured for your forms. Click the button below to get started.";
$L["phrase_configure_new_form"] = "Configure New Form";
$L["phrase_password_reminder"] = "Password Reminder";

$L["phrase_email_field"] = "Email Field";
$L["phrase_username_field"] = "Username Field";
$L["phrase_password_field"] = "Password Field";
$L["phrase_custom_login"] = "Custom Login";
$L["phrase_redirect_failed_logins_c"] = "Redirect failed logins to custom URL:";
$L["phrase_clear_results"] = "Clear Results";

$L["validation_no_username_field"] = "Please select the field which will act as the username.";
$L["validation_no_password_field"] = "Please select the field which will act as the password.";
$L["validation_no_form_id"] = "Please select a form.";
$L["validation_no_view_id"] = "Please select a View.";
$L["validation_no_username"] = "Please enter your username.";
$L["validation_no_password"] = "Please enter your password.";
$L["validation_login_invalid_form_id"] = "Sorry, Submission Accounts have not been configured for this form.";
$L["validation_login_incorrect"] = "Sorry, that login information is incorrect. Please try again.";
$L["validation_email_not_found"] = "Sorry, we can't find a user account with that email address.";
$L["validation_no_email"] = "Please enter your email address.";

$L["notify_error_configuring_form"] = "There was a problem configuring this form.";
$L["notify_form_configured"] = "The submission accounts have been configured for this form.";
$L["notify_submission_account_updated"] = "The submission account has been updated for this form.";
$L["notify_menu_updated"] = "The menu has been updated.";
$L["notify_settings_updated"] = "The settings have been updated.";
$L["notify_login_no_form_id"] = "Sorry, there has been no form ID passed to this page. In order to display the login form, a <b>form_id</b> parameter set to the form ID needs to be passed to this page via POST or GET. Please see the user documentation.";
$L["notify_submission_account_inactive"] = "Sorry, user accounts are currently inactive for this form.";
$L["notify_submission_account_data_deleted"] = "The user login data has been cleared.";
$L["notify_problem_installing"] = "There following error occurred when trying to create the database tables for this module: <b>{\$error}</b>";

$L["text_forget_password_link"] = "If you have forgotten your password, <a href=\"forget_password.php\">click here</a>.";
$L["text_forgot_password"] = "No problem. Just enter your email below and your login information will be sent to you. If you encounter problems, please email the site administrator at {\$site_admin_email}.";
$L["text_add_submission_account_intro"] = "Use the form below to configure the submission accounts for one of your forms. Please see the <a href=\"http://modules.formtools.org/submission_accounts/?page=configure_new_form\">help pages</a> for more information.";
$L["text_html_tab"] = "This tabs lets you generate a custom login form to embed within your own webpages, rather than forcing your users to login via the <a href=\"../login.php\">built-in login form</a>.";
$L["text_include_forget_password"] = "Include \"Forget Password\" link <span class=\"light_grey\">(only available if you have defined the email field)</span>";
