<?php

function smarty_function_item_budget_price( array $params, SK_Layout $Layout )
{	
	if ( $params['entity'] == 'wanted' ) 
	{
		if ( $params['budget_min'] && $params['budget_max'] ) {
			$result = $params['currency'].'<b>'.$params['budget_min'].'</b> - '.$params['currency'].'<b>'.$params['budget_max'].'</b>';
		}
		elseif ( $params['budget_min'] ) {
			$result = SK_Language::text('components.cls.budget_from').' '.$params['currency'].'<b>'.$params['budget_min'].'</b>';
		}
		elseif ( $params['budget_max'] ) {
			$result = SK_Language::text('components.cls.budget_to').' '.$params['currency'].'<b>'.$params['budget_max'].'</b>';
		}
		else {
			$result = SK_Language::text('components.cls.undefined');;
		}
	}
	else 
	{
		if ( $params['price'] ) {
			$result = $params['currency'].'<b>'.$params['price'].'</b>';		
	
		}
		else {
			$result = SK_Language::text('components.cls.undefined');;
		}
		
	}
	
	return $result;
}
