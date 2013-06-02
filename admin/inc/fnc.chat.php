<?php

function getChuppoGenderList()
{
	$query = "SELECT * FROM `".TBL_LINK_CHUPPO_GENDER."`";
	$query_result = SK_MySQL::query($query);
	
	while ($row = $query_result->fetch_assoc()) {
		$result[] = $row;
	}
	
	return $result;
}

function addChuppoGender( $sex, $gender )
{
	$sex = intval( $sex );
	$gender = trim( $gender );
	
	if ( !$sex || !$gender )
		return false;
	
	$query = SK_MySQL::placeholder( "INSERT INTO `".TBL_LINK_CHUPPO_GENDER."`( `sex`, `chuppo_gender` )
		VALUES( ?, '?' )", $sex, $gender );
	
	SK_MySQL::query($query);
	
	return SK_MySQL::affected_rows();
}

function deleteChuppoGender( $sex )
{
	$sex = intval( $sex );
	
	if ( !$sex )
		return false;
	
	$query = SK_MySQL::placeholder( "DELETE FROM `".TBL_LINK_CHUPPO_GENDER."`
		WHERE `sex`=?", $sex );
	
	SK_MySQL::query($query);
	
	return SK_MySQL::affected_rows();
}

?>