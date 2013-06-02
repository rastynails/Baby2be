
{canvas}
{container stylesheet="group_list.style"}
	<div class="right clearfix">
		<input type="button" value="{text %create_new}" onclick="window.location='{document_url doc_key='group_add'}'" />
	</div>
    <div class="clearfix smallmargin"></div>

	{if $no_groups}
		<div class="no_content">{text %no_groups}</div>
	{else}
		{foreach from=$groups item='group' name=gr}
			<div class="group_cont{if $smarty.foreach.gr.iteration is odd} group_odd{/if}">
			{block title=$group.title|out_format|truncate:40|censor:'group':true}
				<div class="group_thumb">
					<a href="{document_url doc_key='group' group_id=$group.group_id}">
						<img src="{$group.thumb}" width="90" />
					</a>
				</div>
				<div class="group_info">
					<div class="group_descr">{$group.description|smile|out_format|censor:'group'}</div>
					<div class="group_stat small">
						{text %.label.by} 
						{if $group.profile_id}
							<a href="{document_url doc_key='profile' profile_id=$group.profile_id}">{$group.username|censor:'group'}</a>
						{else}
							{text %.label.deleted_member}
						{/if}	
							, {$group.creation_stamp|spec_date}
					</div>
					<div class="group_stat small left">
						{text %members}: <span class="highlight">{if $group.members_count}{$group.members_count}{else}0{/if}</span> |
						<a href="{document_url doc_key='group' group_id=$group.group_id}">{text %browse}</a>
					</div>
				</div>
				<div class="clr"></div>
	        {/block}
	        <div class="clr"></div>
	        </div>
        {/foreach}
        <br clear="all" /><br />
        {paging total=$paging.total on_page=$paging.on_page pages=$paging.pages}
    {/if}
{/container}
{/canvas}