function changeCountry() {
	var $city, $obj;

	$city = $("select[name='city']");
	$obj  = $("select[name='country']");

	$city.attr('disabled', true);
	$city.empty();

	if ($obj.val() !== "") {
		$obj.attr('disabled', true);
		$.ajax({
			url: PATH + "/configuration/world/",
			type: "POST",
			dataType: "json",
			data: {country: $obj.val()},
			success: function (data) {
				$.each(data, function(val, text) {
				    $city.append(new Option(text, val));
				});

				$city.attr('disabled', false);
				$obj.attr('disabled', false);
			},
			error: function () {
				alert("An error has occurred");
			}
		});
	}
} changeCountry();