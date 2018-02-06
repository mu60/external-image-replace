<div class="wrap">
	<h1>{__("External image replace", "external-image-replace")}</h1>
	<p>{__("Replace the external image in the posted article with the media library at once.", "external-image-replace")}</p>
	{if $match_count == 0}
		<p class="progress_message">
			{__("The corresponding article could not be found.", "external-image-replace")}
		</p>
	{else}
		<h2>{__("Search results", "external-image-replace")}</h2>
		<p>{$match_count} / {$max_count} {__("The applicable article was found.", "external-image-replace")}</p>
		<form method="post" class="search_box">
			<p>
				<input type="hidden" name="action_button" value="replace">
				<button id="replace_start" type="submit" class="button-primary" disabled>
					{__("Replace", "external-image-replace")}
				</button>
			</p>
			<table class="wp-list-table widefat fixed striped posts">
				<thead>
					<tr>
						<td id="cb" class="manage-column column-cb check-column"><input class="all_check" type="checkbox" name="all_check" /></td>
						<th class="manage-column column-title">{__("Title", "external-image-replace")}</th>
						<th class="manage-column column-posts">{__("Number of images", "external-image-replace")}</th>
					</tr>
				</thead>
				<tbody>
					{foreach from=$match_posts item=$single_post}
						<tr>
							<td><input class="post_id" type="checkbox" name="post_id[]" value="{$single_post['post_id']}" /></td>
							<td>{$single_post["post_title"]}</td>
							<td>{count($single_post["img_src"])}</td>
						</tr>
					{/foreach}
				</tbody>
			</table>
		</form>
	{/if}
	<h2>{__("Excluded domain", "external-image-replace")}</h2>
	<p>{__("Please input one line at a time and save before replacing.", "external-image-replace")}</p>
	<form method="post">
		<input type="hidden" name="action_button" value="ignore_config">
		<textarea name="ignore_url" class="ignore_url">{file_get_contents($ignore_path)}</textarea>
		<p><button id="ignore_save" type="submit" class="button-primary">{__("Save", "external-image-replace")}</button></p>
	</form>
</div>
