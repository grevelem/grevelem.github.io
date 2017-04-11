var photoManager = {

	init: function(photoElement, deleteButtonElement)
	{
		$(".group1").colorbox({ rel: 'group1' });
		$(photoElement).each(function(index, e)
		{
			var $photo = $(e);
			
			$photo.data("uploaded",0);
			$photo.data("image","");
			$photo.data("id",index + 1);
			$photo.find(deleteButtonElement).hide();
			if($photo.hasClass("uploaded"))
			{
				$uploadId = $photo.find("input[name=uploadId]");
				$photo.data("uploadId",$uploadId.val());
				$photo.find(deleteButtonElement).show();
				$photo.data("uploaded",1);
				$("input[name=photoUpload" + $photo.data("id") + "]").val($uploadId.val());
				$uploadId.remove()
			}
			
			
            var screenshotFormName = "#ScreenshotForm";
            var screenshotFormSubmit = "#UploadScreenshots";
            var screenshotClass = ".screenshot";

			var base = "/wp-content/themes/myspartansublease/management/";
			
            $('.photo-form').ajaxForm();

			
			$photo.children("input[type=file]").change(function()
			{
				var $p = $(this).parent();
				uploadPhoto($p);
			});
			
			$photo.find(".delete").click(function(event)
			{
				var $this = $(this);
				var $photoToDelete = $this.parent();
				$photoToDelete.addClass("uploading");
				var deleteId = $photoToDelete.data("uploadId");
				var $img = $photoToDelete.find("img");
				$img.animate({ opacity: "0.5" });
				$.ajax(
				{
					type: "POST",
					url: "/wp-content/themes/myspartansublease/management/upload.php",
					data: { delete: true, id: deleteId },
					dataType: 'json',
					success: function(data)
					{
						if(data.success)
						{
							$img.fadeOut(function()
							{
								$img.remove();
							});
							$photoToDelete.removeClass("uploaded");
							$photoToDelete.removeClass("uploading");
							$photoToDelete.find(deleteButtonElement).fadeOut();
							$photoToDelete.data("uploaded",0);
							$("input[name=photoUpload" + $photoToDelete.data("id") + "]").val("");
						}else{
							alert(data.message);
							$img.animate({ opacity: "1" });
							$photoToDelete.removeClass("uploading");
						}
					},
					failure:function()
					{
						$img.animate({ opacity: "1" });
						$photoToDelete.removeClass("uploading");
						alert("There was a problem deleting the photo.");
					}
				});
			
			});
			
			$photo.click(function(event)
			{
				var $this = $(this);
				var id = $this.data("id");
				var uploaded = $this.data("uploaded");
				var image = $this.data("image");
				if(uploaded == 0)
				{
					if(event.target !== $this.children("input[type=file]")[0])
						$this.children("input[type=file]").click();
				}else if(uploaded == 1)
				{
				
				}else{
					event.preventDefault();
				}
				
			});
			
			function uploadPhoto($p)
			{
				
				$p.addClass("uploading");
				$p.parent().ajaxSubmit({
					context: $p,
					dataType: 'json',
					success: function (response, statusText, xhr, $form) {
						var $this = $(this);
						var $imageSpot = $this.find(".image");
						$this.removeClass("uploading");
						if (response.success) {
							var $a = $(jQuery.parseHTML("<a class='group1 cboxElement' href='" + response.path + "'></a>"));
							var $img = $(jQuery.parseHTML("<img data-fileid='" + response.id + "' src='" + response.path + "'/>"));
							$a.append($img);
							$img.hide();
							$imageSpot.append($a);
							$img.fadeIn();
							$this.addClass("uploaded");
							$this.data("uploadId",response.id);
							$this.data("uploaded",1);
							$this.data("image",response.path);
							$this.find(deleteButtonElement).fadeIn();
							$("input[name=photoUpload" + $this.data("id") + "]").val(response.id);
							$(".group1").colorbox({ rel: 'group1' });
						}
						else {
							alert(response.message);
						}
					}
				});
			}
		});
	}

};

$(function()
{

	var $modals = $(".modal");
	var $checklist = $(".checklist");
	var $confirm = $(".confirm");
	var $submitFinal = $("#SubmitRequestFinal");
	var $submit = $("#SubmitRequest");
	var $form = $("#FormCreatePosting");
	
	var $confirmation_modal = $(".confirmation");
	var $confirmation_text = $(".confirm-text");
	var $approve = $(".Approve");
	var $reject = $(".Reject");
	
	var $state = $("input[name=state]");
	var $readonly = $("input[name=readonly]");
	var $isAdmin = $("input[name=admin]");
	
	if($isAdmin.val() != 1)
		$("#adminNav").hide();
	else
		$("#adminNav").sticky();
	
	var submitting = false;
	
	setupForm();

	$("button").click(function(e)
	{
		
		var href = $(this).attr("href");
		if(href != undefined)
		{
			e.preventDefault();
			window.open(href);
		}
	});
	
	function onSubmitBegin()
	{
		submitting = true;
		$("button[data-dismiss=modal]").prop("disabled",true);
		$("button[data-dismiss=modal]").addClass("disabled");
	}
	function onSubmitFinish()
	{
		submitting = false;
		$("button[data-dismiss=modal]").removeClass("disabled");
		$("button[data-dismiss=modal]").prop("disabled",false);
	}
	
	
	function readState()
	{
		var state = $state.val();
		var readonly = $readonly.val();
		var admin = $isAdmin.val();
		$(".state-live, .state-review, .state-rejected, .state-default, .state-readonly")
			.slideUp();
		
		switch(state)
		{
			case "0":
				$(".state-live").slideDown();
				break;
			case "2": 
				$(".state-review").slideDown();
				break;
			case "3": 
				$(".state-rejected").slideDown();
				break;
			default:
				$(".state-default").slideDown();
				break;
		}
		if(readonly == "1")
		{
			$(".state-readonly").slideDown();
			disableInputs();
			
		}else{
			$(".state-readonly").slideUp();
			enableInputs();
		}
		
		if(admin == "0")
			$(".admin-only").hide();
	}
	
	function disableInputs()
	{
		$("input").addClass("disabled");
		$("input").prop("disabled",true);
		$(".delete").hide();
		$("label.btn-primary").addClass("disabled");
	}
	function enableInputs()
	{
		$("input").removeClass("disabled");
		$("input").prop("disabled",false);
		$(".delete").fadeIn();
		$("label.btn-primary").removeClass("disabled");
	}
	
	function setupForm()
	{
		$form.validate({
			rules: {
				email: {
					required: true,
					email: true
				}
			}
		});
	
		$("label.active").each(function(index, element)
		{
			$(element).children("input").prop('checked', true);
		});
		$(".datepicker").datepicker();
		$modals.modal({
			show:false,
			backdrop: 'static',
			keyboard: false
		}).on('hidden',function()
		{
			if(submitting)
				e.preventDefault();
		});
		$submit.click(onShowRequest);
		
		$confirm.parent().click(onChecked);
		$submitFinal.click(onSubmitFinal);
		
		$("input[name=contactNumber]").mask('(000) 000-0000');
		
		$confirmation_modal.on('hidden',function()
		{
			$("#ConfirmAction").unbind('click');
		});
		$approve.click(function(e)
		{
			e.preventDefault();
			confirmShow(
			"Are you sure you want to <b>Approve</b> this posting? It " +
			"will go live on the site immediately.",
			onApprove
			);
		});
		$reject.click(function(e)
		{
			e.preventDefault();
			confirmShow(
			"Are you sure you want to <b>Reject</b> this posting? It " +
			"will be placed in the rejected category for further review " +
			"or modification immediately.",
			onReject
			);
		});
		photoManager.init(".sublease-image",".delete");
		$("input[name=address]").focus(function()
		{
			$(this).siblings(".warning").remove();
		});
		$("input[name=address]").blur(function()
		{
			checkAddress(function(){ });
		});
		readState();
	}
	
	function setupConfirmations()
	{
		$confirm.parent().each(function(index, element)
		{
			$(element).removeClass("active");
		});
		$confirm.prop('checked',false);
		toggleSubmitFinal();
	}
	
	function checkConfirmations(totalToBeChecked)
	{
		var numChecked = 0;
	
		$confirm.each(function(index, element)
		{
			if($(element).is(":checked"))
			{
				numChecked++;
			}
		});
		
		return numChecked == totalToBeChecked;
	}
	
	function toggleSubmitFinal()
	{
		if(checkConfirmations($confirm.length))
		{
			$("#SubmitRequestFinal").removeClass("disabled");
		}else if(!$("#SubmitRequestFinal").hasClass("disabled"))
		{
			$("#SubmitRequestFinal").addClass("disabled");
		}
	}
	
	function checkPhotos()
	{
		for(var i = 0; i < 4; ++i)
		{
			if($("input[name=photoUpload" + i + "]").val() == "")
				return false;
		}
		return true;
	}
	
	function checkAddress(onFinished)
	{
		var $address = $("input[name=address]");
		waitForCodeAddress(null, $address.val() + " East Lansing, MI", function(success, data)
		{
			$address.siblings(".warning").remove();
			if(!success)
			{
				$address.after("<label class='warning'>Warning: This address cannot be found by Google Maps.</label>");
			}
			onFinished();
		});
	}
	
	function onShowRequest(e)
	{
		e.preventDefault();
		
		var error = false;
		
		checkAddress(function()
		{
			if(!$form.valid())
			{
				error = true;
			}
			if(!checkPhotos())
			{
				$(".photosDesc").addClass("error");
				error = true;
			}else{
				$(".photosDesc").removeClass("error");
			}
			
			if(error)
				return;
			$checklist.modal('show');
			setupConfirmations();
		});

	}
	
	function onChecked(e)
	{
		var $this = $(this);
		if(!$this.find("input").is(":checked"))
		{
			$this.find("input").prop("checked",true);
		}else{
			$this.find("input").prop("checked",false);
		}
		e.preventDefault();
		toggleSubmitFinal();
	}
	
	function onSubmitFinal(e)
	{
		if(submitting) return;
		onSubmitBegin();
		e.preventDefault();
		
		var googleMapsData = {};

		$("#SubmitRequestFinal").addClass("loading");
		waitForCodeAddress(null, $("input[name=address]").val()  + " East Lansing, MI", function(success, data)
		{
			if(success)
			{
				googleMapsData.LatLng = data;
			}
			$("#FormCreatePosting").ajaxSubmit({
				dataType: 'json',
				data: { semesterFall: $("input[name=semesterFall]").is(":checked")
				, semesterSummer: $("input[name=semesterSummer]").is(":checked")
				, semesterSpring: $("input[name=semesterSpring]").is(":checked")
				, googleMapsData: JSON.stringify(googleMapsData)
				},
				success: function (response, statusText, xhr, $form) {
					onSubmitFinish();
					if (response.success) {
						$state.val(response.state);
						$readonly.val(response.readonly);
						$(jQuery.parseHTML("<input type='hidden' name='postingId' value='" + response.postingId + "'/>")).appendTo("body");
						readState();
						$checklist.modal('hide');
					}
					else {
						alert(response.message);
					}
					$("#SubmitRequestFinal").removeClass("loading");
				},
				error: function()
				{
					alert("Your request could not be completed. If this problem persists, please notify a site admin.");
					$("#ConfirmAction").removeClass("loading");
				}
			});
		});

		
	}
	
	function confirmShow(confirmText, confirmAction)
	{
		$confirmation_text.html(confirmText);
		$confirmation_modal.modal('show');
		$("#ConfirmAction").unbind('click').click(confirmAction);
	}
	
	function onApprove(e)
	{
		if(submitting) return;
		var postingIds = new Array();
		postingIds[0] = parseInt($("input[name=postingId]").val());
		onSubmitBegin();
		$("#ConfirmAction").addClass("loading");
		$.ajax({
			  url:"/wp-content/plugins/sublease-manager/go_live.php",
			  type:"POST",
			  data:{ postingsIds: JSON.stringify(postingIds) },
			  dataType:"json",
			  success: function(data)
			  {
				onSubmitFinish();
				if(data.success)
				{
					$state.val(data.state);
					$readonly.val(data.readonly);
					readState();
					$confirmation_modal.modal('hide');
				}else{
					alert(data.message);
				}
				$("#ConfirmAction").removeClass("loading");
			  },
			  failure: function()
			  {
				alert("Your request could not be completed. If this problem persists, please notify a site admin.");
				$("#ConfirmAction").removeClass("loading");
			  }
		});
	}
		
	function onReject(e)
	{
		if(submitting) return;
		var postingIds = new Array();
		postingIds[0] = parseInt($("input[name=postingId]").val());
		onSubmitBegin();
		$("#ConfirmAction").addClass("loading");
		$.ajax({
			url:"/wp-content/plugins/sublease-manager/reject.php",
			type:"POST",
			data:{ postingsIds: JSON.stringify(postingIds) },
			dataType:"json",
				
			success: function(data)
			{
				onSubmitFinish();
				if(data.success)
				{
					$state.val(data.state);
					$readonly.val(data.readonly);
					readState();
					$confirmation_modal.modal('hide');
				}else{
					alert(data.message);
				}
				$("#ConfirmAction").removeClass("loading");
			},
			failure: function()
			{
				alert("Your request could not be completed. If this problem persists, please notify a site admin.");
				$("#ConfirmAction").removeClass("loading");
			}
		});
	}
});
