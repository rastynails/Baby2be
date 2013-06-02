<?php

$file_key = 'mail_scheduler';
$active_tab = 'scheduler';

require_once( '../internals/Header.inc.php' );

require_once( DIR_ADMIN_INC.'fnc.auth.php' );
//require_once( DIR_ADMIN_INC.'fnc.admin.php' );

require_once( DIR_ADMIN_INC.'fnc.scheduler.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

$frontend = new AdminFrontend();

require_once( 'inc.admin_menu.php' );

if ( $_POST['create_statement'] )
{
	switch ( app_ActivityScheduler::createStatement($_POST) )
	{
		case -1:
			$frontend->registerMessage( 'Scheduler name is empty!', 'error' );
			break;
		case -2:
			$frontend->registerMessage( 'Conditions of scheduler are empty!', 'error' );
			break;
		default:
			$frontend->registerMessage( 'Scheduler was created' );
	}
	redirect( $_SERVER['REQUEST_URI'] );
}

if ( $_POST['edit_statement'] )
{
	switch ( app_ActivityScheduler::updateStatement($_POST['statement_id'], $_POST) )
	{
		case -1:
			$frontend->registerMessage( 'Empty scheduler name!', 'error' );
			break;
		case 0:
			$frontend->registerMessage( 'Scheduler was not updated', 'notice' );
			break;
		default:
			$frontend->registerMessage( 'Scheduler was successfully updated' );
			break;
	}

	redirect( $_SERVER['REQUEST_URI'] );
}

if ( $_POST['statement_id'] )
{
	app_ActivityScheduler::deleteStatement($_POST['statement_id']);
	$frontend->registerMessage('Scheduler was successfully deleted');
	redirect($_SERVER['REQUEST_URI']);
}

$all_schedulers = app_ActivityScheduler::getStatements();

$matchesEnabled = app_Features::isAvailable(51);

foreach ( $all_schedulers as $key => $item )
{
    if ( $item['type'] == 'match' && !$matchesEnabled )
    {
        unset($all_schedulers[$key]);
    }
}

$frontend->assign_by_ref('matchesEnabled', $matchesEnabled);

$frontend->assign_by_ref('all_schedulers', $all_schedulers);

$frontend->register_function('print_condition', 'frontendPrintConditions');
$frontend->assign('mail_list', app_ActivityScheduler::getMailTemplates());

$template = 'mail_scheduler.html';

$_page['title'] = "Scheduler";

// display template
$frontend->display( $template );
