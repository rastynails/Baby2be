
{container}

{capture name='all_link'}
	{if !$no_topics}<a href="{document_url doc_key='forum' forum_id=$forum_id}">{text %view_all}</a>{/if}
	{if $is_member}| <a href="{document_url doc_key='forum_new_topic' group_id=$group_id}">{text %new_topic}</a>{/if}
{/capture}

{block title=%label toolbar=$smarty.capture.all_link}
{if $no_topics}
	<div class="no_content">{text %no_topics}</div>
{else}
<ul class="thumb_text_list">
	{foreach from=$topics item=topic}
		<li class="item">
			<div class="list_thumb">{profile_thumb profile_id=$topic.profile_id size=60}</div>
			<div class="list_block">
				<div class="list_info">
					<div class="item_title"><a href="{document_url doc_key='topic' topic_id=$topic.forum_topic_id}">{$topic.title|truncate:$title_truncate|censor:'forum':true}</a></div>
					{text %.components.forum_last_topics.posted_by} 
						{if !$topic.is_deleted}<a href="{document_url doc_key='profile' profile_id=$topic.profile_id}">{$topic.username}</a>{else}{text %.label.deleted_member}{/if}, {$topic.create_stamp|spec_date}
				</div>
				<div class="list_content">{$topic.text|smile|out_format:'forum'|truncate:$text_truncate|censor:'forum'}</div>
			</div>
			<div class="clr"></div>
		</li>
	{/foreach}
</ul>
{/if}
{/block}

{/container}