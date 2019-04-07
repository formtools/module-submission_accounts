/**
 * File: manage_menu.js
 *
 * Manages the menu page, letting the administrator construct the user's menu.
 */

// our namespace for manage submission functions
if (typeof sa_ns == 'undefined') {
	sa_ns = {};
}
sa_ns.num_rows = 0;


/**
 * Adds a new menu item row.
 */
sa_ns.add_menu_item_row = function () {
	var currRow = ++sa_ns.num_rows;

	var li0 = $("<li class=\"col0\"></li>");
	var li1 = $("<li class=\"col1 sort_col\">" + currRow + "</li>");

	var pages_dd = $("#menu_options").html().replace(/%%X%%/gi, currRow);
	var li2 = $("<li class=\"col2\">" + pages_dd + "</li>");
	var li3 = $("<li class=\"col3\"><input type=\"text\" name=\"display_text_" + currRow + "\" id=\"display_text_" + currRow + "\" /></li>");
	var li4 = $("<li class=\"col4\" id=\"row_" + currRow + "_options\"><span class=\"medium_grey\">" + g.messages["word_na"] + "</span></li>");
	var li5 = $("<li class=\"col5 check_area\"><input type=\"checkbox\" name=\"submenu_" + currRow + "\" id=\"submenu_" + currRow + "\" /></li>");
	var li6 = $("<li class=\"col6 colN del\"></li>");

	var ul = $("<ul></ul>");
	ul.append(li0);
	ul.append(li1);
	ul.append(li2);
	ul.append(li3);
	ul.append(li4);
	ul.append(li5);
	ul.append(li6);

	var main_div = $("<div class=\"row_group\"><input type=\"hidden\" class=\"sr_order\" value=\"" + currRow + "\" /></div>");
	main_div.append(ul);
	main_div.append("<div class=\"clear\"></div>");

	$(".rows").append(sortable_ns.get_sortable_row_markup({ row_group: main_div }));
	sortable_ns.reorder_rows($("#edit_sa_menu"), false);

	return false;
}


/**
 * Removes a menu item row.
 */
sa_ns.remove_menu_item_row = function (row) {
	// get the current table
	var tbody = $("menu_table").getElementsByTagName("tbody")[0];

	for (i = tbody.childNodes.length - 1; i > 0; i--) {
		if (tbody.childNodes[i].id == "row_" + row)
			tbody.removeChild(tbody.childNodes[i]);
	}

	return false;
}


sa_ns.change_page = function (row, page) {
	// first, if the Display Text field is empty, set its value to the same as the display text
	// in the dropdown menu
	var page_identifier = $("#page_identifier_" + row)[0];
	for (var i = 0; i < page_identifier.options.length; i++) {
		if (page_identifier.options[i].value == page) {
			if (page_identifier.options[i].value) {
				$("#display_text_" + row).val(page_identifier.options[i].text);
			} else {
				$("#display_text_" + row).val("");
			}
			break;
		}
	}

	// show / hide the appropriate options for this page
	if (page == "custom_url") {
		html = "URL:&nbsp;<input type=\"text\" name=\"custom_options_" + row + "\" style=\"width:150px\" />";
	} else {
		html = g.messages["word_na"];
	}

	$("#row_" + row + "_options").html(html);
}


/**
 * The onsubmit handler for the update client menu form.
 */
sa_ns.update_menu_submit = function (f) {
	$("num_rows").value = sa_ns.num_rows;
	return true;
}