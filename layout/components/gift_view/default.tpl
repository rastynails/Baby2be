
{canvas}
{container stylesheet='gift_view.style'}
	<div class="center_block wider">
	{capture assign='from'}{if $gift.username}{$gift.username}{else}{text %.label.deleted_member}{/if}{/capture}
	{capture assign='title'}{text %gift_from from=$from}{/capture}
	{block title=$title}
	   <div class="center" style="float: left; padding-right: 10px;">
	       {profile_thumb profile_id=$gift.sender_id size=70}
	       {if $gift.username}<a href="{document_url doc_key='profile' profile_id=$gift.profile_id}">{$from}</a>{else}{$from}{/if}
	   </div>
	   <div style="float: left;">
	   <div class="gift_image">
	       <img src="{$gift.picture}" width="100px" />
	   </div>
	   <div class="gift_info">
           <div class="gift_comment highlight">
               {if $gift.sign_text}{$gift.sign_text|out_format:'mailbox'|censor:'mailbox'}<br />{/if}
           </div>	       
	       <div>
    	       <span class="small">{text %when} {$gift.gift_timestamp|spec_date}<br />
    	       {text %gift_`$gift.privacy_status`}
    	       </span>
	       </div>
	   </div>
	   <br clear="all" />
	   </div>
    {/block}
           {if $owner_mode}
        <div align="center"><input type="button" {id="gift_delete_btn"} value="{text %delete_gift}" /></div>
       {/if}
       <br clear="all" />
    
	</div>
    <br clear="all" />
{/container}
{/canvas}