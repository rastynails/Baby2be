<?php

class form_ProfilePreference extends SK_Form
{
	
	public $items = array();
	
	public function __construct()
	{
		parent::__construct('profile_preferences');
	}
	
	
	public function setup()
	{
		$items = app_ProfilePreferences::getList();
		
		foreach ($items as $section_name => $section) {
			foreach ($section as $name => $item) {
				switch ($item["presentation"]) {
					case 'checkbox':
						parent::registerField( new fieldType_checkbox($section_name . '___' . $name));
						break;
					case "text":
						parent::registerField( new fieldType_text($section_name . '___' . $name));
						break;
				}
			}
		}
		
		parent::registerAction('form_ProfilePreferences_Process');
	}
	
	
	
}

class form_ProfilePreferences_Process extends SK_FormAction
{
	public function __construct()
	{
		parent::__construct('save');
	}
	
	private function checkPermissions($section, $config, $value) {
		switch ($section) {
			case 'my_profile':
				switch ($config) {
					case 'hide_im_btn':
					case 'hide_online_activity':
						if (!$value) {
							return true;
						}
						
						$service = new SK_Service($config);
						
						if ($service->checkPermissions() == SK_Service::SERVICE_FULL) {
							$service->trackServiceUse();
							return true;
						} else {
							return $service->permission_message['message'];
						}
                                                break;

                                         case 'is_profile_private':
						if (!$value) {
							return true;
						}

                                                $service = new SK_Service( 'private_status' );

						if ($service->checkPermissions() === SK_Service::SERVICE_FULL) {
							$service->trackServiceUse();
							return true;
						} else {
							return $service->permission_message['message'];
						}
                                                break;
				}
		}
		return true;
	}
	
	public function process( array $post_data, SK_FormResponse $response, SK_Form $from )
	{
		$result = 0;
		foreach ($post_data as $name => $value) {
			list($section, $config) = explode('___', $name);
			$permission = $this->checkPermissions($section, $config, $value);
			if ($permission !== true) {
				$response->addError($permission);
				$response->exec("
					(function(input){
						var last_val = input.attr('checked')
						input.attr('checked', !last_val);
						
					})($(this.\$form.get(0)['$name']));
				");
				continue;
			}
			
			if ($section && $config) {
				try {
					$result += (int)app_ProfilePreferences::set($section, $config, $value);
				} catch (SK_ProfilePreferencesException $e){
					if ($e->getCode() == 1) {
						$response->exec("SK_SignIn()");
					} else {
						$response->addError($e->getMessage());
					}
					return false;
				}
			}
		}
		if ($result) {
			$response->addMessage(SK_Language::text("components.profile_preference.success"));	
			return true;
		}
		return false;
	}
}
