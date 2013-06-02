{container stylesheet="index_photo_preview.style"}
    <div class="ipp_latest_bg">
        <a href="{if $photos.latest}{$photos.latest.url}{else}#{/if}" class="ipp_latest {if !$photos.latest}ipp_no_content{/if}" {if $photos.latest}style="background-image: url({$photos.latest.src});"{/if}></a>
    </div>
    <div class="clearfix ipp_small_bg">
        <div class="ipp_rated">
            <div class="ipp_image {if !$photos.topRated}ipp_small_no_content{/if}">
	            {if $photos.topRated}
	               <a href="{$photos.topRated.url}">
	                   <img src="{$photos.topRated.src}" />
	               </a>
	            {/if}
	        </div>
            <div class="ipp_link"><a href="{$urls.mostViewed}">{text %label_rated}</a></div>
        </div>
        <div class="ipp_commented">
            <div class="ipp_image {if !$photos.mostCommented}ipp_small_no_content{/if}">
	            {if $photos.mostCommented}
	               <a href="{$photos.mostCommented.url}">
	                   <img src="{$photos.mostCommented.src}" />
	               </a>
	            {/if}
	        </div>
            <div class="ipp_link"><a href="{$urls.mostCommented}">{text %label_commented}</a></div>
            
        </div>
        <div class="ipp_viewed">
            <div class="ipp_image {if !$photos.mostViewed}ipp_small_no_content{/if}">
	            {if $photos.mostViewed}
	                <a href="{$photos.mostViewed.url}">
	                   <img src="{$photos.mostViewed.src}" />
	                </a>
	            {/if}
	        </div>
            <div class="ipp_link"><a href="{$urls.mostViewed}">{text %label_viewed}</a></div>
        </div>
    </div>
{/container}