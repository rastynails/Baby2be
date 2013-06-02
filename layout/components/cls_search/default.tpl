{container stylesheet="cls_search.style"}

    <form id="cls-search" method="get" action="">
        <input type="text" name="search" value="{if $search != ''}{$search}{else}{text %.label.cls_search}{/if}" id="cls-search-input" />
        <input type="submit" value="{text %.label.search}" />
    </form>

{/container}