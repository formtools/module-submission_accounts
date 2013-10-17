<?php

$submission_account = sa_get_submission_account($form_id);
$module_settings = ft_get_module_settings("", "submission_accounts");

$login_url = "../login.php?form_id=$form_id";
$text_html_tab = ft_eval_smarty_string($L["text_html_tab"], array("LOGINURL" => $login_url));

// ------------------------------------------------------------------------------------------------

$page_vars = array();
$page_vars["module_settings"] = $module_settings;
$page_vars["submission_account"] = $submission_account;
$page_vars["form_id"] = $form_id;
$page_vars["text_html_tab"] = $text_html_tab;
$page_vars["tabs"] = $tabs;
$page_vars["page"] = $page;
$page_vars["head_string"] =<<< END
  <script src="../global/scripts/generate_custom_login_form.js"></script>
  <script src="$g_root_url/global/codemirror/js/codemirror.js"></script>
END;
$page_vars["head_js"] =<<< END
$(function() {
  setTimeout(function() { lf_ns.generate(); }, 200);
});
END;

ft_display_module_page("templates/admin/edit.tpl", $page_vars);