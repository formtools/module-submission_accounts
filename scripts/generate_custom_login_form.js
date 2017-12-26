/**
 * This page contains the code for dynamically generating the custom login forms for each Submission Account. It's
 * called from the "Custom Login" tab of the Edit Submission Account pages.
 */

var lf_ns = {};


/**
 * Called on the Configure (Add) form page, ensuring that all the values are set to their default
 * values & states.
 */
lf_ns.generate = function() {
  var html = "<form action=\"" + g.root_url + "/modules/submission_accounts/login.php\" method=\"post\">\n"
    + "  <input type=\"hidden\" name=\"form_id\" value=\"" + $("#form_id").val() + "\" />\n";

  if ($("#use_redirect_url").attr("checked") && $("#redirect_url").val() != "")
    html += "  <input type=\"hidden\" name=\"invalid_login_redirect_url\" value=\"" + $("#redirect_url").val() + "\" />\n";

  html += "  ";
  if ($("#username_field_label").val())
    html += $("#username_field_label").val() + " ";
  html += "<input type=\"text\" name=\"username\" /><br />\n"

  html += "  ";
  if ($("#password_field_label").val())
    html += $("#password_field_label").val() + " ";
  html += "<input type=\"text\" name=\"password\" /><br />\n";

  html += "  <input type=\"submit\" name=\"login\" value=\"" + $("#login_button_label").val() + "\" /><br />\n";

  if ($("#include_forget_password_link").attr("checked"))
    html += "  <a href=\"" + g.root_url + "/modules/submission_accounts/forget_password.php\">Forget your password?</a><br />\n";

  html += "</form>";

  html_editor.setCode(html);
}

