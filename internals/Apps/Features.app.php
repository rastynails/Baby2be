<?php

class app_Features 
{
	private static $available_features = array();
	
	public static function isAvailable($feature_id) {
		$features = self::availableList();
        if (empty($features[$feature_id]))
            return false;
		return (bool)$features[$feature_id];
	}
	
	public static function availableList() {
		if (count(self::$available_features)) {
			return self::$available_features;
		}
		
		$result = SK_MySQL::query("SELECT `feature_id`,`name` FROM `".TBL_FEATURE."` WHERE `active`='yes'");
		while ($item = $result->fetch_object()) {
			self::$available_features[$item->feature_id] = $item->name;
		}
		return self::$available_features;
	}
}