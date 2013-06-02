{canvas}
    {container stylesheet="photo_view.style"}
    {include_style file="carousel.style"}
    
    <div class="phv_header clearfix">
        <div class="float_left">
            {component ProfileStatusView profile_id=$owner_id}
        </div>
        <div class="float_right">
            {component ProfileNotes profile_id=$owner_id}
        </div>
    </div>
    
        <div class="float_half_left wider">
            
                      
            {block id="screen" class="photo_view_block"}
                {if $permission.avaliable}
                    <div class="view_full"><a href="{$photo.fullsize_src}" {id="fullsize_btn"} target="_blank">{text %full_s_btn}</a></div>
                {/if}
                <div class="preview_cont">
                    <div class="preview">
                    {if $permission.avaliable}
                        {if $photo_ver_avaliable && $photo.authed}
                            <div class="photo_auth_mark" style="position: absolute; top: 10px; left: 10px" {id="auth_mark"}>
                                 <img src="{$photo_auth_icon}" />
                            </div>
                        {/if}
                        <a href="{$photo.fullsize_src}" target="_blank">
                            <img src="{$photo.src}" />
                        </a>

                        {if $photo.locked && $photo.publishing_status == "password_protected"}
                            <div class="password">
                                <form>
                                    <input type="password" name="password">
                                    <input type="submit" value="{text %unlock}">
                                </form>
                            </div>
                        {/if}
                    {else}
                        <div class="center no_permited">
                            {$permission.msg}
                        </div>
                    {/if}
                    </div>  
                    <div class="clr"></div>
                </div>
                {if $permission.avaliable}
                    <div style="display:none" class="grey"><div class="jcarousel-item"></div></div>
                    
                    <div class="preloader carousel_preloader"></div>
                    <ul {id="carousel"} class="jcarousel-skin" style="display:none">
                        {foreach from=$photos item=item}
                        <li {if $photo.id==$item.id} class="active_thumb" {/if}>
                            <a href="{$item.fullsize_url}"><img {if $photo.id==$item.id} class="active" {/if} height="50" width="50" src="{$item.thumb_src}" title="{$item.title}" /></a>
                        </li>
                        {/foreach}
                    </ul>
                {/if}
            {/block}
            {if !$photo.locked}
                {component ContentSocialSharing}
                {component $comments}
            {/if}
        </div>
        
        <div class="float_half_right narrower">
        {block title=%details.title}
        
            {if $photo.html_description}
                <div class="description">
                    <p>{text %details.description_label} :</p>
                    {$photo.html_description|censor:"photo"}
                </div>
                <br clear="all" />
            {/if}
            <div class="addet">
                {text %details.added} &nbsp;<a href="{$photo.owner_url}">{$photo.owner_name|truncate:18:"...":true}</a> &nbsp;
                <span class="highlight">{$photo.added|spec_date}</span>
            </div>
            
            <div class="views">
                {text %details.views} &nbsp;
                <span class="highlight">{$photo.views}</span>
            </div>
            {if $photo.comments !== false} 
            <div class="comments">
                {text %details.comments} &nbsp;
                <span class="highlight">{$photo.comments}</span>
            </div>
            {/if}
            <div class="right">{component $report}</div>
        {/block}
        {if !$photo.locked}
		<div {id="rate_cont"}>
			{component $rates}
		</div>
		{if $tagsCmp}
			<div {id="tag_cont"}>
				{component $tagsCmp}
			</div>
		{/if}
        {/if}
        {if $album_id}
            {component SmallPhotoAlbumList profile_id=$owner_id exclude=$album_id count=false title=%photo_albums_title}
        {/if}
        </div>
    {/container}
{/canvas}