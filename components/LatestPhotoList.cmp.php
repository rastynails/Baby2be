<?php

class component_LatestPhotoList extends component_PhotoList 
{
	public function items()
	{
		return app_PhotoList::LatestPhotos();
    }
	
}