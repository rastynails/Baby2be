{* navigation menu type:line *}
{include_style file="menu-line.style"}

{strip}<ul class="menu-{$type}">{foreach from=$items item='item' name='menu'}<li class="line_menu_item"><a href="{$item.href}" {if $item.active}class="active"{/if}>{$item.label|strip}</a>&nbsp;&nbsp;{if !$smarty.foreach.menu.last}|{/if}</li>{/foreach}</ul>{/strip}
