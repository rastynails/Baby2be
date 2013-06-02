{* Component Email Verify *}

{canvas}

	{container}
		{block class="center_block"}
			{block class="block_info"}
			
				{text %msg}
			
			{/block}
			<div class="center_block">
				{block title=%send_label class="center"}
					{form EmailVerify}
						{input name=email}{button action=send}
					{/form}
				{/block}
			</div>
			
		{/block}
	{/container}

{/canvas}