{* component NewsfeedFeedItem *}

<li id="{$item.item_auto_id}" class="newsfeed_item {$item.view.class} {if !empty($item.line)}newsfeed_line_item{/if}" >
	<div class="clearfix">
	    {if empty($item.line)}
	        <div class="newsfeed_avatar">
	            {profile_thumb profile_id=$item.user.id size=40 }
	        </div>
	    {else}
	         <div class="newsfeed_line smallmargin ic_info icon_control">
                {include file="`$this->tpl_dir`line-`$item.entityType`.tpl"}
	         </div>
	    {/if}

	    <div class="newsfeed_body">
       		<div {if $item.cycle.lastSection && $item.cycle.lastItem}style="display: none"{/if} class="newsfeed-item-delim {if !$item.cycle.lastItem}border {/if}newsfeed_delimiter newsfeed_doublesided_stdmargin"></div>
            
	        {if $item.remove }
	            <div class="newsfeed_remove">
	                <a class="lbutton red newsfeed_remove_btn" href="javascript://" rel="{text %are_you_sure}">{text %delete_feed_item_label}</a>
	            </div>
	        {/if}
	        {if empty($item.line)}
				<div class="newsfeed_string smallmargin">
				   <a href="{$item.user.url}" class="newsfeed_item_user"><b>{$item.user.name}</b></a>
                   <span class="newsfeed_item_text">{include file="`$this->tpl_dir`string-`$item.entityType`.tpl"}</span>
				</div>
	        {/if}

			{if $item.content}
                <div class="newsfeed_content small smallmargin">
                    {include file="`$this->tpl_dir`feed-`$item.entityType`.tpl"}
                </div>
			{/if}

            
	        <div class="newsfeed_toolbar small remark">

	            <span class="create_time {$item.view.iconClass}">{$item.createTime}</span>				
                
	            {if $item.comments && $item.comments.allow }
				   <span class="newsfeed_toolbar_space">&middot;</span>
	               <span class="newsfeed_control nowrap">
	                    <a href="javascript://" class="newsfeed_comment_btn">{text %comment_btn_label}</a>
	               </span>
	            {/if}

	            {if $item.likes && $item.likes.allow}
                	<span class="newsfeed_toolbar_space">&middot;</span>
	                <span class="newsfeed_control nowrap">
	                    <a {if $item.likes.liked}style="display: none"{/if} href="javascript://" class="newsfeed_like_btn">{text %like_btn_label}</a>
	                    <a {if !$item.likes.liked}style="display: none"{/if} href="javascript://" class="newsfeed_unlike_btn">{text %unlike_btn_label}</a>
	                </span>
	            {/if}

	            {foreach from=$item.toolbar item=toolbarItem key='toolbarName'}
                    <span class="newsfeed_control nowrap">
                        {include file="`$this->tpl_dir`toolbar-`$toolbarName`.tpl"}
                    </span>
                {/foreach}   
                
				{if $item.comments || $item.likes}	
				<span class="newsfeed_toolbar_space">&middot;</span>
		            <span class="newsfeed_control nowrap newsfeed_counter" {if empty($item.comments.count) && empty($item.likes.count)}style="display: none"{/if}>
                         <span class="cursor_pointer alt2 nowrap newsfeed_feedback_counter newsfeed_features_btn">
                            {if $item.comments}

                                <span class="newsfeed_counter_comments miniic_comment miniicon_control" {if !$item.comments.count}style="display: none"{/if}>{$item.comments.count}</span>
                            {/if}

                            {*if $item.comments && $item.likes}

                                 <span {if $item.comments.count && $item.likes.count}style="display: none"{/if} class="newsfeed_counter_delim">&nbsp;</span>
                            {/if*}

                            {if $item.likes}

                                <span class="newsfeed_counter_likes miniic_heart miniicon_control" {if !$item.likes.count}style="display: none"{/if}>{$item.likes.count}</span>
                            {/if}
		                 </span>
	                </span>
                {/if}            

	        </div>
	        

	        {if $item.likes || $item.comments}
	            <div {if !$item.likes.count && !$item.comments.count || !$item.featuresExpanded}style="display: none"{/if} class="newsfeed-features newsfeed_features">
	            
	            <div class="nf_wrap">
			    <div class="nf_block_cap"><div class="nf_block_cap_r"><div class="nf_block_cap_c"></div></div></div>
			    <div class="nf_block_body"><div class="nf_block_body_r"><div class="nf_block_body_c clearfix">
				    {if $item.likes}
					<div class="newsfeed_likes" {if !$item.likes.count}style="display: none"{/if}>
					    {$item.likes.cmp.markup}
					</div>
				    {/if}

				    <div {if !$item.likes.count || !$item.comments.count}style="display: none"{/if} class="border newsfeed_delimiter" comments="{$item.comments.count}" likes="{$item.likes.count}"></div>

				    {if $item.comments}
					<div class="newsfeed_comments" {if !$item.comments.count}style="display: none;"{/if}>
					    {component $item.comments.cmp}
					</div>
				    {/if}
			    </div></div></div>
			    <div class="nf_block_bot"><div class="nf_block_bot_r"><div class="nf_block_bot_c"></div></div></div>
	            </div>

	            </div>
	        {/if}

            

	    </div>
	</div>
</li>