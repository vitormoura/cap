<?php
//Arquivo de importação dos dados da versão 1.5 para versão 2.0 - Documentos

//1:Importando bibliotecas: -->
require_once('sprintsql.php');

//2:Criando links com as bases de dados: -->
$v1 = mysql_connect('localhost', 'root', 'avantguard');
      mysql_select_db('cap', $v1 );
      
//3:Capturando lista de lojas na base antiga: -->
$rs = mysql_query( "SELECT * FROM agenda WHERE situacao = 'n' ORDER BY codagend ASC", $v1 );


//4:Primeiro alerta para o usuário: -->
echo 'Lendo o banco de dados e tranferindo as informações... <br>';

$v2 = mysql_connect('localhost', 'root', 'avantguard');
      mysql_select_db('intranet', $v2 );

//5:Iniciando o transporte das informações: -->
while( $d = mysql_fetch_assoc( $rs ) )
{
	$nota = 0;
	
	//Descubrindo se existia alguma observação...
	if( !empty( $d['obs'] ) )
	{
			    mysql_query('INSERT INTO anotacao VALUES ( "","","'.$d['obs'].'")');
		$nota = mysql_insert_id( $v2 );
	}
		
	//Convertendo entre os tipos de documento...
	$tp   = array( 1 => 1, 2 => 3, 3 => 2, 5 => 1, 13 => 4, 17 => 5, 11 => 6, 12 => 7, 14 => 8, 22 => 9, 15 => 10, 9 => 11, 19 => 12, 16 => 13 );
	$tipo = $tp[$d['codtpdoc']];
	
	//Manipulando as datas:( hoje )
	$gravacao = date("d-m-Y", time());
	
	//Formatando as datas....
	$temp    = explode('-', $d['dtemissao'] );
	$emissao = $temp[2].'-'.$temp[1].'-'.$temp[0];
	
	$temp = explode('-', $d['dtvenc'] );
	$venc = $temp[2].'-'.$temp[1].'-'.$temp[0];
	
	$valor = $d['valor'];
		
	//Escrevendo a query...
	$novosDados = sprintsql( "INSERT INTO documento ( codTp, codNote, codUser, loja, fornecedor, numero, gravacao, emissao, vencimento, valor, contabil ) 
							  VALUES ( %d, %d, %d, %d, %d, %s, %t, %t, %t, $valor, %s )",
							$tipo, $nota , 1, $d['codloja'], $d['codforn'],
							$d['numero'], $gravacao, $emissao, $venc,  $d['fiscal'] );
							
	//gravando ...						
		   mysql_query( $novosDados, $v2 );
	$cod = mysql_insert_id( $v2 );
	
	//Preparamos por fim os dados do pagamento...
	$pagamento = sprintsql("INSERT INTO pagamento ( codDoc, vencimento, aPagar, descontos, acrescimos, abatimentos, comentario, situacao )
							VALUES ( %d, %t, $valor, %f, %f, %f, %s, %s )", 
							$cod, $venc, 0.0, 0.0, 0.0, '','n');

	//gravando pagamento...
	mysql_query( $pagamento, $v2 );
		
}

//6:Avise o termino -->
	echo 'Pronto, dados importados !';
	exit();

	
?>