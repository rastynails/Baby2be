<?php /* Smarty version 2.6.27, created on 2013-04-30 06:55:56
         compiled from /var/www/m/application/views/footer.tpl */ ?>

<div class="footer">
	<ul class="fmenu">
	<?php $_from = $this->_tpl_vars['menu']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
		<li><a href="<?php echo $this->_tpl_vars['item']['link']; ?>
" <?php if ($this->_tpl_vars['item']['active']): ?>class="active"<?php endif; ?>><?php echo $this->_tpl_vars['item']['title']; ?>
</a></li>
	<?php endforeach; endif; unset($_from); ?>
	<br clear="all" />
</ul>

</div>