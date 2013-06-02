{* component SignUp *}

{container stylesheet="sign_up.style" class="sign_up_cont"}
{block title=%label}
	
	{form SignUp}
	
	<div class="field">
		{foreach from=$fields item=field}
		
			{if $field.name=='i_agree_with_tos'}
				{input name=$field.name label=%.profile_fields.label_join.`$field.id`}
			{else}
				<div class="signup_label">{label %.profile_fields.label_join.`$field.id` for=$field.name}</div>
				<div class="signup_input">{input name=$field.name}</div>
			{/if}
		
			
			{if $field.confirm}
				<div class="signup_label">{label %.profile_fields.confirm.`$field.id` for="re_`$field.name`"}</div>
				<div class="signup_input">{input name="re_`$field.name`"}</div>
			{/if}
			
		{/foreach}
	</div>
	<p class="right">
		{button action=$form->active_action label=%.forms._actions.join}
	</p>
	{/form}
{/block}

{/container}
