{container stylesheet='slideshow.style'}
<style>
{literal}
.pagination li a {
    background-image: url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA0AAAAnCAYAAADU3MIsAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA2ZpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0ieG1wLmRpZDowMTgwMTE3NDA3MjA2ODExODcxRkMwMDNFNTU3QkY1QiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo2REEyOUM3QThGNTQxMUUxQjE2M0RFODUyMUQ2RjRBRSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo2REEyOUM3OThGNTQxMUUxQjE2M0RFODUyMUQ2RjRBRSIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ1M1IE1hY2ludG9zaCI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjA0ODAxMTc0MDcyMDY4MTE4NzFGQzAwM0U1NTdCRjVCIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjAxODAxMTc0MDcyMDY4MTE4NzFGQzAwM0U1NTdCRjVCIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+InkitgAAAaJJREFUeNrsVTFLw0AYTYMIouAgUgeRFgRBUIQEF0HFyVnsIlQUBHF28h8UJ51KS0Wpa/6BOLhYlNTBQRAKQkGwiINQEQSJ7ytfwt31kt7k1Acv313ue5e7+16SVBAElgjXdacRHHAUfALrvu9/izmpUITkLEIFXLNktMFDCMuSCII5tG/BEbABPoCf4Cy4xLkXEO52RI7jDCLegQvgJXiAwS9huXmEEjgE5jDm2WhssaCGG9uigIA+TXTE3WO6kGidb5xZMYDwlJedpYMi0TiPvVjJaHGcINE7d6Z6iNIc30h0xZ18XDaWRPum+jWx1IbNJ/ZK9cFgFRxWBDTZCXcLYp0W0b7mOj0rdVpmgYen5HSOOAdXejoCxVW3MKN6j4URbM2+fzWUMCC0Ew0LltUnkWEfNQKLD6fE+41EZNgqDyZhB9wMRaFhTdBlWBPQviXDmkIyrCkkw5qgSe+VaFgTFMKD+AE3VKto4IFFsbj34Dx4o0mmyfbpoxIZtpiZlDIqY+kuw+59tNpx3usACX3D9g37H4YVbUT/p1WTL+yfAAMAENmkCGf35LkAAAAASUVORK5CYII=");
}
{/literal}
</style>

{if $slides}
<div id="slideshow-{$uniqName}" class="ow_slideshow">
    <div class="slides_container" style="height: 0px;">
        {foreach from=$slides item='slide' name='s'}
            <div class="ow_slide">
                {if $slide.url}<a href="{$slide.url}">{/if}<img src="{$slide.imageUrl}" />{if $slide.url}</a>{/if}
                {if $slide.label}<div class="ow_slide_caption" id="caption-{$uniqName}-{$smarty.foreach.s.iteration}"><div>{$slide.label}</div></div>{/if}
            </div>
        {/foreach}
    </div>
</div>
{/if}
{/container}