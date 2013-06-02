<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xml:lang="{$language_tag}" lang="{$language_tag}" {if $themeDirection=='rtl'}dir="rtl"{/if} xmlns="http://www.w3.org/1999/xhtml" itemscope itemtype="http://schema.org/">
<head>
	<link rel="shortcut icon" href="{$smarty.const.SITE_URL}favicon.ico" type="image/x-icon">
	<link rel="icon" href="{$smarty.const.SITE_URL}favicon.ico" type="image/x-icon">
	{$html_head}
</head>
<body>
	<div style="display: none;" id="sk-floatbox-block-prototype">
	    <div class="floatbox_container" style="overflow: visible;">
			{block_cap title="" class="floatbox_header"}<a class="close_btn" href="#"></a>{/block_cap}
	    <div class="floatbox_body">
		<div class="fblock_body block_body">
		 <div class="fblock_body_r block_body_r">
		  <div class="fblock_body_c block_body_c">
		  </div>
		 </div>
		</div>
             </div>
	     <div class="floatbox_bottom">	
		<div class="block_bottom">
		 <div class="block_bottom_r">
		   <div class="block_bottom_c">
		   </div>
                  </div>
		  </div>
               </div>
		<div class="fblock_bottom">
			<div class="fblock_bottom_r">
			<div class="fblock_bottom_c"></div>
			</div>
		</div>


		</div>
	</div>
	<div style="display: none" id="sk-hidden-components">
	   {foreach from=$hiddenComponents item=cmp}
	       {$cmp}
	   {/foreach}
	</div>
	{$html_body}
	{$google_code}
</body>
</html>