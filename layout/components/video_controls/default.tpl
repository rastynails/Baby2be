{* Component Video Controls *}

{container}
	<a href="javascript://" {id="delete_control"}>{text %.components.video_controls.labels.delete}</a> |
	<a href="{document_url doc_key='video_edit' video_id=$video_id}">{text %.components.video_controls.labels.edit}</a>
	
	<span style="display: none;" {id="delete_title"}><span>{text %.components.video_controls.confirm_title}</span></span>
	<span style="display: none;" {id="delete_msg"}><span>{text %.components.video_controls.confirm_msg}</span></span>
{/container}