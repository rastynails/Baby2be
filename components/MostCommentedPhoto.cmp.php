<?php

class component_MostCommentedPhoto extends component_PhotoList 
{
	public function items()
	{
		return app_PhotoList::MostCommented();
	}
}
