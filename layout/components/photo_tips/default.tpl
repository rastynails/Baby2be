{* Component Phot Tips *}

{container class="photo_tips" stylesheet="photo_tips.style"}

<div class="float_right show_btn" {if $opened}style="display: none"{/if}><a href="javascript://">{text %show_btn}</a></div>


{capture name=toolbar}<a href="javascript://" class="hide_btn">{text %hide_btn}</a>{/capture}

<br clear="all" />
<br clear="all" />
<div class="tip_container" {if !$opened}style="display: none"{/if}>
{block title=%important toolbar=$smarty.capture.toolbar}
	<div class="float_left narrow">
		<div class="block_info">
		{text %tip_text} <br /><br />
		{text %file_note filesize=$file_size}
		</div>
	</div>
	<div class="float_left wide">
		
			<div class="float_half_left example_bad">
				<div class="center_block">
					<div class="example_txt">{text %bad_photo}</div>
					<div class="float_half_left">
						<div class="image_several"></div>
						<div class="example_txt">{text %several_people}</div>
					</div>
					<div class="float_half_right">
						<div class="image_one"></div>
						<div class="example_txt">{text %too_small}</div>
					</div>
				</div>
			</div>
			<div class="float_half_left example_good">
				<div class="center_block">
					<div class="example_txt">{text %good_photo}</div>
					<div class="float_half_left image_one">
					</div>
					<div class="float_half_right image_several">
					</div>
				</div>
			</div>
			<div class="clr"></div>
		
	</div>
	<div class="clr"></div>
{/block}
</div>

{/container}