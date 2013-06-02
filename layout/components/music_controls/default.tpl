{* Component Music Controls *}

{container}
	<a href="javascript://" {id="delete_control"}>{text %.components.music_controls.labels.delete}</a> |
	<a href="{document_url doc_key='music_edit' music_id=$music_id}">{text %.components.music_controls.labels.edit}</a>
	
	<span style="display: none;" {id="delete_title"}><span>{text %.components.music_controls.confirm_title}</span></span>
	<span style="display: none;" {id="delete_msg"}><span>{text %.components.music_controls.confirm_msg}</span></span>
{/container}