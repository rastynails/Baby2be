<?php

class component_AffiliateSignUp extends SK_Component
{
	/**
	 * Component AffiliateSignUp constructor.
	 *
	 * @return component_AffiliateSignUp
	 */
	public function __construct( array $params = null )
	{
		parent::__construct('affiliate_sign_up');
		
	}

	public function render( SK_Layout $Layout )
	{

		return parent::render($Layout);
	}

}

