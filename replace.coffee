jQuery ($) ->
	select_ids = $("input.select_id").val()
	select_ids = select_ids.split(",")

	replace_progress = (select_ids) ->
		this.ajax_process
		this.count = 0
		this.max_posts = Number($("input.max_posts").val())
		this.stop_flag = false
		this.get_time = () ->
			now = new Date()
			hh = ("0"+now.getHours()).slice(-2)
			mm = ("0"+now.getMinutes()).slice(-2)
			ss = ("0"+now.getSeconds()).slice(-2)
			return hh + ":" + mm + ":" + ss
		this.working = (select_id) ->
			$(".message").prepend progress.get_time() + " Post ID " + select_id + " Start.<br />"
			$.ajax
				type: "POST"
				url: external_image_replace_ajax.ajax_url
				data:
					action : "external_image_replace"
					select_id: select_id
			.then(
				(response) ->
					$(".message").prepend progress.get_time() + " " + response + "<br />"
					progress.complete()
				->
					$(".message").prepend progress.get_time() + " <span class='error_message'>Error！</span> Post ID " + select_id + " Processing could not be executed.<br />"
					progress.complete()
			)
		this.complete = ->
			++this.count
			$(".progress_text").text this.count
			if this.count == this.max_posts
				$(".message").prepend "Complete.<br />"
				$("#search_button").prop("disabled", false)
				$("#stop_button").prop("disabled", true)
			else if this.stop_flag == false
				progress.working(select_ids[this.count])
			else
				progress.work_stop()
		this.work_stop = ->
			$(".message").prepend "Quit.<br />"
			$("#search_button").prop("disabled", false)
		return
	progress = new replace_progress(select_ids)
	progress.working(select_ids[0])

	$("#stop_button").click ->
		progress.stop_flag = true
		$(".message").prepend "It is aborted after completion of currently executing processing.<br />"
		$("#stop_button").prop("disabled", true)
