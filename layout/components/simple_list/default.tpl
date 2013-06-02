{* Component Simple List *}

{container stylesheet="simple_list.style" class="simple_list"}
	<div class="list_cont">
		<ul class="list">
		{foreach from=$items item=id}
			<li class="item">
				{profile_thumb profile_id=$id size=60 username=true}
			</li>
		{/foreach}
		</ul>
	</div>
{/container}