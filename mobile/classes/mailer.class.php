<?php

define ( 'KOI8_R', 'k' );
define ( 'WINDOWS_1251', 'w' );
define ( 'ISO8859_5', 'i' );
define ( 'X_CP866', 'a' );
define ( 'X_MAC_CYRILLIC', 'm' );
define ( 'TRANSLIT', 't' );


// Message priority constants

define ( 'PRIORITY_LOW', 5 );
define ( 'PRIORITY_NORMAL', 3 );
define ( 'PRIORITY_HIGH', 1 );


// Class definition

class Mailer
{

  var $addr;  // string, recipient address
  var $subject;  // string, mail subject
  var $is_html;  // bool, is message html or plain text
  var $msg;  // string, message text or html body
  var $attachments;  // array of attached file paths
  var $to;  // string, recipient name
  var $from_addr;  // string, sender address
  var $from_name;  // string, sender name
  var $cc; // string, carbon copies
  var $bcc;  // string, blind carbon copies
  var $mailer;  // string, name of mailing program
  var $reply_to;  // string, reply address
  var $date;  // string, message date
  var $priority; //int, message priority
  var $extra_headers;  // string, custom headers
  var $cp_base;  // string, base charset
  var $tpl_containers;  // template containers array

  var $encs;  // internal private array of encodings


  /*
    Description:
      Class constructor.
    Prototype:
      void XPhpMailer ( )
  */
  function Mailer ( )
  {

    $this->encs = array ( 'k'=>'koi8-r', 'w'=>'windows-1251', 'i'=>'iso8859-5',
      'a'=>'x-cp866', 'd'=>'x-cp866', 'm'=>'x-mac-cyrillic', 't'=>'iso-8859-1' );

    $this->ClearFields ( );

  }


  /*
    Description:
      Sets class properties to default values.
    Prototype:
      void ClearFields ( )
  */
  function ClearFields ( )
  {

    $this->addr = '';
    $this->subject = '';
    $this->is_html = FALSE;
    $this->msg = '';
    $this->attachments = array ( );
    $this->to = '';
    $this->from_addr = '';
    $this->from_name = '';
    $this->cc = '';
    $this->bcc = '';
    $this->mailer = 'Mailer';
    $this->reply_to = '';
    $this->date = '';
    $this->priority = PRIORITY_NORMAL;
    $this->extra_headers = '';
    $this->cp_base = WINDOWS_1251;
    $this->tpl_containers = array ( );

  }


  /*
    Description:
      Sets the value to date property in correct format.
    Prototype:
      void FormatMsgDate ( int tm )
    Parameters:
      tm - time in UNIX format
  */
  function FormatMsgDate ( $tm )
  {

    $this->date = date ( "D, d M Y H:i:s", $tm );

  }


  /*
    Description:
      Returns total size of all attachments.
    Prototype:
      int GetAttachedSize ( )
    Return:
      Total size of attached files in bytes.
  */
  function GetAttachedSize ( )
  {

    $total_sz = 0;

    for ( $i = 0; $i < count ( $this->attachments ); $i++ )
      $total_sz += filesize ( $this->attachments[$i] );

    return $total_sz;

  }


  /*
    Description:
      Processes email template.
    Prototype:
      void ProcessTemplate ( string tpl_file )
    Parameters:
      tpl_file - Filename of template
  */
  function ProcessTemplate ( $tpl_file )
  {

    if ( ! file_exists ( $tpl_file ) )
    {
      trigger_error ( 'Template file not found!', E_USER_WARNING );
      return;
    }
    ob_start ( );
    include $tpl_file;
    $this->msg = ob_get_contents ( );
    ob_end_clean ( );
    foreach ( $this->tpl_containers as $cont=>$val )
    {
      $this->msg = str_replace ( $cont, $val, $this->msg );
    }

  }


  /*
    Description:
      Private method used to convert cyrillic charsets of message.
    Prototype:
      void _encode ( string cp_target, bool xcode = FALSE )
    Parameters:
      cp_target - Target charset
      xcode - will ignored
  */
  function _encode ( $str, $cp_target, $xcode = FALSE )
  {

    if ( $cp_target != 't' )
    {
      $str = convert_cyr_string ( $str, $this->cp_base, $cp_target );
      /*
      if ( $xcode )
      {
        $str = '=?' . $this->encs[$cp_target] . '?B?' . base64_encode ( $str );
        $str .= '=?=';
      }
      */
    }
    else
    {
      $str = strtr ( $str, 'àáâãäåçèéêëìíîïðñòóôõúûý',
        'abvgdeziyklmnoprstufh\'ie' );
      $str = strtr ( $str, 'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÚÛÝ',
        'ABVGDEZIYKLMNOPRSTUFH\'IE' );
      $str = strtr ( $str, array ( 'æ'=>'zh', 'ö'=>'ts', '÷'=>'ch',
        'ø'=>'sh', 'ù'=>'shch', 'ü'=>'', 'þ'=>'yu', 'ÿ'=>'ya', 'Æ'=>'ZH',
        'Ö'=>'TS', '×'=>'CH', 'Ø'=>'SH', 'Ù'=>'SHCH', 'Ü'=>'', 'Þ'=>'YU',
        'ß'=>'YA' ) );
    }

    return $str;

  }


  /*
    Description:
      Builds message headers string. Generally it is private method.
    Prototype:
      string ConstructHeaders ( )
    Return:
      String of formated headers.
  */
  function ConstructHeaders ( &$boundary, $encoding )
  {

    $headers = '';

    if ( ! empty ( $this->from_name ) )
    {
      $headers .= 'From: ';
      $headers .= $this->_encode ( $this->from_name, $encoding, true );
      if ( ! empty ( $this->from_addr ) )
        $headers .= ' <' . $this->from_addr . '>';
      $headers .= "\r\n";
    }

    if ( ! empty ( $this->to ) )
    {
      $headers .= 'To: ';
      $headers .= $this->_encode ( $this->to, $encoding, true );
      $headers .= ' <' . $this->addr . '>';
      $headers .= "\r\n";
    }

    if ( ! empty ( $this->cc ) )
    {
      $headers .= 'CC: ' . $this->cc . "\r\n";
    }

    if ( ! empty ( $this->bcc ) )
    {
      $headers .= 'BCC: ' . $this->bcc . "\r\n";
    }

    if ( ! empty ( $this->reply_to ) )
    {
      $headers .= 'Reply-To: ' . $this->reply_to . "\r\n";
    }

    if ( ! empty ( $this->date ) )
    {
      $headers .= 'Date: ' . $this->date . "\r\n";
    }

    $headers .= "MIME-Version: 1.0\r\n";
    $boundary = md5 ( uniqid ( time ( ), 1 ) ) . '_xphpmailer';
    $headers .= "Content-Type: multipart/mixed;\r\n\tboundary=\"";
    $headers .= "$boundary\"\r\n";

    $headers .= 'X-Priority: ' . $this->priority . "\r\n";

    if ( ! empty ( $this->mailer ) )
    {
      $headers .= 'X-Mailer: ';
      $headers .= $this->_encode ( $this->mailer, $encoding, true );
      $headers .= "\r\n";
    }

    if ( ! empty ( $this->extra_headers ) )
    {
      $headers .= $this->_encode ( $this->extra_headers, $encoding, true );
    }

    return $headers;

  }


  /*
    Description:
      Constructs the message body including attachments. Generally it is
      private method.
    Prototype:
      string ConstructMsgBody ( )
    Return:
      Message body string.
  */
  function ConstructMsgBody ( $boundary, $encoding )
  {

    $msg = 'This is a multi-part message in MIME format.' . "\r\n";

    $type = $this->is_html ? 'text/html' : 'text/plain';
    $msg .= "\r\n--$boundary\r\n";
    $msg .= "Content-Type: $type;\n\tcharset=\"";
    $msg .= $this->encs[$encoding] . "\"\r\n\r\n";
    $msg .= $this->msg . "\r\n\r\n";

    $msg = $this->_encode ( $msg, $encoding );

    for ( $i = 0; $i < count ( $this->attachments ); $i++ )
    {
      if ( ! file_exists ( $this->attachments[$i] ) )
      {
        trigger_error ( $this->attachments[$i] . ' not found!', E_USER_ERROR );
        return;
      }
      $fname = basename ( $this->attachments[$i] );
      $msg .= "\r\n--$boundary\r\n";
      $msg .= 'Content-Type: application/octetstream;';
      $msg .= "\r\n\tname=\"$fname\"\r\n";
      $msg .= 'Content-Transfer-Encoding: base64' . "\r\n";
      $msg .= 'Content-Disposition: attachment;';
      $msg .= "\r\n\tfilename=\"$fname\"\r\n\r\n";

      $f = fopen ( $this->attachments[$i], "r" );
      $fcont = fread ( $f, filesize ( $this->attachments[$i] ) );
      fclose ( $f );
      $fcont = chunk_split ( base64_encode ( $fcont ) );

      $msg .= "$fcont\r\n\r\n";
    }

    $msg .= "\r\n--$boundary--\r\n";

    return $msg;

  }


  /*
    Description:
      Sends the message.
    Prototype:
      bool SendMail ( string encoding )
    Parameters:
      encoding - target charset of message
    Return:
      TRUE if the mail was successfully accepted for delivery, FALSE otherwise.
  */
  function SendMail ( $encoding = '' )
  {
//echo "test";
    if ( empty ( $this->addr ) )
    {
      trigger_error ( 'No recipient address!', E_USER_WARNING );
      return FALSE;
    }

    if ( empty ( $encoding ) )
    {
      $encoding = $this->cp_base;
    }

    $subject = $this->_encode ( $this->subject, $encoding, true);
    $headers = $this->ConstructHeaders ( $boundary, $encoding );
    $msg = $this->ConstructMsgBody ( $boundary, $encoding );

    return mail ( $this->addr, $subject, $msg, $headers );

  }

}

?>