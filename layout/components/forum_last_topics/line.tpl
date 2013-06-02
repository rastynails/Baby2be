{* component Forum Last Topics *}

{container stylesheet="forum_last_topics.style"}

<div class="type_line">

{*text %label*}
{block title=%label toolbar=$smarty.capture.goto_link}
{if $topics}
	<ul class="thumb_text_list clearfix">
	    {foreach from=$topics item=topic}
	        <li class="item">
	            <div class="list_thumb">{profile_thumb profile_id=$topic.profile_id size=60}</div>
	            <div class="list_block">
	                <div class="list_info">
	                    <div class="item_title"><a href="{document_url doc_key='topic' topic_id=$topic.forum_topic_id}">{$topic.title|out_format:"comment"|censor:'forum':true}</a></div>
	                    {text %posted_by} 
	                        {if !$topic.is_deleted}<a href="{document_url doc_key='profile' profile_id=$topic.profile_id}">{$topic.username}</a>
	                        {else}{text %.label.deleted_member}{/if}, {$topic.create_stamp|spec_date}
	                </div>
	                <div class="list_content">{$topic.text|out_format:"comment"|smile|censor:'forum'}</div>
	            </div>
	            <div class="clr"></div>
	        </li>
	    {/foreach}
	</ul>
{else}
    <div class="no_content">{text %forum_empty}</div>
{/if}
{/block}
</div>

{/container}
