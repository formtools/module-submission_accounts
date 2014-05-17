  {include file="messages.tpl"}

  <div class="margin_bottom_large">
    {$L.text_html_tab}
  </div>

  <input type="hidden" id="form_id" value="{$form_id}" />
  <input type="hidden" id="username_field_label" value="{$module_settings.username_field_label|escape}" />
  <input type="hidden" id="password_field_label" value="{$module_settings.password_field_label|escape}" />
  <input type="hidden" id="login_button_label" value="{$module_settings.login_button_label|escape}" />

  <table cellspacing="0" cellpadding="0" width="100%" class="margin_bottom">
  <tr>
    <td nowrap width="30"><input type="checkbox" id="use_redirect_url" onchange="lf_ns.generate()" /></td>
    <td nowrap width="50"><label class="pad_right" for="use_redirect_url">{$L.phrase_redirect_failed_logins_c}</label></td>
    <td><input type="text" style="width:100%" id="redirect_url" onkeyup="lf_ns.generate()" /></td>
  </tr>
  <tr>
    <td>
      <input type="checkbox" id="include_forget_password_link" {if $submission_account.email_field_id == ""}disabled{/if}
        onchange="lf_ns.generate()" />
    </td>
    <td colspan="2">
      <label for="include_forget_password_link">
        {$L.text_include_forget_password}
      </label>
    </td>
  </tr>
  </table>

  <div style="border: 1px solid #666666; padding: 3px">
    <textarea name="html" id="html" style="width:100%; height:200px"></textarea>
  </div>

  <script type="text/javascript">
  var html_editor = new CodeMirror.fromTextArea("html", {literal}{{/literal}
  parserfile: ["parsexml.js"],
  path: "{$g_root_url}/global/codemirror/js/",
  stylesheet: "{$g_root_url}/global/codemirror/css/xmlcolors.css"
  {literal}});{/literal}

  {literal}setTimeout(function() { lf_ns.generate()}, 100);{/literal}
  </script>

