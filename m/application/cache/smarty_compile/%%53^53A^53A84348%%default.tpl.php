<?php /* Smarty version 2.6.27, created on 2013-04-30 06:55:56
         compiled from /var/www/m/application/views/default.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'text', '/var/www/m/application/views/default.tpl', 12, false),)), $this); ?>
<?php echo '<?xml'; ?>
 version="1.0" encoding="utf-8"<?php echo '?>'; ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.1//EN" "http://www.w3.org/TR/xhtml-basic/xhtml-basic11.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<?php echo $this->_tpl_vars['header']; ?>

    <body>
    	<div class="screen">
    		<div class="header">
    			<?php echo $this->_tpl_vars['home_url']; ?>

    			<?php if (isset ( $this->_tpl_vars['username'] )): ?><div class="hun"><?php echo $this->_tpl_vars['username']; ?>
 <a class="small" href="<?php echo $this->_tpl_vars['logout_url']; ?>
">(<?php echo $this->_plugins['function']['text'][0][0]->tpl_text(array('section' => 'nav_doc_item','key' => 'sign_out'), $this);?>
)</a></div><?php endif; ?>
    		</div>
			<?php echo $this->_tpl_vars['menu']; ?>

			
			<?php if ($this->_tpl_vars['messages']): ?> 
				<?php $_from = $this->_tpl_vars['messages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['msg']):
?>
					<div class="notification"><?php echo $this->_tpl_vars['msg']['msg']; ?>
</div>
				<?php endforeach; endif; unset($_from); ?>
			<?php endif; ?>
			
			<?php if (isset ( $this->_tpl_vars['notifications'] )): ?>
	    	<?php $_from = $this->_tpl_vars['notifications']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['notif']):
?>
	    		<div class="notification"><a href="<?php echo $this->_tpl_vars['notif']['url']; ?>
"><?php echo $this->_tpl_vars['notif']['title']; ?>
</a></div>
	    	<?php endforeach; endif; unset($_from); ?>
	    	<?php endif; ?>
	    	
	    	<div class="content">
	    		<?php echo $this->_tpl_vars['content']; ?>

	    	</div>
	    	<?php echo $this->_tpl_vars['footer']; ?>

	    </div>
    </body>
</html>