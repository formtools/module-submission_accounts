  {include file="messages.tpl"}

    <form action="{$same_page}" method="post" onsubmit="sa_ns.update_menu_submit()">
      <input type="hidden" name="num_rows" id="num_rows" value="{$submission_account.menu_items|@count}" />

      <div class="sortable groupable edit_sa_menu" id="{$sortable_id}">
        <ul class="header_row">
          <li class="col1">{$LANG.word_order}</li>
          <li class="col2">{$LANG.word_page}</li>
          <li class="col3">{$LANG.phrase_display_text}</li>
          <li class="col4">{$LANG.word_options}</li>
          <li class="col5">{$LANG.word_submenu}</li>
          <li class="col6 colN del"></li>
        </ul>
        <div class="clear"></div>
        <ul class="rows check_areas" id="rows">
        {foreach from=$submission_account.menu_items key=k item=i name=edit_menu_items}
          <li class="sortable_row">
            <div class="row_content">
              <div class="row_group{if $smarty.foreach.edit_menu_items.last} rowN{/if}">
                <input type="hidden" class="sr_order" value="{$i.list_order}" />
                <ul>
                  <li class="col1 sort_col">{$i.list_order}</li>
                  <li class="col2">
                    {user_pages_dropdown selected=$i.page_identifier name_id="page_identifier_`$i.list_order`"}
                  </li>
                  <li class="col3">
                    <input type="text" name="display_text_{$i.list_order}" id="display_text_{$i.list_order}" value="{$i.display_text|escape}" />
                  </li>
                  <li class="col4" id="row_{$i.list_order}_options">
                    {if $i.page_identifier == "custom_url"}
                      URL:&nbsp;<input type="text" name="custom_options_{$i.list_order}" id="custom_options_{$i.list_order}" value="{$i.url|escape}" style="width:150px" />
                    {else}
                      <span class="medium_grey">{$LANG.word_na}</span>
                    {/if}
                  </li>
                  <li class="col5 check_area"><input type="checkbox" name="submenu_{$i.list_order}" {if $i.is_submenu == "yes"}checked{/if} /></li>
                  <li class="col6 colN del"><a href="#" onclick="return sa_ns.remove_menu_item_row({$i.list_order})"></a></li>
                </ul>
                <div class="clear"></div>
              </div>

            </div>
            <div class="clear"></div>
          </li>
        {/foreach}
        </ul>
      </div>

      <script>
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
