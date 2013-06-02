{* component SignUp *}

{container stylesheet="sign_up.style" class="sign_up_cont"}
{block title=%label}
	
	{form SignUp}
	
	<div class="field">
		{foreach from=$fields item=field}
		
			{if $field.name=='i_agree_with_tos'}
				{input name=$field.name label=%.profile_fields.label_join.`$field.id`}
			{else}
				{label %.profile_fields.label_join.`$field.id` for=$field.name}
				{input name=$field.name}
			{/if}
		
			
			{if $field.confirm}
				{label %.profile_fields.confirm.`$field.id` for="re_`$field.name`"}<br>
				{input name="re_`$field.name`"}
			{/if}
			
		{/foreach}
	</div>
	<p class="right">
		{button action=$form->active_action label=%.forms._actions.join}
	</p>
	{/form}
{/block}

{/container}
