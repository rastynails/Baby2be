{container}

<div class="title">{text %title}</div>

<div class="content clearfix">

    <div style="width:46%;float:left;padding-bottom:10px;padding-left:3%;">
        <div style="padding:5px;">{text %avaliable_images}</div>
        <div style="height:200px;overflow-y:scroll;">{component $imageList}</div>
    </div>
        
    <div style="width:49%;float:right;">
        <div style="padding:5px;">{text %upload}</div>
            {form TextFormatterImage}
                {input name='label'}<br />
                <div style="display: none;">{label %image for=file}</div>
                {input name='file'}<br />
                <div style="text-align:right;padding:5px;">{button action='add'}
            {/form}
        </div>
   </div>

</div>

{/container}