<?php

require_once DIR_PHPMAILER . 'class.phpmailer.php';
require_once DIR_PHPMAILER . 'class.smtp.php';

class app_Mail
{
	const FAST_MESSAGE = 'SK_EmailMessage';
	const NOTIFICATION = 'SK_EmailNotification';
	const CRON_MESSAGE = 'SK_CronEmailMessage';

	const PRIORITY_HIGHT = 1;
	const PRIORITY_NORMAL = 3;
	const PRIORITY_LOW = 5;

	public static function QueueProcess($priority = null) {
		$priority_cond = ($priority = intval($priority)) ? "WHERE `priority`=$priority" : "ORDER BY `priority`";
		$result = SK_MySQL::query("SELECT * FROM `" . TBL_MAIL . "` $priority_cond LIMIT " . MAIL_SEND_LIMIT );

        $idListToDelete = array();

		while ($item = $result->fetch_assoc()) {
			try {
				$msg = new SK_DBEmailMessage($item);
				$msg->send();
			} catch (SK_EmailException $e) {}

			$idListToDelete[] = $item['mail_id'];
		}

        if( !empty($idListToDelete) )
        {
            SK_MySQL::query(SK_MySQL::placeholder("DELETE FROM `" . TBL_MAIL . "` WHERE `mail_id` IN (?@)", $idListToDelete));
        }
	}

	/**
	 * Creates new instance of SK_EmailMessage
	 *
	 * @return SK_EmailMessage
	 */
	public static function createMessage($message_type = self::CRON_MESSAGE) {
		if ( !in_array($message_type, array(self::FAST_MESSAGE, self::NOTIFICATION, self::CRON_MESSAGE)) ) {
			throw new SK_EmailException("Undefined message type `$message_type`", SK_EmailException::UNDEFINED_MESSAGE_TYPE);
		}
		return new $message_type();
	}

	/**
	 * Sends email
	 *
	 * @param SK_BaseEmailMessage $msg
	 * @return bool
	 */
	public static function send(SK_BaseEmailMessage $msg) {
		return $msg->send();
	}

        public static function testSMTPConnect()
        {
            $smtp_config = SK_Config::section('email.smtp');

            if ( !$smtp_config->enable )
            {
                return false;
            }

            $mail = new PHPMailer(true);

            if ( $smtp_config->connection_prefix != 'no' )
            {
                $mail->SMTPSecure = $smtp_config->connection_prefix;
            }

            $mail->IsSMTP();

            $mail->SMTPAuth   = true;
            $mail->Host = $smtp_config->host;

            if ($port = intval($smtp_config->port))
            {
                $mail->Port = $port;
            }

            $mail->Username = $smtp_config->user;
            $mail->Password = $smtp_config->password;

            try
            {
                $mail->SmtpConnect();
            }
            catch ( phpmailerException $e )
            {
                throw new Exception($e->getMessage());
            }

            $mail->SmtpClose();

            return true;
        }
}


abstract class SK_BaseEmailMessage
{
	protected $tpl_section = 'mail_template';

	protected $tpl_name;

	protected $assign_vars = array();

	protected $priority = 3;

	protected $language_id;

	protected $profile_id;

	protected $to_email;

	protected $from_email;

	protected $from_name;

	protected $msg_content = array(
				'subject'=>null,
				'txt'=>null,
				'html'=>null);

	public function __construct() {

		$config = SK_Config::section('site')->Section('official');
		$this->from_email = $config->no_reply_email;
		$this->from_name = $config->site_name;

		//default assign vars
		$this->assignVar('site_name', $config->site_name)
			 ->assignVar('site_email_main', $config->site_email_main)
			 ->assignVar('site_email_billing', $config->site_email_billing)
			 ->assignVar('site_email_support', $config->site_email_support)
			 ->assignVar('site_url', SITE_URL);
	}

	//------------------------< setters >----------------------------------

	/**
	 * Sets message template
	 *
	 * @param string $tpl_name
	 * @return SK_EmailMessage
	 */
	public function setTpl($tpl_name) {
		if (!($tpl_name = trim($tpl_name))) {
			throw new SK_EmailException('Wrong template name', SK_EmailException::WRONG_TPL_NAME);
		}

		$this->tpl_name = $tpl_name;
		return $this;
	}

	/**
	 * Assigns var to message
	 *
	 * @param string $name
	 * @param string $value
	 * @return SK_EmailMessage
	 */
	public function assignVar($name, $value, $overwrite = true) {
		if ( ($name = trim($name)) && isset($value)) {
			if (!$overwrite && isset($this->assign_vars[$name])) {
				return $this;
			}
			$this->assign_vars[$name] = $value;
		}
		return $this;
	}

	/**
	 * Assigns range of vars to message
	 *
	 * @param array $range
	 * @return SK_EmailMessage
	 */
	public function assignVarRange($range) {
		if (is_array($range)) {
			$this->assign_vars = array_merge($range, $this->assign_vars);
		}
		return $this;
	}

	/**
	 * Sets message priority. Available values 1 - important,2,3
	 *
	 * @param int $priority
	 * @return SK_EmailMessage
	 */
	public function setPriority($priority = 3) {
		$this->priority = ($priority > 0 && $priority <=3) ? $priority : 3;
		return $this;
	}

	/**
	 * Sets message recipient profile id
	 *
	 * @param string $profile_id
	 * @return SK_EmailMessage
	 */
	public function setRecipientProfileId($profile_id) {
		if ( !($profile_id = intval($profile_id)) ) {
			throw new SK_EmailException("Wrong profile id", SK_EmailException::WRONG_PROFILE_ID);
		}
		$this->profile_id = $profile_id;
		return $this;
	}

	/**
	 * Sets recipient email address
	 *
	 * @param string $email_addr
	 * @return SK_EmailMessage
	 */
	public function setRecipientEmail($email_addr) {
		if ( !($email_addr = trim($email_addr)) ) {
			throw new SK_EmailException('Wrong recipient email address', SK_EmailException::WRONG_RECIPIENT_EMAIL_ADDR);
		}
		$this->to_email = $email_addr;
		return $this;
	}

	/**
	 * Sets recipient langusge id
	 *
	 * @param int $language_id
	 * @return SK_EmailMessage
	 */
	public function setRecipientLangId($language_id) {
		if ( !($language_id = intval($language_id)) ) {
			$this->language_id = SK_Config::Section('languages')->default_lang_id;
		} else {
			$this->language_id = $language_id;
		}
		return $this;
	}

	/**
	 * Sets sender email
	 *
	 * @param string $email
	 * @return SK_EmailMessage
	 */
	public function setSenderEmail($email) {
		if ( !($email = trim($email)) ) {
			throw new SK_EmailException('Wrong sender email address', SK_EmailException::WRONG_SENDER_EMAIL_ADDR);
		}
		$this->from_email = $email;
		return $this;
	}

	/**
	 * Sets sender name
	 *
	 * @param string $name
	 * @return SK_EmailMessage
	 */
	public function setSenderName($name) {
		if ( !($name = trim($name)) ) {
			throw new SK_EmailException('Wrong sender name', SK_EmailException::WRONG_SENDER_NAME);
		}
		$this->from_name = $name;
		return $this;
	}

	/**
	 * Sets message subject
	 *
	 * @param string $subject
	 * @return SK_EmailMessage
	 */
	public function setSubject($subject) {
		if ( !($subject = trim($subject)) ) {
			throw new SK_EmailException('Wrong subject', SK_EmailException::WRONG_SUBJECT);
		}
		$this->msg_content['subject'] = str_replace("\n", ' ', $subject);
		return $this;
	}

	/**
	 * Sets message content
	 *
	 * @param string $txt
	 * @param string $html
	 * @return SK_EmailMessage
	 */
	public function setContent($txt, $html=null) {
		if ( !($txt = trim($txt))
			&& !(isset($html) && ($html = trim($html)))
		) {
			throw new SK_EmailException('Wrong message content', SK_EmailException::WRONG_MESSAGE_CONTENT);
		}
		$this->msg_content['txt'] = $txt;
		if ( isset($html) && strlen($html) ) {
			$this->msg_content['html'] = $html;
		}
		return $this;
	}

	//------------------------< / setters >----------------------------------


	//------------------------< processors >---------------------------------

	/**
	 * Process recipient profile id
	 *
	 * @return SK_EmailMessage
	 */
	protected function processRecipientProfileId() {
		if (!$this->profile_id) {
			return $this;
		}

		$vars = app_Profile::getFieldValues($this->profile_id, array('language_id', 'email'));
		$vars['username'] = app_Profile::username($this->profile_id);

		if (!count($vars) && !isset($this->to_email)) {
			throw new SK_EmailException('Undefined profile', SK_EmailException::UNDEFINED_PROFILE);
		}

		$this->assignVar('profile_username', $vars['username'], false)
			 ->assignVar('username', $vars['username'], false)
			 ->assignVar('email', $vars['email'], false)
			 ->assignVar('profile_email', $vars['email'], false)
             ->assignVar('unsubscribe_url', app_Unsubscribe::getUnsubscribeLink($this->profile_id));

		if (!isset($this->to_email)) {
			$this->to_email = $vars['email'];
		}

		if (isset($this->language_id)) {
			return $this;
		}

		if ($lang_id = intval($vars['language_id'])) {
			$this->language_id = $lang_id;
		} else {
			$this->language_id = SK_Config::Section('languages')->default_lang_id;
		}

		return $this;
	}

	/**
	 * Process ricipient email address
	 *
	 * @param string $name
	 * @param string $value
	 * @return SK_EmailMessage
	 */
	public function processRecipientEmail() {
		if (!isset($this->to_email) || isset($this->profile_id)) {
			return $this;
		}

		$query = SK_MySQL::placeholder("SELECT `profile_id` FROM `" . TBL_PROFILE . "` WHERE `email`='?'", $this->to_email);
		$profile_id = SK_MySQL::query($query)->fetch_cell();
		if ($profile_id = intval($profile_id)) {
			$this->setRecipientProfileId($profile_id);
		}
		return $this;
	}

	/**
	 * Fills message content from template
	 *
	 * @return SK_EmailMessage
	 */
	protected function processTpl() {
		if (!isset($this->tpl_name)) {
			return $this;
		}

		$this->language_id = isset($this->language_id) ? $this->language_id : SK_Config::Section('languages')->default_lang_id;
		$language = SK_Language::instance($this->language_id);
		$section = SK_Language::section($this->tpl_section, $language);

		$txt = '';
		$subject = '';

		try
		{
			$txt = $section->cdata($this->tpl_name . '_body_txt');
			$subject = $section->cdata($this->tpl_name . '_subject');
		}
		catch (SK_LanguageException $e)	{}


    	try
        {
            if ( empty($txt) || empty($subject) )
            {
                $section = SK_Language::section($this->tpl_section);

                $txt = $section->cdata($this->tpl_name . '_body_txt');
                $subject = $section->cdata($this->tpl_name . '_subject');
            }
        }
        catch (SK_LanguageException $e)
        {
            throw new SK_EmailException("Email template `$this->tpl_name` does not exist in section `$this->tpl_section`!"
                                        , SK_EmailException::EMAIL_TPL_NOT_EXISTS );
        }

		$html = null;
		try
		{
			$html = $section->cdata($this->tpl_name . '_body_html');
		}
		catch (SK_LanguageException $e) {};

		$this->setSubject($subject)->setContent($txt, $html);
		return $this;
	}

	//------------------------< / processors >---------------------------------







	/**
	 * Replaces assign vars in message content
	 *
	 * @return SK_EmailMessage
	 */
	private function replaceAssignVars() {
		foreach ($this->msg_content as $key => $item) {
			$this->msg_content[$key] = SK_Language::exec($item, $this->assign_vars);
		}
		return $this;
	}

	/**
	 * Sends message
	 *
	 * @return SK_EmailMessage
	 */
	public function send() {
		$this->onSend();
		return $this->sendProcess();
	}

	protected function onSend() {

		$this->processRecipientEmail();

		$this->processRecipientProfileId();

		$this->processTpl();

		$this->replaceAssignVars();
	}

	/**
	 * Sends messages
	 * @return bool
	 */
	protected abstract function sendProcess();

}

class SK_CronEmailMessage extends SK_BaseEmailMessage
{
	/**
	 * @see SK_EmailMessage::sendProcess()
	 *
	 */
	protected function sendProcess() {
		$query = SK_MySQL::placeholder( "INSERT INTO `" . TBL_MAIL . "`
			( `reciever_mail`, `subject`, `text_body`, `html_body`, `sender_mail`, `sender_name`, `priority`, `sent_date` )
			VALUES( '?@', ?, ?)", array(
						$this->to_email,
						$this->msg_content['subject'],
						$this->msg_content['txt'],
						$this->msg_content['html'],
						$this->from_email,
						$this->from_name
					)
					, $this->priority
					, time()
		);
		SK_MySQL::query($query);
		return (bool) SK_MySQL::affected_rows();
	}

}

class SK_EmailMessage extends SK_BaseEmailMessage
{

	/**
	 * @see SK_EmailMessage::sendProcess()
	 *
	 * @return bool
	 */
	protected function sendProcess() {
		$is_html = strlen( trim($this->msg_content['html']) ) ? true : false;

		try {
			$mail = new PHPMailer(true);

			$smtp_config = SK_Config::section('email.smtp');

			if ($smtp_config->enable)
			{
				if ($smtp_config->connection_prefix != 'no') {
					$mail->SMTPSecure = $smtp_config->connection_prefix;
				}
				$mail->IsSMTP();
				$mail->SMTPAuth   = true;
				$mail->Host       = $smtp_config->host;
				if ($port = intval($smtp_config->port)) {
					$mail->Port = $port;
				}
				$mail->Username   = $smtp_config->user;
				$mail->Password   = $smtp_config->password;

			}

			$mail->SetFrom($this->from_email, $this->from_name);
			$mail->AddReplyTo($this->from_email, $this->from_name);
			$mail->Sender = $this->from_email;
			$mail->AddAddress($this->to_email);
			$mail->Subject = $this->msg_content['subject'];
	        $mail->IsHTML($is_html);
	        $mail->Body = $is_html ? $this->msg_content['html'] : $this->msg_content['txt'];
	        $mail->AltBody = $is_html ? $this->msg_content['txt'] : '' ;

		    $result = $mail->Send();
		} catch (phpmailerException $e) {
			if (DEV_MODE) {
				throw new SK_EmailException( $e->getMessage(), SK_EmailException::PHPMAILER_EXCEPTION);
			}
		}

	    return $result;
	}

}

class SK_EmailNotification extends SK_EmailMessage {}

class SK_DBEmailMessage extends SK_EmailMessage
{
	public function __construct($params = array()) {
		parent::__construct();
		if (count($params)) {
			$this->autoConstruct($params);
		}
	}

	private function autoConstruct($params) {
		$this->setPriority($params['priority'])
			 ->setRecipientEmail($params['reciever_mail'])
			 ->setSenderEmail($params['sender_mail'])
			 ->setSenderName($params['sender_name'])
			 ->setSubject($params['subject'])
			 ->setContent($params['text_body'], $params['html_body']);
	}

	/**
	 * @see SK_BaseEmailMessage::onSend()
	 *
	 */
	protected function onSend() {}


}

class SK_EmailException extends Exception
{
	const EMAIL_TPL_NOT_EXISTS = 1;
	const UNDEFINED_PROFILE = 2;
	const WRONG_PROFILE_ID = 3;
	const WRONG_RECIPIENT_EMAIL_ADDR = 4;
	const WRONG_SENDER_EMAIL_ADDR = 5;
	const WRONG_SENDER_NAME = 6;
	const WRONG_SUBJECT = 7;
	const WRONG_MESSAGE_CONTENT = 8;
	const UNDEFINED_MESSAGE_TYPE= 9;
	const WRONG_TPL_NAME= 10;
	const WRONG_LANG_ID= 11;
	const PHPMAILER_EXCEPTION = 12;
}
