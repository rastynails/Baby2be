{container stylesheet="rss_widget.style"}

<div style="display: none">
    <div {id="form_label"}>{text %form_label}</div>
	<div {id="form"}>
	    {form RssWidget}
	        <table class="form">
	            <tr>
                    <td class="label" style="width: 40%">
                        {label %settings.title for=title}
                    </td>
                    <td class="value" style="width: 60%">
                        {input name=title}
                    </td>
                </tr>
                
	            <tr>
	                <td class="label">
	                    {label %settings.url for=url}
	                </td>
	                <td class="value">
	                    {input name=url}
	                </td>
	            </tr>
	            <tr>
	                <td class="label">
	                    {label %settings.count for=count}
	                </td>
	                <td class="value">
	                    {input name=count}
	                </td>
	            </tr>
	            <tr>
	                <td class="label">
	                    {label %settings.show_desc for=showDesc}
	                </td>
	                <td class="value">
	                    {input name=showDesc}
	                </td>
	            </tr>
	            <tr>
	               <td colspan="2" class="center" style="padding-top: 2px"> 
	                   {button action=save}
	               </td>
	            </tr>
	        </table>
	    {/form}
	</div>
</div>

{block}
    {block_cap title=$settings->title}
        {if $owner}
            <div class="cearfix">
	            <a class="delete_cmp" href="javascript://"></a>
	            {if !$permission}<a {id="edit_cmp"} class="edit_cmp" href="javascript://"></a>{/if}
            </div>
        {/if}
    {/block_cap}
    {if $permission}
        <div class="no_content">
            {$permission}
        </div>
    {else}
	    <ul class="">
	    {foreach from=$rss item=item}
	        <li>
	            <div class="rss_header">
	              <a href="{$item.link}">{$item.title}</a>
	            </div>
	        
	            {if $settings->showDesc}
	                <div class="rss_content">
	                  {$item.description}
	                </div>
	            {/if}
	            
	            <div class="rss_footer small">
                   {if $item.time}
	                {$item.time|spec_date:true}
                   {/if}
	            </div>
	        </li>
	    {foreachelse}
	        <li class="no_content">
	            {text %no_items}
	        </li>
	    {/foreach}
	    </ul>
	 {/if}
    
    
{/block}

{/container}