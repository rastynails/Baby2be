<?php

class app_Site
{
	private static $config_section;

	public static function construct() {
		self::$config_section = SK_Config::section("site")->Section("site_status");
	}

	public static function lock() {
			self::$config_section->set("locked", 1);
	}

	public static function unlock() {
		self::$config_section->set("locked", 0);
	}


	public static function locked() {
		return (bool) (int)self::$config_section->locked;
	}

	public static function suspended() {
		return (bool) (int)self::$config_section->suspended;
	}

	public static function active() {
		return !(self::suspended() || self::locked());
	}
}

app_Site::construct();