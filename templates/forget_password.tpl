{include file="header.tpl"}

  <div class="title">{$LANG.phrase_forgot_password|upper}</div>

  {include file='messages.tpl'}

  <div class="margin_bottom_large">
    {$text_forgot_password}
  </div>

  <form name="forget_password" action="{$same_page}{$g_query_params}" method="post"
    onsubmit="return rsv.validate(this, rules)">

  <table width="320" cellpadding="1" class="login_outer_table">
  <tr>
    <td colspan="1">

      <table width="100%" cellpadding="0" cellspacing="1" class="login_inner_table">
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td class="login_table_text">{$LANG.word_email}</td>
        <td><input type="textbox" size="25" name="email" value="{$email}"></td>
        <td align="center"><input type="submit" name="send_password" value="{$LANG.word_email|upper}" /></td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      </table>

    </td>
  </tr>
  </table>

  </form>

  <p>
    <a href="login.php">{$LANG.phrase_login_panel_leftarrows}</a>
  </p>

{include file="footer.tpl"}