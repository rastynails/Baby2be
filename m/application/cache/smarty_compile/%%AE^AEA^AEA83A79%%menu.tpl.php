<?php /* Smarty version 2.6.27, created on 2013-04-30 06:55:56
         compiled from /var/www/m/application/views/menu.tpl */ ?>

<div class="menubar">
	<?php $_from = $this->_tpl_vars['main_menu']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['m'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['m']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['m_item']):
        $this->_foreach['m']['iteration']++;
?>
		<div class="mitem set<?php echo $this->_tpl_vars['mset']; ?>
 i<?php echo $this->_foreach['m']['iteration']; ?>
<?php if ($this->_tpl_vars['m_item']['active']): ?> active<?php endif; ?>">
			<a href="<?php echo $this->_tpl_vars['m_item']['link']; ?>
" class="<?php echo $this->_tpl_vars['k']; ?>
">&nbsp;</a>
		</div>
	<?php endforeach; endif; unset($_from); ?>
</div>

<h1 class="page_head"><?php echo $this->_tpl_vars['page_head']; ?>
</h1>

<?php if ($this->_tpl_vars['sub_menu']): ?>
<ul class="submenu">
	<?php $_from = $this->_tpl_vars['sub_menu']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['sk'] => $this->_tpl_vars['s_item']):
?>
		<li><a href="<?php echo $this->_tpl_vars['s_item']['link']; ?>
" class="<?php echo $this->_tpl_vars['sk']; ?>
 <?php if ($this->_tpl_vars['s_item']['active']): ?> active<?php endif; ?>"><?php echo $this->_tpl_vars['s_item']['title']; ?>
</a></li>
	<?php endforeach; endif; unset($_from); ?>
	<br clear="all" />
</ul>
<?php endif; ?>

<?php if (isset ( $this->_tpl_vars['im_inv'] )): ?> 
	<?php $_from = $this->_tpl_vars['im_inv']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['inv']):
?>
		<div class="notification"><a href="<?php echo $this->_tpl_vars['inv']['link']; ?>
"><?php echo $this->_tpl_vars['inv']['title']; ?>
: <?php echo $this->_tpl_vars['inv']['message']; ?>
</a></div>
	<?php endforeach; endif; unset($_from); ?>
<?php endif; ?>