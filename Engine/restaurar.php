<?php
//Restaura os formatos para um tipo conhecido e utilizado pela aplicaчуo:

function restaurar( $amostra, $format = 'string' )
{
	switch ( $format )
	{
		case 'valor' :
		return number_format( $amostra, 2, ',','.');
		break;
		
		case 'data' :
		return date("d-m-Y", $amostra );
		break;
		
		case 'coment':
		return str_replace(';',"<br>\n", $amostra );
		break;
		
		case 'string' :
		default:
		
		return stripslashes( $amostra );
		break;
	}
}

function testarDatas( &$dt1, &$dt2 )
{
	//Recebe duas datas no formato DD-MM-YYYY e testa se estуo presentes ou nуo, de acordo, retornam valores defaults em um array
	
	$values = array();
	
	if( !empty( $dt2 ) && !empty( $dt1 ) )
	{
		$values[0] = $dt1;
		$values[1] = $dt2;
	}
	else if( !empty( $dt2 ) && empty( $dt1 ))
	{
		$values[0] = date("d-m-Y", mktime(0,0,0,1,1,1970));
		$values[1] = $dt2;
	}
	else if( empty( $dt2 ) && !empty( $dt1 ))
	{
		$values[0] = $dt1;
		$values[1] = $dt1; //date("d-m-Y", mktime(0,0,0,1,1,2010));
	}
	else 
	{
		$values[0] = date("d-m-Y", mktime(0,0,0,1,1,1970));
		$values[1] = date("d-m-Y", mktime(0,0,0,12,31,2010));
	}
	
	return $values;
}
?>