<?php

$sortable_id = "edit_sa_menu";

if (isset($request["update"]))
{
  $request["tab"] = "menu";
  $request["sortable_id"] = $sortable_id;
  list($g_success, $g_message) = sa_update_submission_account($form_id, $request);
}

$submission_account = sa_get_submission_account($form_id);
$form_fields = ft_get_form_fields($form_id);

// ------------------------------------------------------------------------------------------------

$page_vars = array();
$page_vars["submission_account"] = $submission_account;
$page_vars["sortable_id"] = $sortable_id;
$page_vars["form_id"] = $form_id;
$page_vars["tabs"] = $tabs;
$page_vars["page"] = $page;
$page_vars["form_fields"] = $form_fields;
$page_vars["js_messages"] = array("phrase_please_select", "phrase_please_select_form", "word_na", "word_remove");
$page_vars["head_string"] =<<< END
  <script src="../global/scripts/manage_submission_account.js"></script>
  <script src="../global/scripts/manage_user_menu.js"></script>
  <script src="$g_root_url/global/scripts/sortable.js"></script>
  <link href="../global/css/styles.css" rel="stylesheet" type="text/css"/>
END;

$page_vars["head_js"] =<<< END
$(function() {
  $(".col2 select").live("keyup change", function() {
    var list_order = parseInt($(this).closest(".row_group").find(".sr_order").val(), 10);
    sa_ns.change_page(list_order, this.value);
  });
});

END;

ft_display_module_page("templates/admin/edit.tpl", $page_vars);
