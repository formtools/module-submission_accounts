{ft_include file='modules_header.tpl'}

  <table cellpadding="0" cellspacing="0">
  <tr>
    <td width="45"><a href="index.php"><img src="images/icon_submission_accounts.gif" border="0" width="34" height="34" /></a></td>
    <td class="title">
      <a href="../../admin/modules">{$LANG.word_modules}</a>
      <span class="joiner">&raquo;</span>
      {$L.module_name}
    </td>
  </tr>
  </table>

  {ft_include file="messages.tpl"}

  <div class="margin_bottom_large">
    {$L.module_description}
  </div>

  {if $submission_accounts|@count == 0}

    <div class="notify yellow_bg" class="margin_bottom_large">
      <div style="padding:8px">
        {$L.notify_no_submission_accounts}
      </div>
    </div>

  {else}

    <table cellspacing="1" cellpadding="0" class="list_table" width="100%">
    <tr>
      <th width="30">{$LANG.word_id|upper}</th>
      <th>{$LANG.word_form}</th>
      <th>{$LANG.word_view}</th>
      <th>Uses View Override(s)?</th>
      <th width="120">{$LANG.word_status}</th>
      <th class="edit"></th>
      <th class="del"></th>
    </tr>
    {foreach from=$submission_accounts item=info row=row}
    <tr>
      <td class="medium_grey" align="center">{$info.form_id}</td>
      <td class="pad_left_small"><a href="../../admin/forms/submissions.php?form_id={$info.form_id}">{$info.form_name}</a></td>
      <td class="pad_left_small"><a href="../../admin/forms/edit.php?form_id={$info.form_id}&view_id={$info.view_id}&page=edit_view">{display_view_name view_id=$info.view_id}</a></td>
      <td align="center">
        {if $info.view_overrides|@count == 0}
          {$LANG.word_no}
        {else}
          {$LANG.word_yes}
        {/if}
      </td>
      <td align="center">
        {if $info.submission_account_is_active == "yes"}
          <span class="light_green">{$LANG.word_active}</span>
        {else}
          <span class="red">{$LANG.word_disabled}</span>
        {/if}
      </td>
      <td class="edit"><a href="admin/edit.php?form_id={$info.form_id}"></a></td>
      <td class="del"><a href="#" onclick="return page_ns.delete_form({$info.form_id})"></a></td>
    </tr>
    {/foreach}
    </table>

  {/if}

  <form action="admin/add.php" method="post">
    <div class="margin_top_large">
      <input type="submit" name="add_form" value="{$L.phrase_configure_new_form}" />
    </div>
  </form>


{ft_include file='modules_footer.tpl'}
