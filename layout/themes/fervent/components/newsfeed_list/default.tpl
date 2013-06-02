{* component NewsfeedList *}

<li {if count($feed)}style="display: none;"{/if} class="newsfeed_item nocontent newsfeed_nocontent center">{text %empty_feed_message}</li>

{foreach from=$feed item=section name=f_sections}
    {if !$smarty.foreach.f_sections.first}
        <li class="newsfeed_section small smallmargin">
            <span>{$section.date}</span>
        </li>
    {/if}

    {foreach from=$section.list item=item name=f_items}
        {component NewsfeedFeedItem action=$item sharedData=$sharedData lastItem=$smarty.foreach.f_items.last lastSection=$smarty.foreach.f_sections.last}
    {/foreach}
{/foreach}