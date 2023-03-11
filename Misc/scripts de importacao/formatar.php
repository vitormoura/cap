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
		$temp = explode( '-', &$data );
		
		//Inicialmente formatamos na ordem do Mysql YYYY-MM-DD
		//$data = $temp[2].'-'.$temp[1].'-'.$temp[0];
		
		//Data em formato numerico:
		$data = mktime(0,0,0, $temp[1], $temp[0], $temp[2]);
		
		return "'".$data."'";
	}
	
?>