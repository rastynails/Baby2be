<?php

/**
 * Replace occurrences of the search string with the replacement string
 *
 * @param string $search
 * @param string $replace
 * @param string $subject
 * @param int $depth
 * @return string
 */
function sk_str_dreplace($search, $replace, $subject, $depth = 1) {
	$first_pos = strpos($subject, $search);
	if ($first_pos === false) {
		return $subject;
	}
	$result = substr_replace($subject, $replace, $first_pos, strlen($search));
	if ($depth > 1) {
		$sub_str_start = $first_pos + strlen($replace);
		$sub_str = substr($result, $sub_str_start);
		$processed_sub_str = sk_str_dreplace($search, $replace, $sub_str, $depth - 1);
		$result = substr_replace($result, $processed_sub_str, $sub_str_start);
	}
	return $result;
}