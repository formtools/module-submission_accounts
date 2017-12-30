<?php

use FormTools\Core;
use FormTools\General;
use FormTools\Modules\SubmissionAccounts\Admin;

$root_url = Core::getRootUrl();
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
        "$root_url/modules/submission_accounts/scripts/generate_custom_login_form.js",
        "$root_url/global/codemirror/lib/codemirror.js",
        "$root_url/global/codemirror/mode/xml/xml.js",
        "$root_url/global/codemirror/mode/smarty/smarty.js",
        "$root_url/global/codemirror/mode/php/php.js",
        "$root_url/global/codemirror/mode/htmlmixed/htmlmixed.js",
        "$root_url/global/codemirror/mode/css/css.js",
        "$root_url/global/codemirror/mode/javascript/javascript.js",
        "$root_url/global/codemirror/mode/clike/clike.js"
    ),
    "css_files" => array(
        "$root_url/global/codemirror/lib/codemirror.css"
    )
);

$page_vars["head_js"] = <<< END
$(function() {
    setTimeout(function() {
        lf_ns.generate();
    }, 200);
});
END;

$module->displayPage("templates/admin/edit.tpl", $page_vars);
