{container stylesheet="text_formatter.style"}
<div class="title">{text %.components.text_formatter.link.title}</div>
<div class="content">
    <form class="link_form">
        <table class="form">
            <tr>
                <td class="label">{text %.components.text_formatter.link.url}</td>
                <td class="value all_row_width">
                   <input type="text" name="url" value="http://"/>
                </td>
            </tr>
            <tr>
                <td class="label">{text %.components.text_formatter.link.label}</td>
                <td class="value all_row_width">
                   <input type="text" name="title" value=""/>
                </td>
            </tr>   
            <tr>
                <td colspan="2" class="submit">
                   <input type="submit" value="Add"/>
                </td>
            </tr>
        </table>
     </form>
</div>
{/container}