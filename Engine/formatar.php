<?PHP
//Funчѕes de tratamento de formatos ( Chamados Principalmente pela funчуo SprintSql )

	function s( $string )
	{
		return "'".addslashes(trim( &$string ))."'";
	}
	
	function f( $float )
	{
		//Inicialmente precisamos retirar a mascara...
		$float = str_replace(',','.', str_replace('.','', &$float));
		return floatval( &$float );
	}
	
	function d( $decimal )
	{
		return intval( &$decimal );
	}
	
	function t( $data )
	{
		$temp = explode( '-', $data );
		
		//Inicialmente formatamos na ordem do Mysql YYYY-MM-DD
		//$data = $temp[2].'-'.$temp[1].'-'.$temp[0];
		
		//Data em formato numerico:
		$data = mktime(0,0,0, intval($temp[1]), intval($temp[0]), intval($temp[2]));
		
		//Modifiquei o retorno para INT, estou com medo de efeitos adversos( caso 13/02/2006 )
		//return "'".$data."'";
		return $data;
	}
	
?>