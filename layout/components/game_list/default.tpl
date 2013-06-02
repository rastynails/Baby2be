
{canvas}
{container stylesheet="game_list.style"}
{if $novel_games}
{block title=%novel}
		{foreach from=$novel_games item='game' name=g}
			<div class="game_cont{if $smarty.foreach.g.iteration is odd} game_odd{/if}">
			{block title=$game.name}
				<div class="game_info">
                    <div class="game_descr">
                    {if $game.description}
                        {$game.description|censor:'game'|truncate:220}
                    {else}
                        {text %.components.game_list.no_description_label}
                    {/if}
                    </div>
					<div class="game_stat small right">
						<a href="{document_url doc_key='novel_game' game_id=$game.id}">{text %play}</a>
					</div>
				</div>

				<div class="clr"></div>
	        {/block}
	        <div class="clr"></div>
	        </div>
        {/foreach}
        <br clear="all" /><br />
{/block}
{/if}

{block}
	{if $no_games}
		<div class="no_content">{text %no_games}</div>
	{else}
		{foreach from=$games item='game' name=g}
			<div class="game_cont{if $smarty.foreach.g.iteration is odd} game_odd{/if}">
			{block title=$game.name}
				<div class="game_info">
                    <div class="game_descr">
                    {if $game.description}
                        {$game.description|truncate:220|censor:'game'}
                    {else}
                        {text %.components.game_list.no_description_label}
                    {/if}
                    </div>
					<div class="game_stat small right">						
						<a href="{document_url doc_key='game' game_id=$game.game_id}">{text %play}</a>
					</div>
				</div>

				<div class="clr"></div>
	        {/block}
	        <div class="clr"></div>
	        </div>
        {/foreach}
        <br clear="all" /><br />        
    {/if}
{/block}
{/container}
{/canvas}