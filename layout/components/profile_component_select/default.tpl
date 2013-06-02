{container stylesheet="style.style"}
<div class="block_info">
	<div style="float:left;width:60%;">{text %.components.profile_cmp_select.info_block}</div>
    
    <div style="text-align:right;padding:5px;float:right;width:30%;"><input type="button" {id="input"} value="{text %.components.profile_cmp_select.add_button_label}" /></div><div style="clear:both"></div>
</div>
<div {id="profile_cmp_select"} style="display:none;">
	<span class="title">{text %.components.profile_cmp_select.cap_title}</span>
    <span class="content">
		<div class="pvs_cmp_pp_cont">
		{if !$cmps}
		<div class="no_cmp">{text %.components.profile_cmp_select.no_cmp}</div>
		{else}
		<div class="no_cmp" style="display:none;">{text %.components.profile_cmp_select.no_cmp}</div>
		{foreach from=$cmps item='cmp'}
			<div class="pvs_cmp_cont" {id="`$cmp.id`"}>
		    	<div class="pvs_cmp {$cmp.dto->getClass_name()}"></div>
		        <div class="name">{text %.components.profile_cmp_select.cmp_`$cmp.id`}</div>
		    </div>
		{/foreach}
		{/if}
		</div>
		<br clear="all" />
		<div style="text-align:center"><input{if !$cmps} disabled="disabled"{/if} class="pcv_add_cmps" type="button" {id="cmps_submit"} value="{text %.components.profile_cmp_select.add_submit_label}" /></div>
	</span>
</div>
{/container}