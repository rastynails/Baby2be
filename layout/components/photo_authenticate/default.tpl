
{canvas}
    {container stylesheet="photo_authenticate.style"}
        
        <div class="float_half_left narrow legend">
            {block title=%legend_title}
                {text %legend_content}
                <div class="resend">
                       <form>
                       <input type="hidden" value="send-request" name="command" />
                       <input type="submit" value="{text %send_again}" />
                    </form>
                </div>
            {/block}
        </div>
        
        <div class="float_half_right wide upload">
            {block title=%upload_title}

                {if $code}
                    {if $photoUrl}
                        <div class="image_c">
                            <img src="{$photoUrl}" />
                        </div>
                        <div class="submit_c">
                            <div class="block_info" style="width: 70%; margin: 0 auto">
                                {text %responce_sent}
                            </div>
                        </div>
                    {else}
                        {form PhotoAuthenticate}
                            <div class="image_c">        
                                {input name=photo}
                            </div>
                            <div class="submit_c" style="display: none">
                                {button action=send}
                            </div>
                        {/form}
                    {/if}
                {else}
                    <div class="image_c">
                        {text %not_request}
                    </div>
                {/if}
                
            {/block}
        </div>
    
    {/container}
{/canvas}