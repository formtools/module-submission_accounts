{include file='modules_header.tpl'}

  <table cellpadding="0" cellspacing="0">
  <tr>
    <td width="45"><img src="images/icon_submission_accounts.gif" /></td>
    <td class="title">{$L.module_name|upper}</td>
  </tr>
  </table>

  {include file="messages.tpl"}

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
      <th width="80">{$L.word_users|upper}</th>
      <th width="80">{$LANG.word_edit|upper}</th>
      <th width="80" class="del">{$LANG.word_delete|upper}</th>
    </tr>
    {foreach from=$submission_accounts item=info row=row}
    <tr>
      <td class="medium_grey" align="center">{$info.form_id}</td>
      <td class="pad_left_small"><a href="{$info.form_url}" target="_blank">{$info.form_name}</a></td>
      <td align="center"><a href="admin/edit.php?form_id={$info.form_id}&page=users">{$L.word_users|upper}</a></td>
      <td align="center"><a href="admin/edit.php?form_id={$info.form_id}">{$LANG.word_edit|upper}</a></td>
      <td class="del"><a href="">{$LANG.word_delete|upper}</a></td>
    </tr>
    {/foreach}
    </table>

  {/if}

  <form action="admin/add.php" method="post">
	  <div class="margin_top_large">
	    <input type="submit" name="add_form" value="{$L.phrase_configure_new_form}" />
	  </div>
  </form>


{include file='modules_footer.tpl'}