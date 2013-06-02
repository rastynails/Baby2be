
{canvas blank}

{container stylesheet='sms_zaypay.style'}

{if $error}
    <div class="no_content">{$error}</div>
{else}    
    <div class="zaypay_container">
    <div id="header">
        <div id="logo"><a href="http://www.zaypay.com" target="_blank">Go to zaypay.com</a></div>
    </div>
    <div id="pcontent">
    
    {if $page == 1}
    
        {assign var='english_name' value='english-name'}
        {assign var='native_name' value='native-name'}
        
        <h2>{text %.components.sms_zaypay.choose_country}</h2>
        <form name="form_locale" method="post">
        <table width="100%">
        <tr>
            <td width="50%">
                <select name="locale_country" {id="locale_country"}>
                {foreach from=$locales.countries item='country'}
                    <option value="{$country.code}"{if $country.code == $Zaypay->getLocale('country')} selected{/if}>
                        {$country.name}
                    </option>
                {/foreach}
                </select>
            </td>
            <td width="50%" align="right">
                <select name="locale_language" {id="locale_language"}>
                {foreach from=$locales.languages item='language'}
                    <option value="{$language.code}"{if $language.code == $Zaypay->getLocale('language')} selected{/if}>                      
                        {$language.$english_name} ({$language.$native_name})
                    </option>
                {/foreach}
                </select>
            </td>
        </tr>
        </table>
        </form>
    
        {assign var='payment_method_id' value='payment-method-id'}
        {assign var='very_short_instructions_with_amount' value='very-short-instructions-with-amount'}
    
        <br />
        
        <h2>{text %.components.sms_zaypay.choose_method}</h2>
        <form name="form_method" method="post">
            <table width="70%">
            <tr>
                <td width="100%" valign="top">
                    {foreach from=$payment_methods item='method'}
                        <label>
                            <input type="radio" name="paymentmethod" value="{$method.$payment_method_id}" />
                            {$method.$very_short_instructions_with_amount}
                        </label><br />
                    {/foreach}
                </td>
            </tr>
            </table>
            <input type="hidden" name="locale" value="{$Zaypay->getLocale()}" />
            <input type="hidden" name="action" value="pay" />
            
            <br />
            <div class="center button">
                <input type="submit" name="submit" value="Continue &raquo;" />
            </div>
        </form>
    
    {elseif $page == 2}
    
        {assign var='very_short_instructions_with_amount' value='very-short-instructions-with-amount'}
        {assign var='long_instructions' value='long-instructions'}
        {assign var='status_string' value='status-string'}
        {assign var='verification_needed' value='verification-needed'}
        
        <h2>{$zaypay_info.$very_short_instructions_with_amount}</h2>
        <br />

        <form name="form_pay" method="post" class="form_pay">
            <input type="hidden" name="paymentid" value="{$zaypay_info.payment.id}" />
            <input type="hidden" name="action" value="paid" />
            <table width="100%">
            <tr>
                <td width="20%" valign="top">{text %.components.sms_zaypay.instruction}</td>
                <td width="80%" valign="top">{$zaypay_info.$long_instructions}</td>
            </tr>
            <tr>
                <td width="20%" valign="top">{text %.components.sms_zaypay.status}</td>
                <td width="80%" valign="top">{$zaypay_info.$status_string}</td>
            </tr>
            {if $zaypay_info.payment.$verification_needed == 'true'}
            <tr>
                <td width="20%" valign="top">{text %.components.sms_zaypay.verification_code}</td>
                <td width="80%" valign="top"><input type="text" name="verification_code" size="6" /></td>
            </tr>
            {/if}
            </table>
            <br /><br />
            <div class="center">
                <input type="submit" name="submit" value="Continue &raquo;" />
            </div>
        </form>
    
    {elseif $page == 3}
        {text %.components.sms_zaypay.payment_completed}<br />
        <a href="javascript://" {id="close_tb"}>{text %.components.sms_zaypay.continue}</a>
    {/if}

    </div>
    
    <div class="foot-note">
        <div style="float: left; padding-left: 5px; color: #666">Need help? Mail <a href="mailto:customercare@zaypay.com?subject=Ref ID: {$ps_id}">customer care</a>.</div>
        <div class="powered_by_zp"><a target="_blank" href="http://www.zaypay.com/" title="Micropayments">Powered by zaypay.com</a></div>
    </div>
    
    </div>
{/if}
    
    {/container}
    
{/canvas}