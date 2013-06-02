{container}
    {block title=%title}

        <div>
            {if $tw_enabled}
                <a 
                    href="{$tw_href}" 
                    class="twitter-share-button"

                    {if $tw_data_via}data-via="{$tw_data_via}"{/if}

                    {if !$tw_data_count}data-count="none"{/if}
                    {if $tw_data_size}data-size="large"{/if} 
                    data-dnt="{$data_dnt}"></a>
            {/if}

            {if $gp_enabled}
                <div 
                    class="g-plusone" 
                    data-size="{$gp_size}" 
                    data-annotation="{$gp_annotation}" 
                    data-width="{$gp_width}"></div>
            {/if}

            {if $fb_enabled}
                <div
                    class="fb-like" 
                    data-send="{$fb_send}" 
                    data-layout="{$fb_layout}" 
                    data-width="{$fb_width}" 
                    data-show-faces="{$fb_show_faces}" 
                    data-font="{$fb_font}"
                    data-action="{$fb_action}"
                    data-colorscheme="{$fb_color_scheme}"></div>
            {/if}

        </div>
    {/block}
{/container}
