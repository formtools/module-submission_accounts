{include file='header.tpl'}

  {if $main_error}

    <div class="error" id="ft_message_inner">
      <div style="padding:8px">
        {$error}
      </div>
    </div>

  {else}

    <table cellpadding="0" cellspacing="0">
    <tr>
      <td width="45"><img src="images/icon_submission_accounts.gif" width="34" height="34" /></td>
      <td class="title">{$module_settings.login_form_heading}</td>
    </tr>
    </table>

    {include file="messages.tpl"}

    {if $module_settings.login_form_welcome_text || $submission_account.email_field_id}
      <div class="margin_bottom_large">
        {$module_settings.login_form_welcome_text}
        {if $submission_account.email_field_id}
          {$L.text_forget_password_link}
        {/if}
      </div>
    {/if}

    <form name="login" action="{$same_page}{$query_params}" method="post">

      <table cellpadding="1" class="login_outer_table">
      <tr>
        <td colspan="1">

          <table width="100%" cellpadding="0" cellspacing="1" class="login_inner_table">
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr>
            <td>

              <table width="200" cellpadding="0" cellspacing="1">
              <tr>
                <td class="login_table_text" nowrap>{$module_settings.username_field_label}</td>
                <td><input type="text" size="25" name="username" value="{$username}"></td>
              </tr>
              <tr>
                <td class="login_table_text">{$module_settings.password_field_label}</td>
                <td><input type="password" size="25" name="password" value=""></td>
              </tr>
              </table>

            </td>
            <td align="center" valign="center" class="pad_left pad_right">
              <input type="submit" name="login" value="{$module_settings.login_button_label|escape}" />
            </td>
          </tr>
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
          </table>

        </td>
      </tr>

      {if $error}
      <tr>
        <td colspan="3">
          <div class="login_error pad_left">{$error}</div>
        </td>
      </tr>
      {/if}

      </table>

    </form>

  {/if}

{include file='footer.tpl'}