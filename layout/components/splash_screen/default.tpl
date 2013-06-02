
{canvas blank}
{container stylesheet="splash_screen.style" class="splash_screen_cont"}
    {block}
        {block_cap title=%splash_screen_title}{/block_cap}
            <div class="splash_screen_txt">
                {text %.txt.splash_screen_txt}
            </div>
            <div class="splash_screen_btns clearfix">
                <div class="float_left">
                    <input type="button" value="{text %leave_button_label}" onclick="window.location='{$leave_url}'">
                </div>     
                <div class="float_right">
                    <form method="POST">
                        <input type="hidden" name="enter" value="true">
                        <input type="submit" value="{text %.txt.splash_screen_btn}">
                    </form>
                </div>

            </div>
    {/block}
{/container}
{/canvas}
