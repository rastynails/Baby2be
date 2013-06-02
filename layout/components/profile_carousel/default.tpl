{container stylesheet="style.style"}
<div {id="profile_carousel"}>
{strip}
    {if $prev_bt}
        <a class="carousel_bt profile_carousel_prev" href="{$prev_bt.profile_url}" title="{$prev_bt.title}">{text %.components.profile_view.prev_link}</a><span>|</span>
    {/if}
    <a class="carousel_bt profile_carousel_back" href="{$back_to_list}">{text %.components.profile_view.back_link}</a>
    {if $next_bt}
        <span>|</span><a class="carousel_bt profile_carousel_next" href="{$next_bt.profile_url}" title="{$next_bt.title}">{text %.components.profile_view.next_link}</a>
    {/if}
{/strip}
</div>
<div class="clr_div"></div>
{/container}