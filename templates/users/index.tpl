{assign var=g_omit_top_bar value=true}
{ft_include file='header.tpl'}

  <div class="edit_submission">
    <table cellpadding="0" cellspacing="0" width="100%" class="margin_bottom_large">
    <tr>
      <td><span class="title">{$edit_submission_page_label}</span></td>
    </tr>
    </table>

    {template_hook location="submission_accounts_modules_edit_submission_top"}

    {if $tabs|@count > 0}
      {ft_include file='tabset_open.tpl'}
    {/if}

    {ft_include file="messages.tpl"}

    <form action="{$same_page}" method="post" name="edit_submission_form" enctype="multipart/form-data">
      {* hidden fields needed for JS - don't delete! *}
      <input type="hidden" name="form_id" id="form_id" value="{$form_id}" />
      <input type="hidden" name="submission_id" id="submission_id" value="{$submission_id}" />
      <input type="hidden" name="tab" id="tab" value="{$tab_number}" />

      {foreach from=$grouped_fields key=k item=curr_group}
        {assign var=group value=$curr_group.group}
        {assign var=fields value=$curr_group.fields}

        {if $group.group_name}
          <h3>{$group.group_name|upper}</h3>
        {/if}

        {if $fields|@count > 0}
          <table class="list_table" cellpadding="1" cellspacing="1" border="0" width="100%">
        {/if}

        {foreach from=$fields item=curr_field}
          {assign var=field_id value=$field.field_id}
          <tr>
            <td width="150" class="pad_left_small" valign="top">{$curr_field.field_title}</td>
            <td valign="top">
              {edit_custom_field form_id=$form_id submission_id=$submission_id field_info=$curr_field field_types=$field_types
                settings=$settings}
            </td>
          </tr>
        {/foreach}

        {if $fields|@count > 0}
          </table>
        {/if}
      {/foreach}

      <input type="hidden" name="field_ids" value="{$page_field_ids_str}" />

      {* if there are no fields in this tab, display a message to let the user know *}
      {if $page_field_ids|@count == 0}
        <div class="margin_bottom_large">{$LANG.notify_no_fields_in_tab}</div>
      {/if}

      <div style="position:relative">
        {* only show the update button if there are editable fields in the tab *}
        {if $page_field_ids|@count > 0 && $tab_has_editable_fields}
          <input type="submit" name="update" value="{$LANG.word_update|upper}" />
        {/if}
      </div>
    </form>

    {if $tabs|@count > 0}
      {ft_include file='tabset_close.tpl'}
    {/if}

    {template_hook location="submission_accounts_modules_edit_submission_bottom"}
  </div>

{ft_include file='footer.tpl'}