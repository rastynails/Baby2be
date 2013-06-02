{* component SignUp *}

{container stylesheet="sign_up.style" class="sign_up_cont"}
{block title=%label}
	
	{form SignUp}
	
	<div class="field clearfix">
		<div class="float_left">
		{foreach from=$fields item=field name="f"}

			{if $field.name=='i_agree_with_tos'}
			<div class="float_left field_agree">
				{input name=$field.name label=%.profile_fields.label_join.`$field.id`}
			</div>
			{else}
				{label %.profile_fields.label_join.`$field.id` for=$field.name}
				<div class="input_bg">{input name=$field.name}</div>
			{/if}
		
			
			{if $field.confirm}
				{label %.profile_fields.confirm.`$field.id` for="re_`$field.name`"}<br>
				<div class="input_bg">{input name="re_`$field.name`"}</div>
			{/if}
			
		{if $smarty.foreach.f.iteration == 2}
			</div>
			<div class="float_right">
		{/if}
		{/foreach}
			<p class="right float_right">
				{button action=$form->active_action label=%.forms._actions.join}
			</p>
		</div>
	</div>
	{/form}
{/block}

{/container}
