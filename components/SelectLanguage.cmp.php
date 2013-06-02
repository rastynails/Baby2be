<?php

class component_SelectLanguage extends SK_Component
{
	public function __construct( array $params = null )
	{
		parent::__construct('select_language');
	}


	public function render( SK_Layout $Layout )
	{
		if (!SK_Config::section("site")->Section("additional")->Section("profile")->allow_lang_switch) {
			return false;
		}

		$languages = SK_LanguageEdit::getLanguages();

		$Layout->assign('active_lang_id',SK_HttpUser::language_id());
		foreach ($languages as $key=>$item) {
			if ((bool) $item->enabled) {
				$languages[$key]->href = sk_make_url(null,"language_id=" . $item->lang_id);
			} else {
				unset($languages[$key]);
			}
		}

		if (!(count($languages) > 1)) {
			return false;
		}

		$Layout->assign('languages', $languages);
		return parent::render($Layout);
	}

}
