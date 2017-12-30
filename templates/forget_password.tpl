{ft_include file="header.tpl"}

  <table cellpadding="0" cellspacing="0">
  <tr>
    <td width="45"><img src="images/icon_submission_accounts.gif" width="34" height="34" /></td>
    <td class="title">{$LANG.phrase_forgot_password}</td>
  </tr>
  </table>

  {ft_include file='messages.tpl'}

  <div class="margin_bottom_large">
    {$text_forgot_password}
  </div>

  <form name="forget_password" action="{$same_page}" method="post"
    onsubmit="return rsv.validate(this, rules)">

    <div class="login_panel margin_bottom_large">
      <div class="login_panel_inner">
        <table cellpadding="0" cellspacing="1">
        <tr>
          <td class="login_table_text">{$LANG.word_email}</td>
          <td><input type="textbox" size="25" name="email" value=""></td>
          <td align="center"><input type="submit" name="send_password" value="{$LANG.word_email|upper}" /></td>
        </tr>
        </table>
        <div class="clear"></div>
      </div>
    </div>
  </form>

  <div>
    <a href="login.php">{$LANG.phrase_login_panel_leftarrows}</a>
  </div>

{ft_include file="footer.tpl"}
