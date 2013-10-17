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
      <div class="login_panel margin_bottom_large">
        <div class="login_panel_inner">
          <table cellpadding="0" cellspacing="1">
          <tr>
            <td>{$module_settings.username_field_label}</td>
            <td><input type="text" name="username" value="{$username}" /></td>
          </tr>
          <tr>
            <td>{$module_settings.password_field_label}</td>
            <td><input type="password" name="password" value="" /></td>
          </tr>
          </table>

          <script>
          document.write('<input type="submit" class="login_submit" name="login" value="{$module_settings.login_button_label|escape}" />');
          </script>
          <div class="clear"></div>
        </div>

        {if $error}
          <div>
            <div class="login_error pad_left">{$error}</div>
          </div>
        {/if}
      </div>
    </form>

    <noscript>
      <div class="error" style="padding:6px;">
        {$LANG.text_js_required}
      </div>
    </noscript>
  {/if}

{include file='footer.tpl'}
