{include file='modules_header.tpl'}

  <table cellpadding="0" cellspacing="0" width="100%" class="margin_bottom_large">
  <tr>
    <td width="45"><a href="../index.php"><img src="../images/icon_submission_accounts.gif" border="0" width="34" height="34" /></a></td>
    <td class="title">{$submission_account.form_name|upper}</td>
    <td align="right">
      <span id="loading_icon" style="display:none"><img src="{$images_url}/ajax_loading.gif" /></span>
    </td>
  </tr>
  </table>

	{include file='tabset_open.tpl'}

		{if $page == "main"}
			{include file='../../modules/submission_accounts/templates/admin/tab_main.tpl'}
		{elseif $page == "menu"}
			{include file='../../modules/submission_accounts/templates/admin/tab_menu.tpl'}
		{elseif $page == "users"}
			{include file='../../modules/submission_accounts/templates/admin/tab_users.tpl'}
		{elseif $page == "html"}
			{include file='../../modules/submission_accounts/templates/admin/tab_html.tpl'}
		{else}
			{include file='../../modules/submission_accounts/templates/admin/tab_main.tpl'}
		{/if}

	{include file='tabset_close.tpl'}

{include file='modules_footer.tpl'}