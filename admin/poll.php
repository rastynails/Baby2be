<?php

$file_key	= 'tests';
$active_tab	= 'list';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );

$frontend = new AdminFrontend();

// require file with specific functions
require_once( 'inc.admin_menu.php' );
$id = $_GET['poll'];

if( $_GET['action'] == "delete" ){
	app_Poll::deletePollAnswer($_GET['answer']);
	SK_LanguageEdit::deleteKey('polls', "poll_{$id}_answer_{$_GET['answer']}");
	$frontend->registerMessage("Successfully completed.");
	redirect( $_SERVER['PHP_SELF']."?poll={$id}" );
} 

if($_POST['command'] == 'save'){

	$current_lang_id = SK_Language::current_lang_id();
	if(!empty($_POST["question"])){
		SK_LanguageEdit::setKey('polls', "poll_{$id}_question", $_POST["question"] );
	}

	if( !empty($_POST['answer']) ){
		
		foreach( $_POST['answer'] as $key=>$ans ){
			SK_LanguageEdit::setKey('polls', "poll_{$id}_answer_{$key}", array($current_lang_id => $ans));
		}

	}
	
	if( count( $_POST['new_answer'] ) ){
		foreach( $_POST['new_answer'] as $ans ){
			$ansId = app_Poll::addPollAnswer($id);
			SK_LanguageEdit::setKey('polls', "poll_{$id}_answer_{$ansId}", array($current_lang_id => $ans) );
		}
	}

	$frontend->registerMessage("Successfully updated.");

	redirect( $_SERVER['PHP_SELF']."?poll={$id}" );	
}

$test = app_Poll::get($id);

$test['question'] = @SK_Language::section('polls')->text("poll_{$id}_question");
$test['answers'] = app_Poll::getAnswers($test['id']);

foreach( $test['answers'] as $key=>$ans ){
	$test['answers'][$key]['answer'] = @SK_Language::section("polls")->text("poll_{$ans['pollId']}_answer_{$ans['id']}");
}

$frontend->assign( 'test', $test );

// Generate Output
$_page['title'] = "Polls";

$template = 'poll.html';

// display template
$frontend->display( $template );
?>
