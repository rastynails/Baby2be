{container stylesheet="style.style"}

<div style="display:none;">

<div {id="cap"}>{text %.blogs.image_upload_form_cap}</div>

<div {id="images"} class="clearfix">

<div style="width:46%;float:left;padding-bottom:10px;padding-left:3%;">
<div style="padding:5px;">{text %.blogs.image_upload_cap_images}</div>
<div style="height:200px;overflow-y:scroll;">{component $image_list}</div>
</div>

<div style="width:49%;float:right;">
<div style="padding:5px;">{text %.blogs.image_upload_cap_upload}</div>
{form BlogPostImage}
{input name='label'}<br />
{input name='file'}<br />
<div style="text-align:right;padding:5px;">{button action='blog_post_image'}
{/form}
</div>

</div>

</div>

</div>
{/container}