
{canvas}
{container}
    <div style="margin: 40px auto 20px; width: 100%; text-align: center;">
        {if $game.code}
            {$game.code}
        {else}
            {$err_message}
        {/if}
    </div>
{if $game.description}
    {block class="block_game_descr"}
        {block_cap title=%.components.game_list.description_label}{/block_cap}
        {$game.description|censor:'game'}
    {/block}
{/if}
{/container}
{/canvas}