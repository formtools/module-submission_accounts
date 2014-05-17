  {include file="messages.tpl"}

    <form action="{$same_page}" method="post" onsubmit="sa_ns.update_menu_submit()">
		  <input type="hidden" name="num_rows" id="num_rows" value="{$submission_account.menu_items|@count}" />

	    <table id="menu_table" class="list_table" cellspacing="1" cellpadding="1" width="100%">
	    <tbody>
	    <tr>
	      <th width="40">{$LANG.word_order}</th>
	      <th>{$LANG.word_page}</th>
	      <th width="130">{$LANG.phrase_display_text}</th>
	      <th>{$LANG.word_options}</th>
	      <th width="75">{$LANG.word_submenu}</th>
	      <th class="del" width="70">{$LANG.word_remove|upper}</th>
	    </tr>
	    {foreach from=$submission_account.menu_items key=k item=i}
		    <tr id="row_{$i.list_order}">
		      <td align="center"><input type="text" style="width:30px" name="menu_row_{$i.list_order}_order" id="menu_row_{$i.list_order}_order" value="{$i.list_order}" /></td>
		      <td width="120">
		        {user_pages_dropdown selected=$i.page_identifier name_id="page_identifier_`$i.list_order`" onchange="sa_ns.change_page(`$i.list_order`, this.value)"}
		      </td>
		      <td width="120"><input type="text" name="display_text_{$i.list_order}" id="display_text_{$i.list_order}" value="{$i.display_text|escape}" style="width:120px" /></td>
		      <td class="nowrap"><div id="row_{$i.list_order}_options" class="nowrap pad_left_small">
		        {if $i.page_identifier == "custom_url"}
              URL:&nbsp;<input type="text" name="custom_options_{$i.list_order}" id="custom_options_{$i.list_order}" value="{$i.url}" style="width:160px" />
		        {else}
		          <span class="medium_grey">{$LANG.word_na}</span>
		        {/if}
		        </div></td>
		      <td align="center"><input type="checkbox" name="submenu_{$i.list_order}" {if $i.is_submenu == "yes"}checked{/if} /></td>
		      <td align="center" class="del"><a href="#" onclick="return sa_ns.remove_menu_item_row({$i.list_order})">{$LANG.word_remove|upper}</a></td>
		    </tr>
		  {/foreach}
	    </tbody></table>

	    <script type="text/javascript">
	    sa_ns.num_rows = {$submission_account.menu_items|@count};
	    </script>

	    <p>
	      <a href="#" onclick="return sa_ns.add_menu_item_row()">{$LANG.phrase_add_row}</a>
	    </p>

	    <div id="menu_options" style="display:none">
  	    {user_pages_dropdown name_id="page_identifier_%%X%%"}
			</div>

			<p>
				<input type="submit" name="update" value="{$LANG.word_update}" />
			</p>
		</form>
