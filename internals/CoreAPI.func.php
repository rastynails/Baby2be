<?php

function Object( array $properties )
{
	$object = new stdClass;
	
	foreach ( $properties as $name => $value ) {
		$object->$name = $value;
	}
	
	return $object;
}
