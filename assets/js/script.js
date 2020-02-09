$(document).ready(function() {
	$(document).on("click", ".source", function(event) {
		event.preventDefault();
		$("#resize").removeAttr("style");
		const data = $(this).data();
		if (data.sourceType == "url") {
			$("#local").hide();
			$("#url").show();
		} else {
			$("#url").hide();
			$("#local").show();
		}
	});

	$(document).on("submit", "#submit", function(event) {
		event.preventDefault();
		$.ajax({
			type: "POST",
			url: window.location.href,
			data: new FormData(this),
			dataType: "json",
			contentType: false,
			cache: false,
			processData: false,
			beforeSend: function() {
				$("#loader").show();
			},
			success: function(response) {
				$("#msg")
					.text(response.message)
					.show();
			},
			error: function(response) {
				$("#msg")
					.text(response.responseJSON.message)
					.show();
			},
			complete: function() {
				$("#loader").hide();
			}
		});
	});
});
