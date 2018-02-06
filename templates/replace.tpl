<div class="wrap">
	<h1>{__("External image replace", "external-image-replace")}</h1>
	<input type="hidden" class="max_posts" value="{count($select_ids)}" />
	<p>
		<span class="progress_text">0</span> / {count($select_ids)} {__("Processing is completed.", "external-image-replace")}
	</p>
	<input type="hidden" class="select_id" name="select_id" value="{$post_id}" />
	<form method="post">
		<p>
			<button type="submit" id="search_button" class="button-primary" disabled>
				{__("Re-search", "external-image-replace")}
			</button>
		</p>
	</form>
	<p>
		<button id="stop_button" class="button-primary">
			{__("Quit", "external-image-replace")}
		</button>
	</p>
	<p class="message"></p>
</div>
