<?php
class component_ModeratorList extends component_ProfileList
{
	public function __construct()
	{
		parent::__construct('moderator_list');
	}

	protected function profiles()
	{
		return app_ProfileList::ModeratorList();
	}
}