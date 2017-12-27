var sa_ns = {};
sa_ns.form_fields = {};
sa_ns.num_view_override_rows = 1;
sa_ns.page_type = "add"; // add / edit


/**
 * Called on the Configure (Add) form page, ensuring that all the values are set to their default
 * values & states.
 */
sa_ns.init_configure_form_page = function() {
  if (sa_ns.page_type == "add") {
    $("#form_id").val("");
    $("#view_id,#email_field_id,#username_field_id,#password_field_id,#view_override_field_1,#view_override_values_1,#view_override_view_1").attr("disabled", "disabled");
  } else {
    sa_ns.num_view_override_rows = $("#num_view_override_rows").val();
    if (sa_ns.num_view_override_rows == 0) {
      sa_ns.add_view_override_row();
    }
  }
}


/**
 * Called when the user selects a form from one of the dropdowns in the first column. It shows
 * the appropriate View content in the second column.
 */
sa_ns.select_form = function(form_id) {
  if (form_id == "") {
    $("#view_id")[0].options.length = 0;
    $("#view_id")[0].options[0] = new Option(g.messages["phrase_please_select_form"], "");
    $("#view_id").attr("disabled", "disabled");
    return false;
  } else {
    $("#view_id").attr("disabled", "");
    sa_ns.populate_view_dropdown("view_id", form_id);
  }

  // query the database for the complete list of form fields
  sa_ns.get_form_fields(form_id);
  return false;
}


/**
 * Called whenever the user changes the values in the main View dropdown. If the
 * view isn't set, disable any View override fields.
 */
sa_ns.update_view_override_table_fields = function() {
  var is_disabled = (!$("#view_id").val()) ? true : false;

  for (var i=1; i<=sa_ns.num_view_override_rows; i++) {
    if (!$("#view_override_field_" + i).length) {
      continue;
    }

    $("#view_override_field_" + i + ",#view_override_values_" + i + ",#view_override_view_" + i).attr("disabled", (is_disabled) ? "disabled" : "");

    // update the contents of the views dropdowns for the row
    sa_ns.populate_view_dropdown("view_override_view_" + i, $("#form_id").val());
  }
}


/**
 * Populates a dropdown element with a list of Views including a "Please Select" default
 * option.
 */
sa_ns.populate_view_dropdown = function(element_id, form_id) {
  var form_index = null;
  for (var i=0; i<page_ns.form_views.length; i++) {
    if (form_id == page_ns.form_views[i][0]) {
      form_index = i;
    }
  }

  $("#" + element_id)[0].options.length = 0;
  $("#" + element_id)[0].options[0] = new Option(g.messages["phrase_please_select"], "");

  for (var i=0; i<page_ns.form_views[form_index][1].length; i++) {
    var view_id   = page_ns.form_views[form_index][1][i][0];
    var view_name = page_ns.form_views[form_index][1][i][1];
    $("#" + element_id)[0].options[i+1] = new Option(view_name, view_id);
  }
}


/**
 * Adds a new menu item row.
 */
sa_ns.add_view_override_row = function() {
  var currRow = ++sa_ns.num_view_override_rows;

  var row = document.createElement("tr");
  row.setAttribute("id", "row_" + currRow);

  // [1] Field column
  var td1 = document.createElement("td");
  var sel = document.createElement("select");
  sel.setAttribute("name", "view_override_field_" + currRow);
  sel.setAttribute("id", "view_override_field_" + currRow);
  //sel.appendChild(new Option(g.messages["phrase_please_select"], ""));

  // if the user hasn't yet selected a View, just add a default "Please Select" option and disable the field
  if ($("#view_id").val() == "") {
    sel.setAttribute("disabled", "disabled");
  } else {
    if (sa_ns.page_type == "add") {
      var form_info = sa_ns.form_fields["form_" + $("#form_id").val()];
      var fields    = form_info.fields;

      for (var i=0; i<fields.length; i++) {
        var field_id    = fields[i][0];
        var field_title = fields[i][1]; // .unescapeHTML();
        sel.appendChild(new Option(field_title, field_id));
      }
    } else {
      // just copy the fields found in the email field dropdown
      for (var i=0; i<$("#email_field_id")[0].options.length; i++) {
        sel.appendChild(new Option($("#email_field_id")[0].options[i].text, $("#email_field_id")[0].options[i].value));
      }
    }
  }
  td1.appendChild(sel);

  // [2] has value
  var td2 = document.createElement("td");
  var inp = document.createElement("input");
  inp.style.cssText = "width:98%";
  inp.setAttribute("type", "text");
  inp.setAttribute("name", "view_override_values_" + currRow);
  inp.setAttribute("id", "view_override_values_" + currRow);

  if ($("#view_id").val() == "") {
    inp.setAttribute("disabled", "disabled");
  }
  td2.appendChild(inp);

  // [3] View column
  var td3 = document.createElement("td");
  var sel = document.createElement("select");
  sel.setAttribute("name", "view_override_view_" + currRow);
  sel.setAttribute("id", "view_override_view_" + currRow);
  sel.appendChild(new Option(g.messages["phrase_please_select"], ""));

  if ($("#view_id").val() == "" || (sa_ns.page_type == "add" && $("#form_id").val() == "")) {
    sel.setAttribute("disabled", "disabled");
  } else {
    var form_id = $("#form_id").val();
    var form_index = null;
    for (var i=0; i<page_ns.form_views.length; i++) {
      if (form_id == page_ns.form_views[i][0]) {
        form_index = i;
      }
    }
    for (var i=0; i<page_ns.form_views[form_index][1].length; i++) {
      var view_id   = page_ns.form_views[form_index][1][i][0];
      var view_name = page_ns.form_views[form_index][1][i][1];
      sel.appendChild(new Option(view_name, view_id));
    }
  }
  td3.appendChild(sel);

  // [6] Delete column
  var td4 = document.createElement("td");
  td4.setAttribute("align", "center");
  td4.setAttribute("class", "del"); // for Mozilla
  td4.className = "del"; // for IE
  var delete_link = document.createElement("a");
  delete_link.setAttribute("href", "#");
  delete_link.onclick = function(evt) { return sa_ns.delete_row(currRow); };
  td4.appendChild(delete_link);

  row.appendChild(td1);
  row.appendChild(td2);
  row.appendChild(td3);
  row.appendChild(td4);

  $("#view_override_table tbody").append(row);
  $("#num_view_override_rows").val(sa_ns.num_view_override_rows);

  return false;
}


/**
 * This Ajax function queries the database for a list of form fields - NOT VIEW FIELDS! - to populate the
 * email, username and password dropdowns. It requests the form fields rather than View fields because it's
 * entirely possible the administrator will want the user to be able to sign up using a certain username &
 * password, but not want them to be able to edit those values. Hence: we display ALL form fields, regardless
 * of View.
 *
 * Couple of additional things to note: (a) it shows a "loading" icon while executing, to let the user know
 * something is happening; (b) it stores all View fields in memory in case the user flips back and forth
 * between Forms, eliminating unnecessary database calls.
 */
sa_ns.get_form_fields = function(form_id) {
  if (form_id == "") {
    return false;
  }

  if (typeof sa_ns.form_fields["form_" + form_id] != 'undefined') {
    var form_info = sa_ns.form_fields.get("form_" + form_id);
    if (!form_info.is_loaded) {
      return;
    }
    sa_ns.populate_field_dropdowns(form_id);
  } else {
    // make a note of the fact that we're loading the fields for this form
    sa_ns.form_fields["form_" + form_id] = { is_loaded: false };

    $("#loading_icon").show();
    var url = g.root_url + "/modules/submission_accounts/code/actions.php?action=get_form_fields&form_id=" + form_id;
    $.ajax({
      url:      url,
      type:     "get",
      dataType: "json",
      success:  sa_ns.process_json_field_data,
      error:    ft.error_handler
    });
  }
}


/**
 * This function is passed the result of the database query for the View fields. It populates sa_ns.view_fields
 * with the View field info.
 */
sa_ns.process_json_field_data = function(data) {
  var form_id = data.form_id;

  var form_info = sa_ns.form_fields["form_" + form_id];
  form_info.fields = data.fields;
  form_info.is_loaded = true;
  sa_ns.form_fields["form_" + form_id] = form_info;

  // now, if the form is still selected, update the field list
  var selected_form_id = $("#form_id").val();
  $("#loading_icon").hide();

  if (selected_form_id == form_id) {
    sa_ns.populate_field_dropdowns(form_id);
  }
}


sa_ns.populate_field_dropdowns = function(form_id) {
  var form_info = sa_ns.form_fields["form_" + form_id];
  var fields = form_info.fields;

  $("#email_field_id")[0].disabled = false;
  $("#email_field_id")[0].options.length = 0;
  $("#email_field_id")[0].options[0] = new Option(g.messages["phrase_please_select"], "");
  $("#username_field_id")[0].disabled = false;
  $("#username_field_id")[0].options.length = 0;
  $("#username_field_id")[0].options[0] = new Option(g.messages["phrase_please_select"], "");
  $("#password_field_id")[0].disabled = false;
  $("#password_field_id")[0].options.length = 0;
  $("#password_field_id")[0].options[0] = new Option(g.messages["phrase_please_select"], "");

  // update the View override table field dropdowns, too
  for (var i=1; i<=sa_ns.num_view_override_rows; i++) {
    if (!$("#view_override_field_" + i).length) {
      continue;
    }

    $("#view_override_field_" + i)[0].length = 0;
    $("#view_override_field_" + i)[0].options[0] = new Option(g.messages["phrase_please_select"], "");

    for (var j=0; j<fields.length; j++) {
      var field_id    = fields[j][0];
      var field_title = fields[j][1]; //.unescapeHTML();
      $("#view_override_field_" + i)[0].options[j+1] = new Option(field_title, field_id);
    }
  }

  for (var i=0; i<fields.length; i++) {
    var field_id    = fields[i][0];
    var field_title = fields[i][1];

    $("#email_field_id")[0].options[i+1]    = new Option(field_title, field_id);
    $("#username_field_id")[0].options[i+1] = new Option(field_title, field_id);
    $("#password_field_id")[0].options[i+1] = new Option(field_title, field_id);
  }
}


/**
 * Deletes an individual row. Note that this does NOT re-id all the other fields (e.g. after deleting
 * row 5, row 6 still has an id of row_6) nor does it decrement the global row counter "g_num_rows".
 * This is done for simplicity. The PHP function that handles the update discards any rows without a
 * FORM specified, so the absent row is not important. The num_filters hidden field (which is based on
 * the g_num_rows value) IS important, though - that lets the PHP function know how many rows (or the
 * MAX rows) that the form is sending. So again, it's fine that the actual number of rows passed is less.
 *
 * @param integer row
 */
sa_ns.delete_row = function(row) {
  // get the current table
  var tbody = $("#view_override_table tbody")[0];
  for (var i=tbody.childNodes.length-1; i>0; i--) {
    if (tbody.childNodes[i].id == "row_" + row) {
      tbody.removeChild(tbody.childNodes[i]);
    }
  }
};


sa_ns.toggle_view_override_settings = function() {
  var display_setting = $("#view_override_settings").css("display");

  if (display_setting == 'none') {
    $("#view_override_settings").show();
  } else {
    $("#view_override_settings").hide();
  }
};

