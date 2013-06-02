{container class="index_video_player"}

    {if $video.video_source eq 'file'}
        {component VideoPlayer video_file_url=$video.video_url width=$player_width height=$player_height}
    {else}
        {$video.code}<br />
    {/if}

{/container}