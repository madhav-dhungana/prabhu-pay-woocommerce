(function ($) {
	$(document).ready(function ($) {


		var params = new window.URLSearchParams(window.location.search);
		var prabhupay = params.get('prabhupay');
		var order_id = params.get('order_id');
		// Get the modal
		var phonenumbermodal = document.getElementById("eagleeye_phonenumber");

		// Get the <span> element that closes the modal
		var manjul_button_close = document.getElementsByClassName("eagleeye_close")[0];
		manjul_button_close.onclick = function () {
			phonenumbermodal.style.display = "none";
		}

		if (document.getElementById('payment_method_eagleeye_prabhupay').checked && prabhupay === 'pay') {
			//ajax loader
			$(document).ajaxStart(function () {
				$("#eagleeye_prabhupay_loading").show();
			}).ajaxStop(function () {
				$("#eagleeye_prabhupay_loading").hide();
			});
			//eagleeye_prabhupay_phone_modal();
			//open phone number modal
			phonenumbermodal.style.display = "block";

			//ajax that phone number to getotp
			$('#eagleeye_getotp').on('click', function (event) {
				var empt = document.forms["eagleeye_prabhupay_form"]["eagleeye_prabhupay_phone"].value;
				if (empt == "") {
					alert("Please enter your phone number");
					return false;
				}
				else {
					var formdata = $('#eagleeye_prabhupay_form').serialize();
					formdata += '&order_id=' + order_id;
					formdata += '&action=' + 'eagleeye_process_for_otp';
					$.ajax({
						action: 'eagleeye_process_for_otp',
						type: "POST",
						url: ajax_url,
						data: formdata,
						dataType: "json",
						success: function (msg) {
							msg = $.parseJSON(msg.body);
							if (msg.status == "00") {
								manjul_replacefields_with_OTP(msg);
							}
							else {
								alert(msg.message);
								return false;
							}
						}
					});
				}
			})
			function manjul_replacefields_with_OTP(res) {
				//remove old fields
				$("#eagleeye_getotp").remove();
				//res = $.parseJSON(res);
				//add new fields
				var manjul_newfields = '<div class="eagleeye_prabhupay_otp_wrapper">';
				manjul_newfields += "<label for='eagleeye_prabhupay_otp_input'>Enter OTP</label>"
				manjul_newfields += '<input name="eagleeye_prabhupay_otp" type="text" id="eagleeye_prabhupay_otp_input" class="eagleeye_prabhupay_otp_input" placeholder="OTP" />';
				manjul_newfields += '<input type="hidden" name = "transactionId" value="' + res.data.transactionId + '" />';
				manjul_newfields += '<div onclick="checker();" id="eagleeye_verify_otp">Confirm Payment</div>'
				manjul_newfields += '</div';
				$(manjul_newfields).appendTo('#eagleeye_prabhupay_form');

			}
		}
	})


})(jQuery);
function checker() {
	var phone = document.forms["eagleeye_prabhupay_form"]["eagleeye_prabhupay_phone"].value;
	var otp = document.forms["eagleeye_prabhupay_form"]["eagleeye_prabhupay_otp"].value;
	if (phone == "") {
		alert("Please enter your phone number");
		return false;
	}
	if (otp == "") {
		alert("Please enter the OTP");
		return false;
	}
	else {
		var params = new window.URLSearchParams(window.location.search);
		var order_id = params.get('order_id');
		var formdata = jQuery('#eagleeye_prabhupay_form').serialize();
		formdata += '&order_id=' + order_id;
		formdata += '&action=' + 'eagleeye_prabhupay_verify_otp';
		jQuery.ajax({
			action: 'eagleeye_prabhupay_verify_otp',
			type: "POST",
			url: ajax_url,
			data: formdata,
			dataType: "json",
			success: function (msg) {
				if (msg.status === "success") {
					window.location.href = msg.url;
				}
				if (msg.status == "failed" && msg.further === "reOTP") {
					alert(msg.msg);
					return false;
				} else {
					window.location.href = msg.url;
				}
			}
		});
	}
}
