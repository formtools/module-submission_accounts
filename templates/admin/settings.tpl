{ft_include file='modules_header.tpl'}

  <table cellpadding="0" cellspacing="0">
  <tr>
    <td width="45"><a href="../"><img src="../images/icon_submission_accounts.gif" border="0" width="34" height="34" /></a></td>
    <td class="title">
      <a href="../../../admin/modules/">{$LANG.word_modules}</a>
      <span class="joiner">&raquo;</span>
      <a href="../">{$L.module_name}</a>
      <span class="joiner">&raquo;</span>
      {$L.module_name}
    </td>
  </tr>
  </table>

  {ft_include file="messages.tpl"}

  <form action="{$same_page}" method="post">

    <table cellspacing="1" cellpadding="0" class="list_table">
    <tr>
      <td class="pad_left_small" width="240">Login Page Heading</td>
      <td><input type="text" name="login_form_heading" style="width:300px" value="{$module_settings.login_form_heading|escape}" /></td>
    </tr>
    <tr>
      <td class="pad_left_small" valign="top">Login Page Intro Text</td>
      <td><textarea name="login_form_welcome_text" style="width:98%; height: 60px">{$module_settings.login_form_welcome_text|escape}</textarea></td>
    </tr>
    <tr>
      <td class="pad_left_small">Login Page Username / Email login field label</td>
      <td><input type="text" name="username_field_label" style="width:300px" value="{$module_settings.username_field_label|escape}" /></td>
    </tr>
    <tr>
      <td class="pad_left_small">Login Page Password Field Label</td>
      <td><input type="text" name="password_field_label" style="width:200px" value="{$module_settings.password_field_label|escape}" /></td>
    </tr>
    <tr>
      <td class="pad_left_small">Login Button Label</td>
      <td><input type="text" name="login_button_label" style="width:200px" value="{$module_settings.login_button_label|escape}" /></td>
    </tr>
    <tr>
      <td class="pad_left_small">Logout URL</td>
      <td>
        <input type="radio" id="ll1" name="logout_location" value="login_page" {if $module_settings.logout_location == "login_page"}checked{/if} />
          <label for="ll1">The appropriate login page</label><br />
        <input type="radio" id="ll2" name="logout_location" value="custom_url"
          {if $module_settings.logout_location == "custom_url" || $module_settings.logout_location == ""}checked{/if} />
          <label for="ll2">Custom URL:</label>
          <input type="text" name="logout_url" style="width:370px" value="{$module_settings.logout_url|escape}" />
      </td>
    </tr>
    <tr>
      <td class="pad_left_small">Num Users Listed Per Page</td>
      <td><input type="text" name="num_logged_in_users_per_page" style="width:30px" value="{$module_settings.num_logged_in_users_per_page|escape}" /></td>
    </tr>
    </table>

    <p>
      <input type="submit" name="update_settings" value="{$LANG.word_update}" />
    </p>
  </form>

{ft_include file='modules_footer.tpl'}
