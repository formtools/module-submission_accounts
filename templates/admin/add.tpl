{ft_include file='modules_header.tpl'}

  <span id="loading_icon" style="display:none"><img src="{$g_root_url}/global/images/loading.gif" /></span>

  <table cellpadding="0" cellspacing="0">
  <tr>
    <td width="45"><a href="../"><img src="../images/icon_submission_accounts.gif" border="0" width="34" height="34" /></a></td>
    <td class="title">
      <a href="../../../admin/modules">{$LANG.word_modules}</a>
      <span class="joiner">&raquo;</span>
      <a href="../">{$L.module_name}</a>
      <span class="joiner">&raquo;</span>
      {$L.phrase_configure_new_form}
    </td>
  </tr>
  </table>

  {ft_include file="messages.tpl"}

  <div class="margin_bottom_large">
    {$L.text_add_submission_account_intro}
  </div>

  <form action="../index.php" method="post" onsubmit="return rsv.validate(this, rules)">
    <input type="hidden" name="num_view_override_rows" id="num_view_override_rows" value="1" />

    <table cellspacing="1" cellpadding="0" class="list_table margin_bottom_large">
    <tr>
      <td width="15" class="red" align="center">*</td>
      <td class="pad_left_small" width="140">{$LANG.word_form}</td>
      <td>
        {forms_dropdown name_id="form_id" omit_forms=$omit_forms include_blank_option=true
          onchange="sa_ns.select_form(this.value); sa_ns.update_view_override_table_fields()"}
      </td>
    </tr>
    <tr>
      <td class="red" align="center">*</td>
      <td class="pad_left_small">{$LANG.word_view}</td>
      <td>
        <select name="view_id" id="view_id" disabled onchange="sa_ns.update_view_override_table_fields()">
          <option value="">{$LANG.phrase_please_select_form}</option>
        </select>
      </td>
    </tr>
    <tr>
      <td class="red" align="center">*</td>
      <td class="pad_left_small">{$LANG.word_theme}</td>
      <td>{themes_dropdown name_id="theme"}</td>
    </tr>
    <tr>
      <td class="red" align="center"> </td>
      <td class="pad_left_small">{$L.phrase_email_field}</td>
      <td>
        <select name="email_field_id" id="email_field_id" disabled>
          <option value="">{$LANG.phrase_please_select_form}</option>
        </select>
      </td>
    </tr>
    <tr>
      <td class="red" align="center">*</td>
      <td class="pad_left_small">{$L.phrase_username_field}</td>
      <td>
        <select name="username_field_id" id="username_field_id" disabled>
          <option value="">{$LANG.phrase_please_select_form}</option>
        </select>
      </td>
    </tr>
    <tr>
      <td class="red" align="center">*</td>
      <td class="pad_left_small">{$L.phrase_password_field}</td>
      <td>
        <select name="password_field_id" id="password_field_id" disabled>
          <option value="">{$LANG.phrase_please_select_form}</option>
        </select>
      </td>
    </tr>
    </table>

    <div class="grey_box">
      <div style="margin_top">
        <a href="#" onclick="return sa_ns.toggle_view_override_settings()">{$L.phrase_view_override_settings_rightarrow}</a>
      </div>

      <div style="display:none" id="view_override_settings">
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
        <tr id="row_1">
          <td>
            <select name="view_override_field_1" id="view_override_field_1" disabled>
              <option value="">{$LANG.phrase_please_select}</option>
            </select>
          </td>
          <td>
            <input type="text" style="width:98%" name="view_override_values_1" id="view_override_values_1" disabled />
          </td>
          <td>
            <select name="view_override_view_1" id="view_override_view_1" disabled>
              <option value="">{$LANG.phrase_please_select}</option>
            </select>
          </td>
          <td class="del"><a href="#" onclick="return sa_ns.delete_row(1)"></a></td>
        </tr>
        </tbody></table>

        <div class="margin_bottom_large">
          <a href="#" onclick="return sa_ns.add_view_override_row()">{$LANG.phrase_add_row}</a>
        </div>
      </div>
    </div>

    <div class="margin_top_large">
      <input type="submit" name="add_form" value="{$LANG.word_add|upper}" />
    </div>

  </form>

{ft_include file='modules_footer.tpl'}
