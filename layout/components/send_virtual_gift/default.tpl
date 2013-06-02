
{container stylesheet="send_virtual_gift.style"}

	<a href="javascript://" {id="send_gift_btn"}>{text %gift_btn}</a>
	
	<span style="display: none" {id="send_gift_title"}>{text %gift_title}</span>
	<div style="display: none" {id="gifts_list"}>
	{if $permission_msg}
		<div class="no_content">{$permission_msg}</div>
		<div style="display: none">
		{form SendVirtualGift}
			<div class="center">{button action='send'}</div>
		{/form}
		</div>
	{else}
	   <div>
	   {if $show_cost}<div class="balance">{text %balance credits=$credits_balance}</div>{/if}
		<div class="scrollable">
		<div class="gifts_list">
            {if $by_category}
            <ul class="treeview" id="tree">
            {foreach from=$gifts_list item='category' name='v'}
            {if $category.tpls}
            <li class="{if $smarty.foreach.v.first}collapsable{else}expandable{/if}">
              <div class="hitarea {if $smarty.foreach.v.first}collapsable{else}expandable{/if}-hitarea"></div>
              <span><b>{$category.title}</b></span>
                <ul {if !$smarty.foreach.v.first}style="display: none;"{/if} class="clearfix">
                {foreach from=$category.tpls item='tpl'}
                    <li class="tpl_box">
                        <img src="{$tpl.picture}" width="100" /><br />
                        {if $show_cost && $tpl.credits > 0}
                            <div class="cost">{text %gift_cost cost=$tpl.credits}</div>
                        {/if}
                        <span style="display: none" class="tpl_id">{$tpl.tpl_id}</span>
                    </li>
                {/foreach}
                </ul>
            </li>
            {/if}
            {/foreach}
            </ul>
    		{else}
            {foreach from=$gifts_list item='tpl' name='v'}
                <div class="tpl_box center small">
                    <img src="{$tpl.picture}" width="100" /><br />
                    {if $show_cost && $tpl.credits > 0}
                        <div class="cost">{text %gift_cost cost=$tpl.credits}</div>
                    {/if}  
                    <span style="display: none" class="tpl_id">{$tpl.tpl_id}</span>
                </div>
            {/foreach}
    		{/if}
		</div>
		</div><br />
		{form SendVirtualGift}
            {label for='sign'}:
            {input name='sign' class="area_small"}
            <table>
                <tr><td>{input name='is_private'}</td> <td>{label for='is_private'}</td></tr>
            </table><br />
			<div class="center">{button action='send'}</div>
		{/form}
		</div>
	{/if}
	</div>

<script>
{literal}
function send_gift_fb( recipient_id ){
	
    var $list_content = $("#send_virtual_gift-gifts_list", "#sk-hidden-components").children();
    $list_content.find("input[name=recipient_id]").val(recipient_id);
    window.send_gift_floatbox = new SK_FloatBox({
        $title: $("#send_gift_title", "#sk-hidden-components").text(),
        $contents: $list_content,
        width: 610
    });
};
{/literal}
</script>
	
{/container}