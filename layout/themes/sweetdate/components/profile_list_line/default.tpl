{container stylesheet="profile_list_line.style"}

<ul class="pllist_cont clearfix">
    {foreach from=$items item=pid}
    <li class="plitem">
        {profile_thumb profile_id=$pid size=90 username=true}
    </li>
    {/foreach}
</ul>

{*<a href="{$viewAllUrl}">View All</a>*}

{/container}