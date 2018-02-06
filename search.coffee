jQuery ($) ->
	$("input.all_check").click ->
		$("input.post_id").prop("checked", $("input.all_check").prop("checked"))
		$("#replace_start").prop("disabled", !$("input.all_check").prop("checked"))
	$("input.post_id").click ->
		max_count = $("input.post_id").length
		check_count = $("input.post_id:checked").length
		disabled = true
		all_check = false
		if check_count > 0
			disabled = false
		if check_count == max_count
			all_check = true
		$("input.all_check").prop("checked", all_check)
		$("#replace_start").prop("disabled", disabled)
	$("form").submit ->
		$("button[type='submit']").prop("disabled", true)
