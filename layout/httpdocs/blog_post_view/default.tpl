{* Httpdoc Sign In component *}

{canvas}
	{block_cap}<h3>{$blog_post->getTitle()|censor:'blog':true}</h3>{/block_cap}
    {block}
    	<table width="100%">
            <tbody>
                <tr>
                    <td class="forum_thumb">
                        <p class="small"><a href="profile.php">SK9</a></p>
                    </td>
                    <td class="forum_post brd_bottom">
                        <div class="time small">{$blog_post->getCreate_time_stamp()|spec_date}</div>
                        {$blog_post_text|censor:'blog'}
                    </td>
                </tr>
                <tr>
                	<td colspan="2">{paging total=$blog_post_pages_count on_page=1 pages=10}</td>
                </tr>
            </tbody>
        </table>
    {/block}
    <div style="width:500px;margin:0 auto;">{component $comments}</div>
{/canvas}