{canvas}
{container}
    {block title=%title class="center_block"}
        <div class="send_request_c">
	        <div class="block_info">
	            {text %tip}
	        </div>
	        <div class="center">
	            <form>
	               <input type="hidden" value="send-request" name="command" />
	               <input type="submit" value="{text %send}" />
	            </form>
	        </div>
        </div>
    {/block}
{/container}
{/canvas}