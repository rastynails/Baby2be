{container stylesheet="attachment.style"}
    <div class="attachment_container">
         <div class="attachments_label">{text %label}:</div>
         {foreach from=$list item='attm'}
         <div class="attachment">
             <span class="attachment_icon">&nbsp;</span>
             {if $attm.url != ''}<a href="{$attm.url}">{$attm.label}</a>{else}{$attm.label}{/if} ({$attm.size}Kb)
             
             {if $permissionMode}
                 <a href="javascript://" class="delete_attachment lbutton" sk-attachment-id="{$attm.id}" >delete</a>
             {/if}
         </div>
         {/foreach}
     </div>
{/container}