
{block}
    {text section='components.email_verify' key='msg'}
    <br /><br />
    
    <div class="form">
        <span class="highlight">{text section='components.email_verify' key='send_label'}</span><br /><br />
        <form method="post">
            <input type="text" name="email" value="{$email}" style="width: 150px" />
            <input type="submit" name="emailverify" value="{text section='forms.email_verify.actions' key='send'}" />
        </form>
    </div>
{/block}
