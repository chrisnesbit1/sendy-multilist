(function ($) {
	"use strict";
	$(function () {
		
		var clearMessage = function() {
			$("#sendy-note").css('color', 'inherit').html('');
		};
		
		var errorMessage = function(msg) {
			$("#sendy-note").css('color', 'red').html('<p>' + msg + '</p>');
		};
		
		var statusMessage = function(msg) {
			$("#sendy-note").css('color', 'green').html('<p>' + msg + '</p>');
		};
		
		var formIsValid = function(theform) {
			
			var nameMissing = $("#sendy-name").val() == '';
			var emailMissing = $("#sendy-email").val() == '';
			if (nameMissing || emailMissing) {
				
				if (nameMissing) {
					$("#sendy-name").attr('style', "border-radius: 5px; border:#FF0000 1px solid;");
				}
				
				if (emailMissing) {
					$("#sendy-email").attr('style', "border-radius: 5px; border:#FF0000 1px solid;");
				}
				
				errorMessage('All fields are required');
				return false;
			}
			
			return true;	
		};
		
		var submitForm = function(theform, listid) {
			clearMessage();

	    // this collects all of the data submitted by the form
			var data = {
				name: $('#sendy-name').val(),
				email: $('#sendy-email').val(),
				list: listid,
			};
			
			$.ajax({			   
				type: "POST",
				//url: theform.attr('action'),
				url: '/wp-json/wplikeapro-sendy/v1/subscribe',
				data: data, 
				complete : function(response){
					
					// this sets up our notification area for error / success messages
					$("#sendy-note").ajaxComplete(function(event, request, settings)
					{
						var msg = response.responseText;
						if (msg.substr(0, 4) == "null") {
							msg = msg.substr(0, msg.length - 4); //mysterious "null" at end of response
						}
                        
						if(msg+"" == "1" || msg.toLowerCase() == "subscribed")
						{
							statusMessage("<br/>Thank you for subscribing! A confirmation email has been sent to you - be sure to click the confirmation link.");
							theform.hide();
						}
						else
						{
							errorMessage(msg)
						}
					});					 
				}					 
			});
		};
		
		$("#sendy-name, #sendy-email").change(function(){
			var field = $(this);
			
			if (field.val() == '') {
				field.attr('style', "border-radius: 5px; border:#FF0000 1px solid;");	
			} else {
				field.attr('style', "");	
				clearMessage();
			}
		});
		
		// this is the ID of your FORM tag
		$("#sendy-subscribe-form").submit(function(e) {
      e.preventDefault(); // this disables the submit button so the user stays on the page
	    var theform = $(this);
			
			if (!formIsValid(theform)) {
				return;
			}

			$.each($("input[name='sendy-lists[]']:checked"), function() {
			  var listid = $(this).val();
				submitForm(theform, listid);
			});
	 });
	});
}(jQuery));