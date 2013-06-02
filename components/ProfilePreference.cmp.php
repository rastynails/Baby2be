<?php

class component_ProfilePreference extends SK_Component
{
	private $preferences = array();

	public function __construct( array $params = null )
	{
		parent::__construct('profile_preference');
	}

	public function prepare(SK_Layout $Layout, SK_Frontend $Frontend) {
		$profile_id = SK_HttpUser::profile_id();
		$this->preferences = app_ProfilePreferences::getList();

//                $private_service = new SK_Service( 'private_status', SK_httpUser::profile_id() );
//
//                if( $private_service->checkPermissions() !== SK_Service::SERVICE_FULL )
//                {
//                    unset($this->preferences['my_profile']['is_profile_private']);
//                }

		if (!app_Features::isAvailable(23)) {
                    unset($this->preferences['blogs']);
		}

		if (!app_Features::isAvailable(25)) {
                    unset($this->preferences['my_profile']['friends_only_comments']);
		}

		if (!app_Features::isAvailable(7) && !app_Features::isAvailable(8)) {
                    unset($this->preferences['mailbox']);
		} else {
			if (!app_Features::isAvailable(8)) {
                            unset($this->preferences['mailbox']['notify_kisses']);
			}
			if (!app_Features::isAvailable(7)) {
                            unset($this->preferences['mailbox']['notify_messages']);
			}
		}

	    if (!app_Features::isAvailable(11)) {
                unset($this->preferences['my_profile']['hide_im_btn']);
            }

	   if (!app_Features::isAvailable(8)) {
            unset($this->preferences['gifts']);
        }

	    if (!app_Features::isAvailable(51)) {
            unset($this->preferences['matches']);
        }

	   if (!app_Features::isAvailable(14)) {
            unset($this->preferences['friend_network']);
            unset($this->preferences['my_profile']['friends_only_comments']);
            unset($this->preferences['my_profile']['is_profile_private']);
        }

		$Layout->assign('preferences', $this->preferences);

		$handler = new SK_ComponentFrontendHandler('ProfilePreference');

		if (app_ProfilePreferences::hasCustoms()) {
			$handler->showRestoreBtn();
		}

		$this->frontend_handler = $handler;

		return parent::prepare($Layout, $Frontend);
	}

	public function handleForm(SK_Form $Form) {

		foreach ($this->preferences as $section_name => $section){
			foreach ($section as $conf_name => $config) {
				if ($config["value"]) {
					$Form->fields[$section_name . "___" . $conf_name]->setValue($config["value"]);
				}
			}
		}

		$Form->frontend_handler->bind("success", 'function(result){
			if (result) {
				this.ownerComponent.showRestoreBtn();
			}
		}');
	}

	public static function ajax_restoreDefaults($params = null, SK_ComponentFrontendHandler $handler){

		try {
			app_ProfilePreferences::resetConfigs();
		} catch (SK_ProfilePreferencesException $e) {
			if (!$e->getCode() == 1) {
				$handler->showSignIn();
			}
			return false;
		}

		$items = app_ProfilePreferences::getList();
		foreach ($items as $section_name => $section){
			foreach ($section as $conf_name => $config) {
				if ($config["value"]) {
					$handler->setValue($section_name . '___' . $conf_name, $config["value"]);
				} else {
					$handler->setValue($section_name . '___' . $conf_name, "");
				}
			}
		}
		$handler->message(SK_Language::text("components.profile_preference.restore_complete"));
		return true;
	}
}
