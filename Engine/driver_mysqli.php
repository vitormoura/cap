<?php
//Biblioteca de funções mysqli

//Configuraçoes gerais:

	$usuario = 'root';
	$senha   = 'avantguard';
	$host    = 'localhost';
	$db      = 'intranet';

	
//Corpo do Driver, não altere qualquer linha a partir deste ponto.
	$conn    = mysqli_connect( $host, $usuario, $senha, $db ) or die('Erro durante a conexão com a base de dados');
				   
	function dbQuery( $query )
	{
		global $conn;
			   $amostra = strtoupper( substr( $query, 0, 7 ) );
		
		switch ( $amostra )
		{
			//No caso de Selects, retornamos uma fonte de dados mysqli para ser capturada.
			case 'SELECT ':
				$resultSet = @mysqli_query( $conn, $query ) or die( 'O banco de dados não responde, por favor contate o administrador : <br>'.mysqli_error() );
				return $resultSet;
			break;
			
			//No caso de Inserts, retornamos o código do registro inserido.
			case 'INSERT ':
				@mysqli_query( $conn, $query ) or die( 'O banco de dados não responde, por favor contate o administrador: <br>'.mysqli_error());
				return mysqli_insert_id( $conn );
			break;
			
			//No caso de Deletes ou Inserts, retornamos o número de linhas afetadas.
			case 'DELETE ':
			case 'UPDATE ':
				@mysqli_query( $conn, $query ) or die( 'O banco de dados não responde, por favor contate o administrador: <br>'.mysqli_error());
				return mysqli_affected_rows( $conn );
			break;
			
			//Caso seja qualquer outro tipo de querie... bye bye execução.
			default:
				die('Instrução enviada ao banco de dados considerada inválida');
							
		}
		
	}
	
	function rsFetch( &$rs, $tipo = 'ASSOC' )
	{
		switch( $tipo )
		{
			case 'ASSOC':
			return @mysqli_fetch_assoc( $rs );
			break;
			
			case 'NUM':
			return @mysqli_fetch_row( $rs );
			break;
			
			case 'BOTH':
			return @mysqli_fetch_array( $rs );
			break;
		}
	}
	
	function howMany( &$rs )
	{
		return @mysqli_num_rows( $rs );
	}
	
	function quote( $string ) {
		
		return addslashes( trim( $string ) );
	}
	
	function unquote( $string ) {
		
		return stripslashes( $string );
	}
	
?>