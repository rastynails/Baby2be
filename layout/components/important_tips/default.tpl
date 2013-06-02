{* component Important Tops *}
 
 {container stylesheet="important_tips.style" class="important_tips"}
	 {if $tips}
		{block title=%label_tips class='tip'}
			<ul>
				{foreach from=$tips item='tip'}
					<li>{$tip}</li>
				{/foreach}
			</ul>
		{/block}
	{/if}	
 {/container} 


