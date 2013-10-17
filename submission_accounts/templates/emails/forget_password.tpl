{*
  forget_password.tpl
  -------------------

  This template is used to generate the "forgot your password?" email for the Submission Accounts module.
  It's sent in text format only.

  These placeholders have special meaning:
    $login_url  - the login URL for this form (i.e. with the ?form_id=X appended to the query string)
    $username   - the username
    $password   - the password (unencrypted)

  Note: the language strings ($LANG.-----} are all stored in your language file /global/lang/. If you
  change the contents of that file, bear in mind that any time you upgrade Form Tools those changes
  will be overwritten.
*}
{$LANG.text_login_info}

{$LANG.phrase_login_panel_c} {$login_url}
{$LANG.word_username_c} {$username}
{$LANG.word_password_c} {$password}