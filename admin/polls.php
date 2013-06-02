<?php

$file_key	= 'polls';
$active_tab	= 'polls';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );

$frontend = new AdminFrontend();

// require file with specific functions
require_once( 'inc.admin_menu.php' );

$langSection  = new SK_Config_Section('languages');

switch( $_POST['command'] ){
	case 'addTest':
        
        $answerEmpty = true;
        foreach( $_POST['answer_txt'] as $answer )
        {
            if (!empty($answer))
                $answerEmpty = false;
        }
        
        if ($answerEmpty)
        {
            $frontend->registerMessage("All fields should be filled", 'error    ');
            redirect(URL_ADMIN.'polls.php');
            
        }
        
		$test['question'] = $_POST['question'];
		foreach ( $_POST['answer_txt'] as $key=>$value ){
            
            if (empty($value)) 
            {
                continue;
            }
            
			$test['answers'][] = array( 'answer'=>$value);
		}
        
		$test['expiration_timestamp'] = mktime(1,2,3, 6, 1, 2009);
		$id = app_Poll::add($test['expiration_timestamp']);

		$values = array($langSection->get('default_lang_id') => $test['question']);

		SK_LanguageEdit::setKey( 'polls', 'poll_'.$id.'_question', $values );
		
		foreach ($test['answers'] as $answer){
			$answerId = app_Poll::addPollAnswer($id);
			$values = array($langSection->get('default_lang_id') => $answer['answer']);
			SK_LanguageEdit::setKey( 'polls', "poll_{$id}_answer_{$answerId}", $values );
		}

		$frontend->registerMessage("The Poll successfuly added.");
		redirect(URL_ADMIN.'polls.php');
		break;
		
	case 'delete_test':

		if( count($_POST['test']) ){
			foreach ( $_POST['test'] as $testId=>$val ){
				$anses = app_Poll::getAnswers($testId);
				foreach($anses as $ans){
					$key = "poll_{$testId}_answer_{$ans['id']}";
					SK_LanguageEdit::deleteKey('polls', $key);
				}
				$key = "poll_{$testId}_question"; 
				SK_LanguageEdit::deleteKey('polls', $key);
				app_Poll::delete($testId);
			}
		}
		
		break;
}

// Generate Output

$tests = app_Poll::getList(0, app_Poll::getCount());

foreach ($tests as $key=>$test){
	$tests[$key]['question'] = @SK_Language::section('polls')->text("poll_{$test['id']}_question");
}

$frontend->assign('tests', $tests);

$_page['title'] = "Polls";

$template = 'polls.html';

// display template
$frontend->display( $template );
?>