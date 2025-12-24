function init_school_lead(id, isEdit) {
	if ($("#task-modal").is(":visible")) {
	  $("#task-modal").modal("hide");
	}
	// In case header error
	if (init_school_lead_modal_data(id, undefined, isEdit)) {
	  $("#lead-modal").modal("show");
	}

  // Replace 'leads' with 'school_leads' in the action URL
//   var newAction = currentAction.replace('/leads/', '/school_leads/');

  // Set the updated action to the form
//   $("#lead_form").attr("action", newAction);
  }
  function school_leads_kanban_update(ui, object) {
	if (object !== ui.item.parent()[0]) {
	  return;
	}
  
	var data = {
	  status: $(ui.item.parent()[0]).attr("data-lead-status-id"),
	  leadid: $(ui.item).attr("data-lead-id"),
	  order: [],
	};
  
	$.each($(ui.item).parents(".leads-status").find("li"), function (idx, el) {
	  var id = $(el).attr("data-lead-id");
	  if (id) {
		data.order.push([id, idx + 1]);
	  }
	});
  
	setTimeout(function () {
	   var url = "";
	   if(last_part == 'sl'){
		url = admin_url + "school_leads/update_lead_status?type=sl";
	  }else{
		 url = admin_url + "school_leads/update_lead_status";
	   }
	  $.post(url, data).done(function (
		  response
	  ) {
		update_kan_ban_total_when_moving(ui, data.status);
		school_leads_kanban();
	  });
	}, 200);
  
  }
  function school_lead_mark_as(status_id, lead_id) {
	var data = {};
	data.status = status_id;
	data.leadid = lead_id;
	$.post(admin_url + "school_leads/update_lead_status", data).done(function (
		response
	) {
	  table_leads.DataTable().ajax.reload(null, false);
	});
  }
  function school_leads_kanban() {
	init_kanban(
		"school_leads/kanban",
		school_leads_kanban_update,
		".leads-status",
		290,
		360,
		init_leads_status_sortable
	);
  }
function init_school_lead_modal_data(id, url, isEdit) {
	debugger;
	if(last_part == 'sl'){
	  var requestURL =
		  (typeof url != "undefined" ? url : "leads/lead/") +
		  (typeof id != "undefined" ? (id) : "sl");
	  var concat = "?";
	  if (requestURL.indexOf("?") > -1) {
		concat += "&";
	  }
	  requestURL += concat + "type=sl";
	  if (isEdit === true) {
  
		if (requestURL.indexOf("?") > -1) {
		  concat += "&";
		}
		requestURL += concat + "edit=true&school_table=true";
	  }
	}else{
	  var requestURL =
		  (typeof url != "undefined" ? url : "school_leads/lead/") +
		  (typeof id != "undefined" ? id : "");
	  if (isEdit === true) {
		var concat = "?";
		if (requestURL.indexOf("?") > -1) {
		  concat += "&";
		}
		requestURL += concat + "edit=true";
	  }
	}
  
  
  
  
	requestGetJSON(requestURL)
		.done(function (response) {
		  _lead_init_data(response, id);
		  var currentAction = $("#lead_form").attr("action");
		  var newAction = currentAction.replace('/leads/', '/school_leads/');
          $("#lead_form").attr("action", newAction);
		})
		.fail(function (data) {
		  alert_float("danger", data.responseText);
		});
  }
$(document).ready(function() {
	

	var LeadsServerParams = {
		custom_view: "[name='custom_view']",
		assigned: "[name='view_assigned']",
		status: "[name='view_status[]']",
		source: "[name='view_source']",
	  };
	
	  // Init the table
	  table_leads = $("table.table-school-leads");
	  if (table_leads.length) {
		var tableLeadsConsentHeading = table_leads.find("#th-consent");
		var leadsTableNotSortable = [0];
		var leadsTableNotSearchable = [0, table_leads.find("#th-assigned").index()];
	
		if (tableLeadsConsentHeading.length > 0) {
		  leadsTableNotSortable.push(tableLeadsConsentHeading.index());
		  leadsTableNotSearchable.push(tableLeadsConsentHeading.index());
		}
	
		_table_api = initDataTable(
			table_leads,
			admin_url + "school_leads/table",
			leadsTableNotSearchable,
			leadsTableNotSortable,
			LeadsServerParams,
			[table_leads.find("th.date-created").index(), "desc"]
		);



	  }
});