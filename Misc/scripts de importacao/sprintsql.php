<?php

function sprintSql( $query )
{
		
	$n        = func_num_args();			//Quantidade de argumentos
	$pos      = strpos($query, '%', 0 );	//Posiчуo da primeira occorencia de %
	$flags    = array( 's', 'f', 'd', 't');	//Array com o nome das funчѕes de tratamento
	$args     = func_get_args();			//Argumentos passados pelo usuсrio
	$x        = 1;							//Argumento atualmente sendo utilizado
		
	
	while( $pos !== false && $n > 1 )
	{
	
		$flag = ( $pos + 1 >= strlen( $query ) )? '' : $query{$pos + 1};
			
		if( in_array( $flag, $flags ))
		{	
			@require_once('formatar.php');
				
			$arg   = $flag( $args[$x] );
			$query = substr_replace($query, $arg, $pos ).substr( $query, $pos + 2);
	
			$x++;
		}
		else 
		{
			$arg = ' ';
		}

		$pos = strpos($query, '%', $pos + strlen( $arg ));	
	
		if( $x > ($n - 1) )
			break;	
			
	}
	
	return $query;
}
?>