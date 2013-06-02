{* component Cls *}

{canvas}
{container stylesheet='cls.style'}

<div class="clearfix">
<div class="float_half_right">
    {component ClsSearch}
</div>
</div>

{if $search}
    {component $searchResCmp}
{else}
<div class="float_half_left">
	{component ClsIndexCategories entity='wanted'}
</div>
<div class="float_half_right">
	{component ClsIndexCategories entity='offer'}
</div>
<br clear="all"/>

<div class="float_half_left">
	{component ClsIndexItemList entity='wanted'}
</div>
<div class="float_half_right">
	{component ClsIndexItemList entity='offer'}
</div>
{/if}
<br clear="all"/>

{/container}
{/canvas}