<?php
//Biblioteca de funções Mysql

//Configuraçoes gerais:

	$usuario = 'cap2';
	$senha   = '567casc34';
	$host    = 'localhost';
	$db      = 'intranet';

	
//Corpo do Driver, não altere qualquer linha a partir deste ponto.
	$conn    = @mysql_connect( $host, $usuario, $senha ) or die('Erro durante a conexão com a base de dados');
			   @mysql_select_db( $db );

			   
	function dbQuery( $query )
	{
		global $conn;
			   $amostra = strtoupper( substr( $query, 0, 7 ) );
		
		switch ( $amostra )
		{
			//No caso de Selects, retornamos uma fonte de dados mysql para ser capturada.
			case 'SELECT ':
				$resultSet = @mysql_query( $query, $conn ) or die( 'O banco de dados não responde, por favor contate o administrador : <br>'.mysql_error() );
				return $resultSet;
			break;
			
			//No caso de Inserts, retornamos o código do registro inserido.
			case 'INSERT ':
				@mysql_query( $query, $conn ) or die( 'O banco de dados não responde, por favor contate o administrador: <br>'.mysql_error());
				return mysql_insert_id( $conn );
			break;
			
			//No caso de Deletes ou Inserts, retornamos o número de linhas afetadas.
			case 'DELETE ':
			case 'UPDATE ':
				@mysql_query( $query, $conn ) or die( 'O banco de dados não responde, por favor contate o administrador: <br>'.mysql_error());
				return mysql_affected_rows( $conn );
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
			return @mysql_fetch_assoc( $rs );
			break;
			
			case 'NUM':
			return @mysql_fetch_row( $rs );
			break;
			
			case 'BOTH':
			return @mysql_fetch_array( $rs );
			break;
		}
	}
	
	function howMany( &$rs )
	{
		return @mysql_num_rows( $rs );
	}
	
?>