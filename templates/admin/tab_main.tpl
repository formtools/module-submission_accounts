  {include file="messages.tpl"}

  <form action="{$same_page}" method="post" onsubmit="return rsv.validate(this, rules)">
    <input type="hidden" id="default_view_id" value="{$submission_account.view_id}" />

	  <table cellspacing="1" cellpadding="0" class="list_table">
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
	    <td width="15" class="red" align="center">*</td>
	    <td class="pad_left_small" >{$LANG.word_form}</td>
	    <td class="medium_grey">{$submission_account.form_name}</td>
	  </tr>
	  <tr>
	    <td class="red" align="center">*</td>
	    <td class="pad_left_small">{$LANG.word_view}</td>
	    <td>
	      {views_dropdown name_id="view_id" form_id=$form_id default=$submission_account.view_id}
	    </td>
	  </tr>
	  <tr>
	    <td class="red" align="center">*</td>
	    <td class="pad_left_small">{$LANG.word_theme}</td>
	    <td>{themes_dropdown name_id="theme" default=$submission_account.theme}</td>
	  </tr>
	  <tr>
	    <td class="red" align="center"> </td>
	    <td class="pad_left_small">Email Field</td>
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
	    <td class="pad_left_small">Username Field</td>
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
	    <td class="pad_left_small">Password Field</td>
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

	  <div class="margin_top_large">
	    <input type="submit" name="update" value="{$LANG.word_update|upper}" />
	  </div>

  </form>
