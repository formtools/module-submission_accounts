var sa_ns = {};
sa_ns.form_fields = new Hash();


/**
 * Called on the Configure (Add) form page, ensuring that all the values are set to their default
 * values & states.
 */
sa_ns.init_configure_form_page = function()
{
  $("form_id").value = "";
  $("view_id").disabled = true;
  $("email_field_id").disabled = true;
  $("username_field_id").disabled = true;
  $("password_field_id").disabled = true;
}


/**
 * Called when the user selects a form from one of the dropdowns in the first column. It shows
 * the appropriate View content in the second column.
 */
sa_ns.select_form = function(form_id)
{
  if (form_id == "")
  {
    $("view_id").options.length = 0;
    $("view_id").options[0] = new Option(g.messages["phrase_please_select_form"], "");
    $("view_id").disabled = true;
    return false;
  }

  var form_index = null;
  for (var i=0; i<page_ns.form_views.length; i++)
  {
    if (form_id == page_ns.form_views[i][0])
      form_index = i;
  }

  $("view_id").disabled = false;
  $("view_id").options.length = 0;
  $("view_id").options[0] = new Option(g.messages["phrase_please_select"], "");

  for (var i=0; i<page_ns.form_views[form_index][1].length; i++)
  {
    var view_id   = page_ns.form_views[form_index][1][i][0];
    var view_name = page_ns.form_views[form_index][1][i][1];

    $("view_id").options[i+1] = new Option(view_name, view_id);
  }

  // query the database for the complete list of form fields
  sa_ns.get_form_fields(form_id);

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
sa_ns.get_form_fields = function(form_id)
{
  if (form_id == "")
    return false;

  if (sa_ns.form_fields.get("form_" + form_id))
  {
    // if this form's fields haven't been returned from the server, do nothing: it'll be handled by the (asynchronous)
    // response function
    var form_info = sa_ns.form_fields.get("form_" + form_id);
    if (!form_info.is_loaded)
      return;

    sa_ns.populate_field_dropdowns(form_id);
  }
  else
  {
    // make a note of the fact that we're loading the fields for this form
    sa_ns.form_fields.set("form_" + form_id, { is_loaded: false });

    $("loading_icon").show();
    var url = g.root_url + "/modules/submission_accounts/global/code/actions.php?action=get_form_fields&form_id=" + form_id;

    new Ajax.Request(url, {
      method: "get",
      onSuccess: sa_ns.process_json_field_data,
      onFailure: function() { alert("Couldn't load page: " + url); }
        });
  }
}


/**
 * This function is passed the result of the database query for the View fields. It populates sa_ns.view_fields
 * with the View field info.
 */
sa_ns.process_json_field_data = function(transport)
{
  try {
	  var response = transport.responseText.evalJSON();
	}
	catch (e)
	{
	  alert("Error: " + e);
	  return;
	}

  var form_id = response.form_id;
  var form_info = sa_ns.form_fields.get("form_" + form_id);
  form_info.fields = response.fields;
  form_info.is_loaded = true;
  sa_ns.form_fields.set("form_" + form_id, form_info);

  // now, if the form is still selected, update the field list
  var selected_form_id = $("form_id").value;

  $("loading_icon").hide();

  if (selected_form_id == form_id)
    sa_ns.populate_field_dropdowns(form_id);
}


sa_ns.populate_field_dropdowns = function(form_id)
{
  var form_info = sa_ns.form_fields.get("form_" + form_id);
  var fields = form_info.fields;

  $("email_field_id").disabled = false;
  $("email_field_id").options.length = 0;
  $("email_field_id").options[0] = new Option(g.messages["phrase_please_select"], "");
  $("username_field_id").disabled = false;
  $("username_field_id").options.length = 0;
  $("username_field_id").options[0] = new Option(g.messages["phrase_please_select"], "");
  $("password_field_id").disabled = false;
  $("password_field_id").options.length = 0;
  $("password_field_id").options[0] = new Option(g.messages["phrase_please_select"], "");

  for (var i=0; i<fields.length; i++)
  {
    var field_id    = fields[i][0];
    var field_title = fields[i][1].unescapeHTML();

    $("email_field_id").options[i+1]    = new Option(field_title, field_id);
    $("username_field_id").options[i+1] = new Option(field_title, field_id);
    $("password_field_id").options[i+1] = new Option(field_title, field_id);
  }
}
