
{container stylesheet='hot_list.style'}
{block title=%title}
<div class="clearfix">
	{foreach from=$hot_list item='item'}
	<div class="float_left {cycle values='hot_even,hot_odd'}">
		{profile_thumb profile_id=$item.profile_id size=70}
	</div>
	{/foreach}
</div>
   <div class="center">
   {if $use_sms}
       <a href="{document_url doc_key='sms_services'}?service=hot_list">{text %join_link}</a>
   {else}
       <a href="javascript://" {id="become_hot"}>{text %join_link}</a>
   {/if}
   </div>

{/block}
{/container}