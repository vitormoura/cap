<?php
//Arquivo de importa��o dos dados da vers�o 1.5 para vers�o 2.0

//1:Importando bibliotecas:
require_once('sprintsql.php');

//2:Criando links com as bases de dados:
$v1 = mysql_connect('localhost', 'root', 'avantguard');
      mysql_select_db('cap', $v1 );
      
//3:Capturando lista de lojas na base antiga:
$rs = mysql_query( "SELECT * FROM lojas ORDER BY codloja ASC", $v1 );


//4:Primeiro alerta para o usu�rio:
echo 'Lendo o banco de dados e tranferindo as informa��es... <br>';

$v2 = mysql_connect('localhost', 'root', 'avantguard');
      mysql_select_db('intranet', $v1 );

//5:Iniciando o transporte das informa��es:
while( $d = mysql_fetch_assoc( $rs ) )
{
	//preparando a query...
	$novosDados = sprintsql( 'INSERT INTO loja ( codLoja, codGrupo, pessoa, nome, fantasia, inscFed, inscEst, endereco, cidade, estado, cep ) 
							  VALUES ( %d, %d, %s, %s, %s, %s, %s, %s, %s, %s, %s )',
							$d['codloja'], 0 , 'j', $d['razsocial'], $d['fantasia'],
							$d['cnpj'], $d['inscest'], $d['endereco'], $d['cidade'], $d['estado'], $d['cep'] );
							
	//gravando ...						
	mysql_query( $novosDados, $v2 );
	
	
}

//6:Avise o termino
echo 'Pronto, dados importados !';
exit();

?>