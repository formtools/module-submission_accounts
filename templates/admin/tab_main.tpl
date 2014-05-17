  {include file="messages.tpl"}

  <form action="{$same_page}" method="post" onsubmit="return rsv.validate(this, rules)">
    <input type="hidden" id="form_id" name="form_id" value="{$form_id}" />
    <input type="hidden" id="default_view_id" value="{$submission_account.view_id}" />
    <input type="hidden" name="num_view_override_rows" id="num_view_override_rows" value="{$submission_account.view_overrides|@count}" />

    <table cellspacing="1" cellpadding="0" class="list_table margin_bottom_large">
    <tr>
      <td width="15" class="red" align="center">*</td>
      <td width="140" class="pad_left_small">{$LANG.word_active}</td>
      <td>
        <input type="radio" name="is_active" id="is_active1" value="yes" {if $submission_account.submission_account_is_active == "yes"}checked{/if} />
          <label for="is_active1" class="green">{$LANG.word_yes}</label>
        <input type="radio" name="is_active" id="is_active2" value="no" {if $submission_account.submission_account_is_active == "no"}checked{/if} />
          <label for="is_active2" class="red">{$LANG.word_no}</label>
      </td>
    </tr>
    <tr>
      <td class="red" align="center">*</td>
      <td class="pad_left_small" >{$LANG.word_form}</td>
      <td class="medium_grey">{$submission_account.form_name}</td>
    </tr>
    <tr>
      <td class="red" align="center">*</td>
      <td class="pad_left_small">{$LANG.word_view}</td>
      <td>
        {views_dropdown name_id="view_id" form_id=$form_id selected=$submission_account.view_id}
      </td>
    </tr>
    <tr>
      <td class="red" align="center">*</td>
      <td class="pad_left_small">{$LANG.word_theme}</td>
      <td>{themes_dropdown name_id="theme" default=$submission_account.theme default_swatch=$submission_account.swatch}</td>
    </tr>
    <tr>
      <td class="red" align="center"> </td>
      <td class="pad_left_small">{$L.phrase_email_field}</td>
      <td>
        <select name="email_field_id" id="email_field_id">
          <option value="">{$LANG.phrase_please_select}</option>
          {foreach from=$form_fields item=field}
            <option value="{$field.field_id}" {if $submission_account.email_field_id == $field.field_id}selected{/if}>{$field.field_title}</option>
          {/foreach}
        </select>
      </td>
    </tr>
    <tr>
      <td class="red" align="center">*</td>
      <td class="pad_left_small">{$L.phrase_username_field}</td>
      <td>
        <select name="username_field_id" id="username_field_id">
          <option value="">{$LANG.phrase_please_select}</option>
          {foreach from=$form_fields item=field}
            <option value="{$field.field_id}" {if $submission_account.username_field_id == $field.field_id}selected{/if}>{$field.field_title}</option>
          {/foreach}
        </select>
      </td>
    </tr>
    <tr>
      <td width="15" class="red" align="center">*</td>
      <td class="pad_left_small">{$L.phrase_password_field}</td>
      <td>
        <select name="password_field_id" id="password_field_id">
          <option value="">{$LANG.phrase_please_select}</option>
          {foreach from=$form_fields item=field}
            <option value="{$field.field_id}" {if $submission_account.password_field_id == $field.field_id}selected{/if}>{$field.field_title}</option>
          {/foreach}
        </select>
      </td>
    </tr>
    </table>

    <div class="grey_box">
      <div style="margin_top">
        <a href="#" onclick="return sa_ns.toggle_view_override_settings()">{$L.phrase_view_override_settings_rightarrow}</a>
      </div>

      <div {if $submission_account.view_overrides|@count == 0}style="display:none"{/if} id="view_override_settings">
        <div>
          {$L.text_view_override_intro}
        </div>

        <table cellspacing="1" cellpadding="0" width="100%" id="view_override_table" class="list_table margin_top_large margin_bottom_large">
        <tbody><tr>
          <th width="160" class="blue">{$L.phrase_if_field}</th>
          <th>{$L.phrase_has_values}</th>
          <th>{$L.phrase_then_use_view}</th>
          <th class="del"></th>
        </tr>
        {foreach from=$submission_account.view_overrides key=k item=i}
          {assign var="row" value=$i.process_order}
          <tr id="row_{$row}">
            <td>
              <select name="view_override_field_{$row}" id="view_override_field_{$row}">
                <option value="">{$LANG.phrase_please_select}</option>
                {foreach from=$form_fields item=field}
                  <option value="{$field.field_id}" {if $i.field_id == $field.field_id}selected{/if}>{$field.field_title}</option>
                {/foreach}
              </select>
            </td>
            <td>
              <input type="text" style="width:98%" name="view_override_values_{$row}" id="view_override_values_{$row}" value="{$i.match_values|escape}" />
            </td>
            <td>
              {views_dropdown name_id="view_override_view_`$row`" form_id=$form_id selected=$i.view_id}
            </td>
            <td class="del"><a href="#" onclick="return sa_ns.delete_row({$row})"></a></td>
          </tr>
        {/foreach}
        </tbody></table>

        <div class="margin_bottom_large">
          <a href="#" onclick="return sa_ns.add_view_override_row()">{$LANG.phrase_add_row}</a>
        </div>
      </div>
    </div>

    <div class="margin_top_large">
      <input type="submit" name="update" value="{$LANG.word_update|upper}" />
    </div>

  </form>
