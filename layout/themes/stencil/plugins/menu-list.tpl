{* navigation menu type:list *}

{include_style file="menu-list.style"}

{strip}<ul class="menu-{$type}">{foreach from=$items item='item' name='menu'}<li class="item"><a {if $item.active}class="active"{/if} href="{$item.href}">{$item.label|strip}</a>&nbsp;&nbsp;{if !$smarty.foreach.menu.last}|{/if}</li>{if  $item.active && $item.sub_menu}<br><br>{include file=$tpl_self items=$item.sub_menu}{/if}{/foreach}</ul>{/strip}