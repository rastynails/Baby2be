<?php
$file_key = 'badwords';
$active_tab = 'badwords';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );

$frontend = new AdminFrontend( $language );

$service = app_TextService::newInstance();


//********************************************//

$page = ( isset($_GET['page']) && (int)$_GET['page'] > 0) ? (int)$_GET['page'] : 1;
$badwordType = ( isset($_GET['badword_type']) && in_array($_GET['badword_type'], array('string', 'word', 'regexp'))) ? $_GET['badword_type'] : 'word';

$configs = $service->getConfigs();

$bcount = $service->getBadwordsCount( $badwordType );

$pages_count = ( $bcount - ( $bcount % $configs['on_page_count'] ) ) / $configs['on_page_count'] + ( ( $bcount % $configs['on_page_count'] ) == 0 ? 0 : 1 );

$errorList = array();

if( $pages_count > 0 && $pages_count < $page )
{
    $page = $pages_count;
}

if( $_POST['add'] )
{
	if( $_POST['badword'] && strlen(trim($_POST['badword'])) > 2 )
	{
		$ex_entry = $service->findBadwordByLabel( trim( $_POST['badword'] ), $_POST['badword_type'] );

		if( $ex_entry != null )
		{
			$frontend->registerMessage( 'Duplicated badword label!', 'error' );
		}
		else
		{
			$badword = new Badword( trim($_POST['badword']), $_POST['badword_type'] );
			$service->saveOrUpdate( $badword );
			$frontend->registerMessage( 'Badword was added!' );
		}
		redirect( $_SERVER['REQUEST_URI'] );
	}
}

if( $_GET['delete_id'] )
{
	controlAdminGETActions();

	$del_entry = $service->findBadwordById( (int)$_GET['delete_id'] );

	if( $del_entry != null )
	{
		$service->deleteBadwordById( (int)$_GET['delete_id'] );
		$frontend->registerMessage( 'Badword was deleted!' );
	}
	else
	{
		$frontend->registerMessage( 'No Badword to delete!', 'error' );
	}
	redirect( URL_ADMIN.'badwords.php?page='.$page.'&badword_type=' . $badwordType );
}

if( $_POST['edit'] )
{
    foreach ( $_POST['badwords'] as $key => $value )
    {
        $badword = $service->findBadwordById( $key );

        if( $badword === null )
                continue;

        $d_badword = $service->findBadwordByLabel( trim( $value ), $badwordType );

        if( $d_badword != null )
                continue;

        if ( strlen(trim($value)) > 3 )
        {
            $badword->setLabel( trim( $value ) );
            $service->saveOrUpdate( $badword );
        }
        else
        {
           $errorList[$key] = $value;
        }
    }
    
    if ( count($errorList) == 0 )
    {
        $frontend->registerMessage( 'Badwords list was updated!' );
        redirect( URL_ADMIN.'badwords.php?page='.$page.'&badword_type=' . $badwordType );
    }
}

if( $_POST['section'] && $_POST['section'] == 'badwords' )
{
	$conf = new adminConfig();
	$conf->SaveConfigs($_POST);
	$conf->getResult($frontend);
}

//********************************************//
$badwordList = array();
$badwords = $service->findBadwordList( $page, $badwordType );

foreach( $badwords as $badword  )
{
    $badwordList[$badword->getId()] = $badword;
}

$frontend->assign_by_ref( 'badwords', $badwordList );
$frontend->assign( 'error_list', $errorList );

$pages_array = array();

for( $i = 1; $i<= $pages_count; $i++ )
{
	$pages_array[] = array( 'index' => $i, 'active' => ( $i == $page ? true : false ) );
}

$frontend->assign_by_ref( 'pages', $pages_array );
$frontend->assign( 'uri', URL_ADMIN.'badwords.php?page='.$page.'&badword_type=' . $badwordType );
$frontend->assign( 'badword_type', $badwordType );

require_once( 'inc.admin_menu.php' );

$_page['title'] = "Badwords";

$frontend->display( 'badwords.html' );
?>
