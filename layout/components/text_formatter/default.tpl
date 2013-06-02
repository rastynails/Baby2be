{container stylesheet="text_formatter.style"}

<div class="tf_controls clearfix">
	{foreach from=$controls item=item}
	    <a href="javascript://" sk-tf-command="{$item}" title="{$item}" class="b_{$item} b_control"></a>
	{/foreach}
</div>

<div style="display: none;">
    <div {id="smile-box-c"}>
        <div {id="smile-box"} class="add_smile_block content">
            {$smileString}
        </div>
    </div>
</div>

{/container}