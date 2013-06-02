<?php /* Smarty version 2.6.27, created on 2013-04-30 06:55:56
         compiled from /var/www/m/application/views/index.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'text', '/var/www/m/application/views/index.tpl', 2, false),)), $this); ?>

<div class="sign_in_label"><?php echo $this->_plugins['function']['text'][0][0]->tpl_text(array('section' => 'nav_doc_item.headers','key' => 'sign_in'), $this);?>
</div>

<div class="form">
	<form method="post" action="">
	   <input type="hidden" name="action" value="login" />
		<div class="label"><label for="login"><?php echo $this->_plugins['function']['text'][0][0]->tpl_text(array('section' => 'forms.sign_in.fields.login','key' => 'label'), $this);?>
:</label></div>
		<input id="login" type="text" name="login" /><br />
		<div class="label"><label for="password"><?php echo $this->_plugins['function']['text'][0][0]->tpl_text(array('section' => 'forms.sign_in.fields.password','key' => 'label'), $this);?>
:</label></div>
		<input id="password" type="password" name="password" /><br /><br />
		<div class="center"><input type="submit" value="<?php echo $this->_plugins['function']['text'][0][0]->tpl_text(array('section' => 'forms.sign_in.actions','key' => 'process'), $this);?>
" /></div><br />
	</form>
</div>

<?php if ($this->_tpl_vars['allow_registration']): ?>
<div class="sign_in_label"><?php echo $this->_plugins['function']['text'][0][0]->tpl_text(array('section' => 'forms._actions','key' => 'join'), $this);?>
</div>
<div class="form">
    <form method="post" action="">
        <input type="hidden" name="action" value="join" />

        <?php $_from = $this->_tpl_vars['fields']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['field']):
?>

            <?php if ($this->_tpl_vars['field']['name'] == 'i_am_at_least_18_years_old' && in_array ( $this->_tpl_vars['field']['name'] , $this->_tpl_vars['fieldnames'] )): ?>
                <div class="label">
                    <input id="<?php echo $this->_tpl_vars['field']['name']; ?>
" type="<?php echo $this->_tpl_vars['field']['presentation']; ?>
" name="<?php echo $this->_tpl_vars['field']['name']; ?>
" value="1" <?php if (isset ( $this->_tpl_vars['sval'][$this->_tpl_vars['field']['name']] ) && $this->_tpl_vars['sval'][$this->_tpl_vars['field']['name']] == '1'): ?>checked="checked"<?php endif; ?> />
                    <label for="<?php echo $this->_tpl_vars['field']['name']; ?>
"><?php echo $this->_plugins['function']['text'][0][0]->tpl_text(array('section' => 'profile_fields.label_join','key' => $this->_tpl_vars['field']['id']), $this);?>
</label>
                </div>
            <?php elseif ($this->_tpl_vars['field']['name'] == 'i_agree_with_tos' && in_array ( $this->_tpl_vars['field']['name'] , $this->_tpl_vars['fieldnames'] )): ?>
                <div class="label">
                    <input id="<?php echo $this->_tpl_vars['field']['name']; ?>
" type="<?php echo $this->_tpl_vars['field']['presentation']; ?>
" name="<?php echo $this->_tpl_vars['field']['name']; ?>
" value="1" <?php if (isset ( $this->_tpl_vars['sval'][$this->_tpl_vars['field']['name']] ) && $this->_tpl_vars['sval'][$this->_tpl_vars['field']['name']] == '1'): ?>checked="checked"<?php endif; ?> />
                    <label for="<?php echo $this->_tpl_vars['field']['name']; ?>
"><?php echo $this->_tpl_vars['field']['label']; ?>
</label>
                </div>            
            <?php else: ?>
                <div class="label"><label for="<?php echo $this->_tpl_vars['field']['name']; ?>
"><?php echo $this->_plugins['function']['text'][0][0]->tpl_text(array('section' => 'profile_fields.label_join','key' => $this->_tpl_vars['field']['id']), $this);?>
:</label></div>
                <?php if ($this->_tpl_vars['field']['presentation'] == 'multicheckbox'): ?>
                    <?php $_from = $this->_tpl_vars['field']['values']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['val']):
?>
                    
                        <label><input type="checkbox" name="<?php echo $this->_tpl_vars['field']['name']; ?>
[]" value="<?php echo $this->_tpl_vars['val']['val']; ?>
" <?php if ($this->_tpl_vars['val']['checked']): ?>checked="checked"<?php endif; ?> />
                            <?php echo $this->_plugins['function']['text'][0][0]->tpl_text(array('section' => 'profile_fields.value','key' => "sex_".($this->_tpl_vars['val']['val'])), $this);?>

                        </label>
                    <?php endforeach; endif; unset($_from); ?>
                <?php elseif ($this->_tpl_vars['field']['presentation'] == 'radio'): ?>
                    <?php $_from = $this->_tpl_vars['field']['values']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['val']):
?>
                        <label><input type="radio" name="<?php echo $this->_tpl_vars['field']['name']; ?>
" value="<?php echo $this->_tpl_vars['val']['val']; ?>
" <?php if (isset ( $this->_tpl_vars['sval'][$this->_tpl_vars['field']['name']] ) && $this->_tpl_vars['sval'][$this->_tpl_vars['field']['name']] == $this->_tpl_vars['val']['val']): ?>checked="checked"<?php endif; ?> />
                            <?php echo $this->_plugins['function']['text'][0][0]->tpl_text(array('section' => 'profile_fields.value','key' => ($this->_tpl_vars['field']['name'])."_".($this->_tpl_vars['val']['val'])), $this);?>

                        </label>
                    <?php endforeach; endif; unset($_from); ?>
                <?php elseif ($this->_tpl_vars['field']['name'] == 'birthdate'): ?>
                    <select name="<?php echo $this->_tpl_vars['field']['name']; ?>
[year]">
                    <option><?php echo $this->_plugins['function']['text'][0][0]->tpl_text(array('section' => 'forms._fields.date','key' => 'year'), $this);?>
</option>
                    <?php $_from = $this->_tpl_vars['field']['years']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['y']):
?>
                        <option value="<?php echo $this->_tpl_vars['y']; ?>
"<?php if (isset ( $this->_tpl_vars['sval'][$this->_tpl_vars['field']['name']]['year'] ) && $this->_tpl_vars['sval'][$this->_tpl_vars['field']['name']]['year'] == $this->_tpl_vars['y']): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['y']; ?>
</option>
                    <?php endforeach; endif; unset($_from); ?>
                    </select>
                    <select name="<?php echo $this->_tpl_vars['field']['name']; ?>
[month]">
                    <option><?php echo $this->_plugins['function']['text'][0][0]->tpl_text(array('section' => 'forms._fields.date','key' => 'month'), $this);?>
</option>
                    <?php $_from = $this->_tpl_vars['field']['months']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['m']):
?>
                        <option value="<?php echo $this->_tpl_vars['m']; ?>
"<?php if (isset ( $this->_tpl_vars['sval'][$this->_tpl_vars['field']['name']]['month'] ) && $this->_tpl_vars['sval'][$this->_tpl_vars['field']['name']]['month'] == $this->_tpl_vars['m']): ?> selected="selected"<?php endif; ?>><?php echo $this->_plugins['function']['text'][0][0]->tpl_text(array('section' => 'i18n.date','key' => "month_full_".($this->_tpl_vars['m'])), $this);?>
</option>
                    <?php endforeach; endif; unset($_from); ?>
                    </select>
                    <select name="<?php echo $this->_tpl_vars['field']['name']; ?>
[day]">
                    <option><?php echo $this->_plugins['function']['text'][0][0]->tpl_text(array('section' => 'forms._fields.date','key' => 'day'), $this);?>
</option>
                    <?php $_from = $this->_tpl_vars['field']['days']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['d']):
?>
                        <option value="<?php echo $this->_tpl_vars['d']; ?>
"<?php if (isset ( $this->_tpl_vars['sval'][$this->_tpl_vars['field']['name']]['day'] ) && $this->_tpl_vars['sval'][$this->_tpl_vars['field']['name']]['day'] == $this->_tpl_vars['d']): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['d']; ?>
</option>
                    <?php endforeach; endif; unset($_from); ?>
                    </select>
                <?php elseif ($this->_tpl_vars['field']['name'] == 'match_agerange'): ?>
                    <?php echo $this->_plugins['function']['text'][0][0]->tpl_text(array('section' => 'profile.labels','key' => 'field_agerange_from'), $this);?>

                    <select name="<?php echo $this->_tpl_vars['field']['name']; ?>
[from]">
                    <?php $_from = $this->_tpl_vars['field']['from']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['f']):
?>
                        <option value="<?php echo $this->_tpl_vars['f']; ?>
"<?php if (isset ( $this->_tpl_vars['sval'][$this->_tpl_vars['field']['name']]['from'] ) && $this->_tpl_vars['sval'][$this->_tpl_vars['field']['name']]['from'] == $this->_tpl_vars['f']): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['f']; ?>
</option>
                    <?php endforeach; endif; unset($_from); ?>
                    </select>
                    <?php echo $this->_plugins['function']['text'][0][0]->tpl_text(array('section' => 'profile.labels','key' => 'field_agerange_to'), $this);?>

                    <select name="<?php echo $this->_tpl_vars['field']['name']; ?>
[to]">
                    <?php $_from = $this->_tpl_vars['field']['to']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['t']):
?>
                        <option value="<?php echo $this->_tpl_vars['t']; ?>
"<?php if (isset ( $this->_tpl_vars['sval'][$this->_tpl_vars['field']['name']]['to'] ) && $this->_tpl_vars['sval'][$this->_tpl_vars['field']['name']]['to'] == $this->_tpl_vars['t']): ?> selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['t']; ?>
</option>
                    <?php endforeach; endif; unset($_from); ?>
                    </select>
                <?php else: ?>
                    <input id="<?php echo $this->_tpl_vars['field']['name']; ?>
" type="<?php echo $this->_tpl_vars['field']['presentation']; ?>
" name="<?php echo $this->_tpl_vars['field']['name']; ?>
" <?php if (isset ( $this->_tpl_vars['sval'][$this->_tpl_vars['field']['name']] )): ?>value="<?php echo $this->_tpl_vars['sval'][$this->_tpl_vars['field']['name']]; ?>
"<?php endif; ?> />
                <?php endif; ?>

                <?php if ($this->_tpl_vars['field']['confirm']): ?>
                    <div class="label"><label for="re_<?php echo $this->_tpl_vars['field']['name']; ?>
"><?php echo $this->_plugins['function']['text'][0][0]->tpl_text(array('section' => 'profile_fields.confirm','key' => $this->_tpl_vars['field']['id']), $this);?>
:</label></div>
                    <?php ob_start(); ?>re_<?php echo $this->_tpl_vars['field']['name']; ?>
<?php $this->_smarty_vars['capture']['fname'] = ob_get_contents(); ob_end_clean(); ?>
                    <input id="re_<?php echo $this->_tpl_vars['field']['name']; ?>
" type="<?php echo $this->_tpl_vars['field']['presentation']; ?>
" name="re_<?php echo $this->_tpl_vars['field']['name']; ?>
" <?php if (isset ( $this->_tpl_vars['sval'][$this->_smarty_vars['capture']['fname']] )): ?>value="<?php echo $this->_tpl_vars['sval'][$this->_smarty_vars['capture']['fname']]; ?>
"<?php endif; ?> /><br />
                <?php endif; ?>
            <?php endif; ?>
        <?php endforeach; endif; unset($_from); ?>

        <table>
            <tr>
                <td><?php echo $this->_plugins['function']['text'][0][0]->tpl_text(array('section' => 'profile_fields.label_search','key' => '112'), $this);?>
</td>
                <td>
                    <select name="country_id" id="country_select">
                        <option value=""><?php echo $this->_plugins['function']['text'][0][0]->tpl_text(array('section' => 'profile_fields.label_join','key' => '112'), $this);?>
</option>;
                        <?php $_from = $this->_tpl_vars['countries']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['c']):
?>
                            <option value="<?php echo $this->_tpl_vars['c']['Country_str_code']; ?>
"><?php echo $this->_tpl_vars['c']['Country_str_name']; ?>
</option>;
                        <?php endforeach; endif; unset($_from); ?>
                    </select>
                </td>
            </tr>
        </table>

        <br />
        <div class="center"><input type="submit" value="<?php echo $this->_plugins['function']['text'][0][0]->tpl_text(array('section' => 'forms._actions','key' => 'join'), $this);?>
" /></div><br />
    </form>
</div>
<?php endif; ?>
<br />