<?php

class component_MostViewed extends component_PhotoList
{
	public function items()
	{
        return app_PhotoList::MostViewed();
    }

}