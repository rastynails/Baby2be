{* component Event Attend *}

{container}
    {block}
		{form EventAttend}
        	<table class="automargin">
                <tbody>
                    <tr>
                        <td class="value">{text %.components.event_attend.i_am} {input name='attend'} {text %.components.event_attend.this_event}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="block_submit center">{button action='event_attend'}</td>
                    </tr>
                </tbody>
            </table>
        {/form}
    {/block}
{/container}
