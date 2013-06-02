<?php

function int2color( $num )
{
	switch( $num )
	{
		case 10: return 'AA';
		case 11: return 'BB';
		case 12: return 'CC';
		case 13: return 'DD';
		case 14: return 'EE';
		case 15: return 'FF';
		default: return $num.$num;
	}
}

$colors = array();
for($i0=0; $i0 < 16; $i0+=3)
	for($i1=0; $i1 < 16; $i1+=3)
		for($i2=0; $i2 < 16; $i2+=3)
			$colors[] = '#'.int2color($i0).int2color($i1).int2color($i2);

$this->_tpl_vars['colors'] = $colors;

?>