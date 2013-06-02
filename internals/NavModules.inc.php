<?php

if ( SK_Config::Section('navigation')->Section("settings")->mod_rewrite) {
	
	SK_Navigation::LoadModule("photo");
	SK_Navigation::LoadModule("profile");
	SK_Navigation::LoadModule("video");
        SK_Navigation::LoadModule("music");


}