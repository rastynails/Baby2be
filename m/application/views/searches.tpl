
{foreach from=$lists item='list'}
	<div class="stdpadding {cycle values='even,odd'}"><a href="{$list->href}">{$list->criterion_name}</a><br /></div>
{/foreach}
<br /><br />