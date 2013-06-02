{container stylesheet="style.style"}

{foreach from=$images item='image'}
<a href="javascript://" {id="image_link_`$image.id`"}>{$image.label}</a>&nbsp;&nbsp;<a {id="del_image_`$image.id`"} href="javascript://" style="color:red;font-weight:bold;">X</a><br />
{/foreach}

{/container}