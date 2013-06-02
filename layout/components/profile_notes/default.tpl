
{container stylesheet='profile_notes.style'}
	{capture name='note_title'}
		{text %note_about} {$username}
	{/capture}
	
	{capture name='view_all'}
		<a href="{document_url doc_key='notes_list'}" title="{text %view_title}" >{text %view_all}</a>
	{/capture}
	
	{if $permission_msg}
		{block title=$smarty.capture.note_title}
			<div class="no_content">{$permission_msg}</div>
		{/block}
	{else}
		{block_cap title=$smarty.capture.note_title toolbar=$smarty.capture.view_all}
		{if !$msg}
			<div class="note_controls">
				<div {id="del_note"} class="delete_btn float_right" title='{text %delete}'></div>
				<div {id="edit_note"} class="edit_btn float_right" title='{text %edit}'></div>
			</div>
		{/if}
		{/block_cap}
		{block class="tip"}
			<div {id="note_text"}>
				{if $msg}
					<a href="javascript://" {id="add_note_control"}>{$msg}</a>
				{else}
					{$note.note_text}
				{/if}
			</div>
			<span {id="delete_title"} style="display: none;">{text %confirm_title}</span>
			<span {id="delete_msg"} style="display: none;">{text %confirm_msg}</span>
			<div {id="edit_note_form"} style="display: none;">
				{form EditNote}
					{input name='note'}
					<div class="block_submit right">
						{if $msg}
							{button action='add'}
						{else}
							{button action='update'}
						{/if}
						<input type="button" value="{text %cancel}" {id="cancel_btn"} /></div>
				{/form}
			</div>
		{/block}
	{/if}
{/container}