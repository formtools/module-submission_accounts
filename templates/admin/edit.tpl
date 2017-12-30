{ft_include file='modules_header.tpl'}

  <span id="loading_icon" class="sa-loading" style="display:none"><img src="{$g_root_url}/global/images/loading.gif" /></span>

  <table cellpadding="0" cellspacing="0" class="margin_bottom_large">
  <tr>
    <td width="45"><a href="index.php"><img src="../images/icon_submission_accounts.gif" border="0" width="34" height="34" /></a></td>
    <td class="title">
      <a href="../../admin/modules">{$LANG.word_modules}</a>
      <span class="joiner">&raquo;</span>
      <a href="../">{$L.module_name}</a>
      <span class="joiner">&raquo;</span>
      {$submission_account.form_name}
    </td>
  </tr>
  </table>

  {ft_include file='tabset_open.tpl'}

    {if $page == "main"}
      {include file='./tab_main.tpl'}
    {elseif $page == "menu"}
      {include file='./tab_menu.tpl'}
    {elseif $page == "users"}
      {include file='./tab_users.tpl'}
    {elseif $page == "html"}
      {include file='./tab_html.tpl'}
    {else}
      {include file='./tab_main.tpl'}
    {/if}

  {ft_include file='tabset_close.tpl'}

{ft_include file='modules_footer.tpl'}
