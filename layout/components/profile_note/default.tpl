{canvas blank}

{container stylesheet="style.style"}
<div class='content_white'>
{form EditNote}
    <div class="ddbox">
		

	{capture name='note_title'}
		{text %.components.profile_notes.note_about} {$username}
	{/capture}
	
	{capture name='view_all'}
		<a href="{document_url doc_key='notes_list'}" title="{text %.components.profile_notes.view_title}" target="_blank" >{text %.components.profile_notes.view_all}</a>
	{/capture}

	{if $permission_msg}
		{block title=$smarty.capture.note_title}
			<div class="no_content">{$permission_msg}</div>
        {/block}
	{else}
		{block_cap title=$smarty.capture.note_title toolbar=$smarty.capture.view_all}{/block_cap}
		{block class="tip"}
			<div {id="edit_note_form"}>
                <textarea name="note" id="note">{$note}</textarea>
            </div>
        {/block}
	{/if}

		
		<div style="text-align:center">
			<input class="pcv_add_cmps" type="button" {id="esd_reference_bookmark"} value="{text %.components.event.speed_dating.remember_profile}" />
			<input class="pcv_add_cmps" type="button" {id="esd_profile_note_close"} value="{text %.components.event.speed_dating.esd_profile_note_close_label}" />
		</div>
    
{/form}
    </div>
</div>
{/container}

{/canvas}