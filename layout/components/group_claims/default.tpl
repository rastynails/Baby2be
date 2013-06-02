
{canvas}
    {container}
        {if isset($error)}
            <div class="no_content">{$error}</div>
        {elseif $claims}
            {component $claims}
        {/if}
    {/container}
{/canvas}