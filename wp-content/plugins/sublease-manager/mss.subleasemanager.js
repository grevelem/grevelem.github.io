$(function()
{

	var $modals = $(".modal");
	var $current_tab = $("#Actionable");
	var $current_tab_list_item = $(".tab-list-item.selected");
	$modals.modal({
		show:false
	}).modal('hide');
	$(".manager-tab").hide();
	$(".colorpicker").colorpicker();
	$current_tab.show();
	
	$(".request").click(requestClick);
	
	function requestClick(e)
	{
		var $this = $(this);
		if($this.hasClass("selected"))
		{
			$(this).removeClass("selected");
		}else{
			$(this).addClass("selected");
		}
		
		updateSelected();
	}
	
	$(".num-selected").hide();
	var lastNumSelected = 0;
	function updateSelected()
	{
		var $selected = $(".request.selected");
		var $num_selected = $(".num-selected");
		if($selected.length > 0)
		{
			$num_selected.text($selected.length + " Selected.");
			if(lastNumSelected <= 0)
			{
				$num_selected.fadeIn();
			}
		}else{
			if(lastNumSelected > 0)
			{
				$num_selected.fadeOut();
			}
		}
		
		lastNumSelected = $selected.length;
	}
	
	var options = {
		"delete" : "#OptionDelete",
		"live":"#OptionLive",
		"email":"#OptionEmail",
		"reject":"#OptionReject",
		"review":"#OptionReview",
		"deleterequest":"#OptionDeleteRequest"
	}
	
	for(var o in options)
	{
		var capability = $current_tab_list_item.attr("data-can" + o);
		var found = false;
		if(capability != undefined)
		{
			$(options[o]).show();
			if(!found)
				$(options[o]).addClass("first");
			else
				$(options[o]).removeClass("first");
			found = true;
		}else{
			$(options[o]).hide();
		}
	}
	
	if(typeof(window.localStorage) == undefined)
		$("#OptionUnmarkSelected").hide();
	
	
	$(".tab-list-item").click(function(e)
	{
		var $this = $(this);
		var found = false;
		$current_tab_list_item.removeClass("selected");
		$this.addClass("selected");
		$current_tab_list_item = $this;
		for(var o in options)
		{
			var capability = $this.attr("data-can" + o);
			if(capability != undefined)
			{
				$(options[o]).show();
				if(!found)
					$(options[o]).addClass("first");
				else
					$(options[o]).removeClass("first");
				found = true;
			}else{
				$(options[o]).hide();
			}
		}
		$current_tab.hide();
		
		$current_tab = $($this.attr("data-tab"));
		$current_tab.show();
		

		$(".request.selected").removeClass("selected");
		updateSelected();
	});
	
	function getRequestId($r)
	{
		id = $r.find(".id").text();
		return parseInt(id);
	}
	function getId($r)
	{
		var id = $r.find("input[name=postingId]").val();
		return parseInt(id);
	}
	
	function getSelected()
	{
		var requests = [];
		$(".request.selected").each(function(index, r)
		{
			var $r = $(r);
			requests[index] = getId($r);
		});
		return requests;
	}
	function getSelectedRequests()
	{
		var requests = [];
		$(".request.selected").each(function(index, r)
		{
			var $r = $(r);
			requests[index] = getRequestId($r);
		});
		return requests;
	}
	
	function updateCategory(categoryId)
	{
		var $requests = $(categoryId).find(".request");
		
		if($requests.length == 0)
		{
			$(categoryId).find(".none").fadeIn();
		}else{
			$(categoryId).find(".none").fadeOut();
		}
		updateSelected();
	}
	
	function handle_state_change(data, new_home, old_home)
	{
		if(data.success)
		{
			for(var i = 0; i < data.affected_postings.length; ++i)
			{
				$(".request").each(function(index, r)
				{
					var $r = $(r);
					var id = getId($r);
					if(id == data.affected_postings[i])
					{
						
					
						$r.fadeOut(function()
						{
							$r.remove();
							$(new_home).append($r);
							$r.fadeIn();
							$r.click(requestClick);
							$r.removeClass("selected");
							updateCategory(new_home);
							updateCategory(old_home);
						});
						
						
						
					}
				});
			}
		}else{
			ShowMessage("Error",data.message);
			//alert(data.message);
		}
	}
	function handle_request_delete(data, new_home, old_home)
	{
		if(data.success)
		{
			for(var i = 0; i < data.affected_postings.length; ++i)
			{
				$(".request").each(function(index, r)
				{
					var $r = $(r);
					var id = getRequestId($r);
					if(id == data.affected_postings[i])
					{
						$r.fadeOut(function()
						{
							$r.remove();
							updateCategory(old_home);
						});
						
						
						
					}
				});
			}
		}else{
			ShowMessage("Error",data.message);
		}
	}
	function option_click(new_home, state)
	{
		var requests = getSelected();
		var old_home = "#" + $current_tab.attr("id");
	
		postJSON(
			"/wp-content/plugins/sublease-manager/change_status.php",
			{ postingsIds: JSON.stringify(requests), state:state },
			function(data)
			{
				handle_state_change(data,new_home,"#" + $current_tab.attr("id"));
				$(".loading").removeClass("loading");
				enableBtns();
			}
		);
	}
	
	function postJSON(url, data, success)
	{
		$.ajax({
			  url:url,
			  type:"POST",
			  data:data,
			  dataType:"json",
			  success: success
		});
	}
	function getEmails()
	{
		var emails = JSON.parse((localStorage["marked_emails"] ? localStorage["marked_emails"] : "[]"));
		return emails;
	}
	function setEmails(emails)
	{
		localStorage["marked_emails"] = JSON.stringify(emails);
	}
	
	$("#OptionUnmarkSelected").click(function(e)
	{		
		if($(".request.selected").length <= 0)
		{
			ShowMessage("Error","Please selected one or more requests to unmark.");
			return;
		}
		var emails = getEmails();
		$(".request.selected").each(function(i, r)
		{
			for(var k = 0; k < emails.length; ++k)
			{
				if($(r).children(".id").text() == emails[k].requestId)
				{
					emails.splice(k,1);
					$(r).children(".tag").css({"background-color":"rgba(0,0,0,0)"});
					$(r).children(".email").text("");
				}
			}
		});
		ShowMessage("Success","Unmarked the selected requests.");
		setEmails(emails);
	});
	$("#OptionEmail").click(function(e)
	{		
		$(".email-form").modal('show');
		

	});
	$("#SendEmail").click(function(e)
	{		
		if($(".request.selected").length <= 0)
		{
			ShowMessage("Error","Please selected one or more requests to email.");
			return;
		}
		if($(this).hasClass("loading"))
			return;
		disableBtns();
		$(this).addClass("loading");
		
		var requests = getSelectedRequests();
		var addressTo = $("#SendLinksTo").val();
		if(typeof(window.localStorage) == 'undefined')
			$("#EmailColor").parent().parent().hide();
			
		var color = $("#EmailColor").val();
		if(color == "") color = "none";
		postJSON(
			"/wp-content/plugins/sublease-manager/email.php",
			{ addressTo: addressTo, requestIds: JSON.stringify(requests) },
			function(data)
			  {
					if(data.success)
					{
						if(typeof(window.localStorage) != 'undefined')
						{
							$(".request").each(function(i, e)
							{
								for( var k = 0; k < requests.length; ++k)
								{
									if($(e).children(".id").text() == requests[k])
									{
										$(e).removeClass("selected");
										var emails = getEmails();
										emails.push({ requestId: requests[k], email: addressTo, color: color });
										setEmails(emails);
									}
								}
							});
							readEmailedRequests();
						}
						ShowMessage("Success","Email sent successfully.");
						$(".email").modal('hide');
					}else{
						ShowMessage("Error",data.message);
					}
					enableBtns();
					$(".loading").removeClass("loading");
			  }
		);
	});
	
	function readEmailedRequests()
	{
		if(typeof(window.localStorage) == 'undefined')
			return;
		var emails = getEmails();

		for(var i = 0; i < emails.length; ++i)
		{
			$(".request").each(function(k, e)
			{
				if($(e).children(".id").text() == emails[i].requestId)
				{
					$(e).find(".email.column").html("Emailed to <h5>" + emails[i].email + "</h5>");
					$(e).find(".tag.column").css({"background-color":emails[i].color});
				}
			});
		}
		

	}
	
	readEmailedRequests();
	$confirmation_modal = $(".confirmation");
	var $confirmation_text = $(".confirm-text");
	$confirmation_modal.on('hidden',function()
	{
		$("#ConfirmAction").unbind('click');
	});
	function confirmShow(confirmText, confirmAction)
	{
		$confirmation_text.html(confirmText);
		$confirmation_modal.modal('show');
		$("#ConfirmAction").unbind('click').click(confirmAction);
	}
	function ShowMessage(title, message)
	{
		$modals.modal('hide');
		var $messageModal = $("#Message");
		var $messageModalTitle = $messageModal.find(".modal-title");
		var $messageModalContent = $messageModal.find(".message");
		$messageModalTitle.text(title);
		$messageModalContent.html(message);
		$messageModal.modal('show');
	}
	function disableBtns()
	{
		$(".btn-primary").addClass("disabled");
		$(".btn-primary").prop("disabled",true);
	}
	function enableBtns()
	{
		$(".btn-primary").removeClass("disabled");
		$(".btn-primary").prop("disabled",false);
	}
	
	$("#OptionDelete").click(function(e)
	{		
		if($(".request.selected").length <= 0)
		{
			ShowMessage("Error","Please selected one or more postings to delete.");
			return;
		}
		confirmShow("Are you sure you want to <b>permanently delete</b> these " + $(".request.selected").length + " postings?",function(){
			if($(this).hasClass("loading"))
				return;
			disableBtns();
			$(this).addClass("loading");
			var requests = [];
			$(".request.selected").each(function(index, r)
			{
				var $r = $(r);
				requests[index] = getId($r);
			});
			postJSON(
				"/wp-content/plugins/sublease-manager/change_status.php",
				{ postingsIds: JSON.stringify(requests), state:4 },
				function(data)
				{
					if(data.success)
						ShowMessage("Success","Successfully deleted postings.");
					
					handle_state_change(data,"","#" + $current_tab.attr("id"));
					$(".loading").removeClass("loading");
					enableBtns();
				}
			);
		});
	});
	$("#OptionDeleteRequest").click(function(e)
	{	
		if($(".request.selected").length <= 0)
		{
			ShowMessage("Error","Please selected one or more requests to delete.");
			return;
		}	
		confirmShow("Are you sure you want to <b>permanently delete</b> these " + $(".request.selected").length + " requests?",function(){
			if($(this).hasClass("loading"))
				return;
			disableBtns();
			$(this).addClass("loading");
			var requests = [];
			$(".request.selected").each(function(index, r)
			{
				var $r = $(r);
				requests[index] = getRequestId($r);
			});
			postJSON(
				"/wp-content/plugins/sublease-manager/delete_request.php",
				{ requestIds: JSON.stringify(requests)},
				function(data)
				{					
					if(data.success)
						ShowMessage("Success","Successfully deleted requests.");
					
					handle_request_delete(data,"","#" + $current_tab.attr("id"));
					$(".loading").removeClass("loading");
					enableBtns();
				}
			);
		});
	});
	$("#OptionReject").click(function(e)
	{		
		if($(".request.selected").length <= 0)
		{
			ShowMessage("Error","Please selected one or more requests to reject.");
			return;
		}
		if($(this).hasClass("loading"))
			return;
		disableBtns();
		$(this).addClass("loading");
		option_click("#Rejected",3);
	});

	$("#OptionReview").click(function(e)
	{			
		if($(".request.selected").length <= 0)
		{
			ShowMessage("Error","Please selected one or more requests to review.");
			return;
		}	
		if($(this).hasClass("loading"))
			return;
		disableBtns();
		$(this).addClass("loading");
		option_click("#AwaitingReview",2);
	});
	
	$("#OptionLive").click(function(e)
	{		
		if($(".request.selected").length <= 0)
		{
			ShowMessage("Error","Please selected one or more requests to set live.");
			return;
		}
		if($(this).hasClass("loading"))
			return;
		disableBtns();
		$(this).addClass("loading");
		option_click("#Live",0);
	});

});
