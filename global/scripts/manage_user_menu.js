/**
 * File: manage_menu.js
 *
 * Manages the menu page, letting the administrator construct the user's menu.
 */

// our namespace for manage submission functions
if (typeof sa_ns == 'undefined')
  sa_ns = {};

sa_ns.num_rows = 0;


/**
 * Adds a new menu item row.
 */
sa_ns.add_menu_item_row = function()
{
  var currRow = ++sa_ns.num_rows;

  // get the current table
  var tbody = $("menu_table").getElementsByTagName("tbody")[0];

  var row = document.createElement("tr");
  row.setAttribute("id", "row_" + currRow);

	// [1] Order column
	var td1 = document.createElement("td");
	td1.setAttribute("align", "center");
	var inp = document.createElement("input");
	inp.setAttribute("type", "text");
	inp.style.cssText = "width:30px";
	inp.setAttribute("value", currRow);
	inp.setAttribute("name", "menu_row_" + currRow + "_order");
	inp.setAttribute("id", "menu_row_" + currRow + "_order");
  td1.appendChild(inp);

  // [2] Page
  var td2 = document.createElement("td");
  var pages_dd = $("menu_options").innerHTML;
  pages_dd = pages_dd.replace(/%%X%%/gi, currRow);
  var div = document.createElement("div");
  div.innerHTML = pages_dd;

  // now add the onchange handler to the select field
  for (var i=0; i<div.childNodes.length; i++)
  {
    if (div.childNodes[i].nodeName == "SELECT")
      div.childNodes[i].onchange = function (evt) { sa_ns.change_page(currRow, this.value); }
  }
  td2.appendChild(div);

  // [3] Display column
  var td3 = document.createElement("td");
  var inp = document.createElement("input");
  inp.style.cssText = "width:120px";
  inp.setAttribute("type", "text");
  inp.setAttribute("name", "display_text_" + currRow);
  inp.setAttribute("id", "display_text_" + currRow);
  td3.appendChild(inp);

  // [4] Options column [empty by default]
  var td4 = document.createElement("td");
  var div = document.createElement("div");
  div.setAttribute("id", "row_" + currRow + "_options");
  div.setAttribute("class", "pad_left_small");
  div.setAttribute("class", "nowrap");
  var span = document.createElement("span");
  span.className = "medium_grey";
  span.setAttribute("class", "medium_grey");
  span.appendChild(document.createTextNode(g.messages["word_na"]));
  div.appendChild(span);
  td4.appendChild(div);

  // [5] Is Sub-option
  var td5 = document.createElement("td");
  td5.setAttribute("align", "center");
  var inp = document.createElement("input");
  inp.setAttribute("type", "checkbox");
  inp.setAttribute("name", "submenu_" + currRow);
  inp.setAttribute("id", "submenu_" + currRow);
  td5.appendChild(inp);

  // [6] Delete column
	var td6 = document.createElement("td");
	td6.setAttribute("align", "center");
	td6.setAttribute("class", "del"); // for Mozilla
	td6.className = "del"; // for IE
	var delete_link = document.createElement("a");
	delete_link.setAttribute("href", "#");
	delete_link.onclick = function (evt) { return sa_ns.remove_menu_item_row(currRow); };
	delete_link.appendChild(document.createTextNode(g.messages["word_remove"].toUpperCase()));
	td6.appendChild(delete_link);

  row.appendChild(td1);
  row.appendChild(td2);
  row.appendChild(td3);
  row.appendChild(td4);
  row.appendChild(td5);
  row.appendChild(td6);

  tbody.appendChild(row);

	return false;
}


/**
 * Removes a menu item row.
 */
sa_ns.remove_menu_item_row = function(row)
{
  // get the current table
  var tbody = $("menu_table").getElementsByTagName("tbody")[0];

  for (i=tbody.childNodes.length-1; i>0; i--)
  {
    if (tbody.childNodes[i].id == "row_" + row)
      tbody.removeChild(tbody.childNodes[i]);
  }

  return false;
}


sa_ns.change_page = function(row, page)
{
  // first, if the Display Text field is empty, set its value to the same as the display text
  // in the dropdown menu
  for (var i=0; i<$("page_identifier_" + row).options.length; i++)
  {
    if ($("page_identifier_" + row).options[i].value == page)
    {
      if ($("page_identifier_" + row).options[i].value)
        $("display_text_" + row).value = $("page_identifier_" + row).options[i].text;
      else
        $("display_text_" + row).value = "";
      break;
    }
  }

  // show / hide the appropriate options for this page
  if (page == "custom_url")
    html = "URL:&nbsp;<input type=\"text\" name=\"custom_options_" + row + "\" style=\"width:160px\" />";
  else
    html = g.messages["word_na"];

  $("row_" + row + "_options").innerHTML = html;
}


/**
 * The onsubmit handler for the update client menu form.
 */
sa_ns.update_menu_submit = function(f)
{
  $("num_rows").value = sa_ns.num_rows;
  return true;
}