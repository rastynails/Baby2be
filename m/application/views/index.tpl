
<div class="sign_in_label">{text section='nav_doc_item.headers' key='sign_in'}</div>

<div class="form">
	<form method="post" action="">
	   <input type="hidden" name="action" value="login" />
		<div class="label"><label for="login">{text section='forms.sign_in.fields.login' key='label'}:</label></div>
		<input id="login" type="text" name="login" /><br />
		<div class="label"><label for="password">{text section='forms.sign_in.fields.password' key='label'}:</label></div>
		<input id="password" type="password" name="password" /><br /><br />
		<div class="center"><input type="submit" value="{text section='forms.sign_in.actions' key='process'}" /></div><br />
	</form>
</div>

{if $allow_registration}
<div class="sign_in_label">{text section='forms._actions' key='join'}</div>
<div class="form">
    <form method="post" action="">
        <input type="hidden" name="action" value="join" />

        {foreach from=$fields item='field'}

            {if $field.name=='i_am_at_least_18_years_old' && in_array($field.name, $fieldnames)}
                <div class="label">
                    <input id="{$field.name}" type="{$field.presentation}" name="{$field.name}" value="1" {if isset($sval[$field.name]) && $sval[$field.name] == '1'}checked="checked"{/if} />
                    <label for="{$field.name}">{text section='profile_fields.label_join' key=$field.id}</label>
                </div>
            {elseif $field.name=='i_agree_with_tos' && in_array($field.name, $fieldnames)}
                <div class="label">
                    <input id="{$field.name}" type="{$field.presentation}" name="{$field.name}" value="1" {if isset($sval[$field.name]) && $sval[$field.name] == '1'}checked="checked"{/if} />
                    <label for="{$field.name}">{$field.label}</label>
                </div>            
            {else}
                <div class="label"><label for="{$field.name}">{text section='profile_fields.label_join' key=$field.id}:</label></div>
                {if $field.presentation == 'multicheckbox'}
                    {foreach from=$field.values item='val'}
                    
                        <label><input type="checkbox" name="{$field.name}[]" value="{$val.val}" {if $val.checked}checked="checked"{/if} />
                            {text section='profile_fields.value' key=sex_`$val.val`}
                        </label>
                    {/foreach}
                {elseif $field.presentation == 'radio'}
                    {foreach from=$field.values item='val'}
                        <label><input type="radio" name="{$field.name}" value="{$val.val}" {if isset($sval[$field.name]) && $sval[$field.name] == $val.val}checked="checked"{/if} />
                            {text section='profile_fields.value' key=`$field.name`_`$val.val`}
                        </label>
                    {/foreach}
                {elseif $field.name == 'birthdate'}
                    <select name="{$field.name}[year]">
                    <option>{text section='forms._fields.date' key='year'}</option>
                    {foreach from=$field.years item='y'}
                        <option value="{$y}"{if isset($sval[$field.name].year) && $sval[$field.name].year == $y} selected="selected"{/if}>{$y}</option>
                    {/foreach}
                    </select>
                    <select name="{$field.name}[month]">
                    <option>{text section='forms._fields.date' key='month'}</option>
                    {foreach from=$field.months item='m'}
                        <option value="{$m}"{if isset($sval[$field.name].month) && $sval[$field.name].month == $m} selected="selected"{/if}>{text section='i18n.date' key=month_full_`$m`}</option>
                    {/foreach}
                    </select>
                    <select name="{$field.name}[day]">
                    <option>{text section='forms._fields.date' key='day'}</option>
                    {foreach from=$field.days item='d'}
                        <option value="{$d}"{if isset($sval[$field.name].day) && $sval[$field.name].day == $d} selected="selected"{/if}>{$d}</option>
                    {/foreach}
                    </select>
                {elseif $field.name == 'match_agerange'}
                    {text section='profile.labels' key='field_agerange_from'}
                    <select name="{$field.name}[from]">
                    {foreach from=$field.from item='f'}
                        <option value="{$f}"{if isset($sval[$field.name].from) && $sval[$field.name].from == $f} selected="selected"{/if}>{$f}</option>
                    {/foreach}
                    </select>
                    {text section='profile.labels' key='field_agerange_to'}
                    <select name="{$field.name}[to]">
                    {foreach from=$field.to item='t'}
                        <option value="{$t}"{if isset($sval[$field.name].to) && $sval[$field.name].to == $t} selected="selected"{/if}>{$t}</option>
                    {/foreach}
                    </select>
                {else}
                    <input id="{$field.name}" type="{$field.presentation}" name="{$field.name}" {if isset($sval[$field.name])}value="{$sval[$field.name]}"{/if} />
                {/if}

                {if $field.confirm}
                    <div class="label"><label for="re_{$field.name}">{text section='profile_fields.confirm' key=$field.id}:</label></div>
                    {capture name='fname'}re_{$field.name}{/capture}
                    <input id="re_{$field.name}" type="{$field.presentation}" name="re_{$field.name}" {if isset($sval[$smarty.capture.fname])}value="{$sval[$smarty.capture.fname]}"{/if} /><br />
                {/if}
            {/if}
        {/foreach}

        <table>
            <tr>
                <td>{text section='profile_fields.label_search' key='112'}</td>
                <td>
                    <select name="country_id" id="country_select">
                        <option value="">{text section='profile_fields.label_join' key='112'}</option>;
                        {foreach from=$countries item='c'}
                            <option value="{$c.Country_str_code}">{$c.Country_str_name}</option>;
                        {/foreach}
                    </select>
                </td>
            </tr>
        </table>

        <br />
        <div class="center"><input type="submit" value="{text section='forms._actions' key='join'}" /></div><br />
    </form>
</div>
{/if}
<br />