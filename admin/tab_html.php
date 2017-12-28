<?php

use FormTools\General;
use FormTools\Modules\SubmissionAccounts\Admin;

$submission_account = Admin::getSubmissionAccount($form_id);

$login_url = "../login.php?form_id=$form_id";
$text_html_tab = General::evalSmartyString($L["text_html_tab"], array("LOGINURL" => $login_url));

$page_vars = array(
    "module_settings" => $module->getSettings(),
    "submission_account" => $submission_account,
    "form_id" => $form_id,
    "text_html_tab" => $text_html_tab,
    "tabs" => $tabs,
    "page" => $page,
    "js_files" => array(
        "global/scripts/generate_custom_login_form.js",
        "global/codemirror/js/codemirror.js"
    )
);

$page_vars["head_js"] = <<< END
$(function() {
    setTimeout(function() { lf_ns.generate(); }, 200);
});
END;

$module->displayPage("templates/admin/edit.tpl", $page_vars);
