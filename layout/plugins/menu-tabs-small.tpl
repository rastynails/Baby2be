{* navigation menu type:tabs-small *}

{include_style file="menu-tabs-small.style"}

<ul class="menu-tabs-small">{foreach from=$items item='item' name='item'}<li class="tab {if $smarty.foreach.item.first}first{/if} {if $smarty.foreach.item.last}last{/if}"><a {if $item.active}class="active"{/if} href="{$item.href}">{$item.label|strip}</a></li>{/foreach}</ul>
