<?php
session_start();

//Autenticando o usu�rio:
if( !isset( $_SESSION['usuario'] ) || $_SESSION['nivel'] < 1 )
	header('Location: Index.php');

//Este arquivo � uma central de processamento de tarefas corriqueiras... 

//Importando bibliotecas principais:
	require_once('Engine/driver.php');
	require_once('Engine/sprintsql.php');

//Testando se � uma exclus�o de documento....
if( isset( $_GET['act'] ) && $_GET['act'] == 'exc' && isset( $_GET['codigo'] ) && is_numeric( $_GET['codigo'] ) )
{
	
	//Descubro junto ao banco de dados se este documento pode mesmo ser excluido:
	$info  = dbQuery( sprintsql('SELECT documento.codNote, pagamento.situacao FROM documento, pagamento
								  WHERE documento.codDoc = pagamento.codDoc AND pagamento.situacao = "n" AND documento.codDoc = %d', $_GET['codigo'] ));
	
	//Testando se o documento j� nao foi pago...
	if( $teste = rsFetch( $info ) )
	{
		//Excluindo vest�gios:
		dbQuery( sprintsql( 'DELETE FROM documento WHERE codDoc = %d', $_GET['codigo'] ));
		dbQuery( sprintsql( 'DELETE FROM pagamento WHERE codDoc = %d', $_GET['codigo'] ));
		dbQuery( sprintsql( 'DELETE FROM lembrete WHERE tipo = "Notificacao" AND ref = %s', $_GET['codigo']));
		
		//Testando se o documento possui anota��es, elas precisam ser excluidas tamb�m:
		if( $teste['codNote'] )
		{
			dbQuery( sprintsql( 'DELETE FROM anotacao WHERE codNote = %d', $teste['codNote'] ));
		}
	}
		
	//Redirecionado o usu�rio para de onde ele veio...
	header('Location: '.$_SERVER["HTTP_REFERER"] );
	exit();
	
}
//Testa se esta tentando excluir uma notifica��o...
else if( !empty( $_GET['codigo'] ) && is_numeric( $_GET['codigo']) && isset( $_GET['act'] ) && $_GET['act'] == 'dnot')
{
	
	//Deletando o lembrete...
	dbQuery( sprintsql( 'DELETE FROM lembrete WHERE codLemb = %d', $_GET['codigo']));
	
	//Redirecionado o usu�rio para de onde ele veio...
	header('Location: '.$_SERVER["HTTP_REFERER"] );
	exit();
	
}
//Gravando uma movimenta��o...
else if( !empty( $_POST['LSconta']) && !empty( $_POST['LSforma']) && !empty( $_POST['TXTdata']) && isset( $_POST['select']) && $_POST['HDtotal'] > 0 && $_SESSION['nivel'] >= 2 )
{
	//Gravando todos os checkboxes selecionados:
	$itens = $_POST['select'];
	$valor = $_POST['HDtotal'];
	
	$insertSql  = sprintsql( "INSERT INTO movimentacao ( codForma, codConta, dtMov, numero, nPagamentos, valor, tipo ) VALUES ( %d, %d, %t, %s, %d, $valor, %s )",
							 $_POST['LSforma'], $_POST['LSconta'], $_POST['TXTdata'], $_POST['TXTnum'], count( $itens ), 'd');
	
	//Gravando a movimenta��o:
	$cod = dbQuery( $insertSql );

	$temp = "UPDATE pagamento SET pagamento.codMov = $cod, situacao = 'p' WHERE codDoc IN ( ";
	
	foreach ( $itens AS $value )
		$temp .= " $value,";
	
	//Eliminando a �ltima v�rgula depois da itera��o pelo array
	$temp = substr( $temp, 0, strlen( $temp ) - 1 ).' )';	
	
	//Atualizando os pagamentos no banco de dados:
	dbQuery( $temp );
	
	//Redirecionado o usu�rio para de onde ele veio...
	header('Location: '.$_SERVER["HTTP_REFERER"] );
	exit();
	
}
else
	die( 'Aconteceu um erro imprevisto, por favor contate o administrador');
	
?>