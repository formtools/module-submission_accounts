{include file='modules_header.tpl'}

  <table cellpadding="0" cellspacing="0" width="100%">
  <tr>
    <td width="45"><a href="../index.php"><img src="../images/icon_submission_accounts.gif" border="0" width="34" height="34" /></a></td>
    <td class="title">{$L.phrase_configure_new_form|upper}</td>
    <td align="right">
      <span id="loading_icon" style="display:none"><img src="{$images_url}/ajax_loading.gif" /></span>
    </td>
  </tr>
  </table>

  {include file="messages.tpl"}

  <div class="margin_bottom_large">
    {$L.text_add_submission_account_intro}
  </div>

  <form action="../index.php" method="post" onsubmit="return rsv.validate(this, rules)">

	  <table cellspacing="1" cellpadding="0" class="list_table">
	  <tr>
	    <td width="15" class="red" align="center">*</td>
	    <td class="pad_left_small" width="140">{$LANG.word_form}</td>
	    <td>
	      {forms_dropdown name_id="form_id" omit_forms=$omit_forms include_blank_option=true onchange="sa_ns.select_form(this.value)"}
	    </td>
	  </tr>
	  <tr>
	    <td width="15" class="red" align="center">*</td>
	    <td class="pad_left_small">{$LANG.word_view}</td>
	    <td>
	      <select name="view_id" id="view_id" disabled>
	        <option value="">{$LANG.phrase_please_select_form}</option>
	      </select>
	    </td>
	  </tr>
	  <tr>
	    <td class="red" align="center">*</td>
	    <td class="pad_left_small">{$LANG.word_theme}</td>
	    <td>{themes_dropdown name_id="theme" default=$submission_account.theme}</td>
	  </tr>
	  <tr>
	    <td width="15" class="red" align="center"> </td>
	    <td class="pad_left_small">{$L.phrase_email_field}</td>
	    <td>
	      <select name="email_field_id" id="email_field_id" disabled>
	        <option value="">{$LANG.phrase_please_select_form}</option>
	      </select>
	    </td>
	  </tr>
	  <tr>
	    <td width="15" class="red" align="center">*</td>
	    <td class="pad_left_small">{$L.phrase_username_field}</td>
	    <td>
	      <select name="username_field_id" id="username_field_id" disabled>
	        <option value="">{$LANG.phrase_please_select_form}</option>
	      </select>
	    </td>
	  </tr>
	  <tr>
	    <td width="15" class="red" align="center">*</td>
	    <td class="pad_left_small">{$L.phrase_password_field}</td>
	    <td>
	      <select name="password_field_id" id="password_field_id" disabled>
	        <option value="">{$LANG.phrase_please_select_form}</option>
	      </select>
	    </td>
	  </tr>
	  </table>

	  <div class="margin_top_large">
	    <input type="submit" name="add_form" value="{$LANG.word_add|upper}" />
	  </div>

  </form>


{include file='modules_footer.tpl'}