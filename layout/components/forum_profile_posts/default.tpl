{* component Forum Profile Posts *}
{container stylesheet="forum_profile_posts.style"}

{capture name=title}
{text %label username=$username}
{/capture}

{block}
{block_cap title=$smarty.capture.title}
    <a class="delete_cmp" href="javascript://"></a>
{/block_cap}
    {if $posts}	
	<ul class="text_list">
		{foreach from=$posts item=post}
		<li class="item">
			<div class="list_block">
                <div class="list_info">
                    <div class="item_title">{text %in_topic} <a href="{$post.post_url}">{$post.title|out_format:'forum'|censor:'forum':true}</a></div>
                    {$post.create_stamp|spec_date}
                </div>
                <div class="list_content">{$post.text|smile|out_format:'forum'|censor:'forum'}</div>
			</div>
		</li>
		{/foreach}
	</ul>
{else}
	<div class="no_content">{text %no_post}</div>
{/if}
{/block}
{/container}
