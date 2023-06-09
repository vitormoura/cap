<?php
session_start();

//Autenticando o usu�rio:
if( !isset( $_SESSION['usuario'] ) || $_SESSION['nivel'] < 1 )
	header('Location: Index.php');
	
	
//Teste simples, depois melhore com a verifica��odo usu�rio
if( !isset( $_GET['plano']) || !isset( $_GET['loja']) || !isset( $_GET['dt1']) || !isset( $_GET['dt2']) || !isset( $_GET['RDcont']))
	die('Ocorreu um erro imprevisto, por favor contate o seu administrador.');

//Importando as bibliotecas principais:
	require_once('../Engine/driver.php');
	require_once('../Engine/sprintsql.php');
	require_once('../Engine/restaurar.php');
	
	list( $dt1, $dt2 ) = testarDatas( $_GET['dt1'], $_GET['dt2'] );
	$loja  		= ( $_GET['loja'] )? $_GET['loja'] : '%';
	$plano  	= $_GET['plano'];
	//$contabil 	= ( $_GET['RDcont'] == 'all' )? '%': (( @$_GET['RDcont'] == 'true' )? 's' : 'n' );
	$contabil	= '%';
	
	//Primeiro eu consigo o total liquido de todos os documentos, assim posso calcular a participa��o de cada um dentro do total geral
	$resultTotal = sprintsql( 'SELECT sum( pagamento.aPagar) + sum( pagamento.acrescimos ) - sum( pagamento.descontos ) - sum( pagamento.abatimentos ) AS total
			   FROM pagamento, planodecontas, documento
			   WHERE pagamento.codPlano = planodecontas.codPlano AND documento.codDoc = pagamento.codDoc AND pagamento.codPlano = %d AND documento.loja LIKE %s AND pagamento.vencimento BETWEEN %t AND %t AND documento.contabil LIKE %s
			   ', $plano, $loja, $dt1, $dt2, $contabil ) ;
			   
	$rs     			= dbQuery( $resultTotal );
	list( $totalGeral ) = rsFetch( $rs, 'NUM' );
	
	$result = sprintsql( 'SELECT colaborador.fantasia AS plano, sum( pagamento.aPagar ) AS aPagar, sum( pagamento.descontos ) AS descontos, sum( pagamento.acrescimos ) AS acrescimos, sum( pagamento.abatimentos ) AS abatimentos
							   FROM pagamento, documento, planodecontas, colaborador
							   WHERE pagamento.codDoc = documento.codDoc AND pagamento.codPlano = planodecontas.codPlano AND documento.fornecedor = colaborador.codColab AND
					   		   pagamento.codPlano = %d AND documento.loja LIKE %s AND pagamento.vencimento BETWEEN %t AND %t AND documento.contabil LIKE %s
							   GROUP BY documento.fornecedor', $plano, $loja, $dt1, $dt2, $contabil);
							   

	$rs     = dbQuery( $result );
	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Distrital Intranet</title>
<link href="../Misc/reports.css" rel="stylesheet" type="text/css">
</head>
<body onLoad="self.focus();">
<table width="921" border="0" cellspacing="0">
  <tr>
    <td width="158" height="30" align="center"><img src="../Imagens/IntraLogo.gif" width="138" height="9"></td>
    <td width="175" align="center">&nbsp;</td>
    <td width="290" align="center">&nbsp;</td>
  </tr>
  <tr>
    <td height="30" colspan="3" class="destaque2">Resultados por fornecedor : </td>
  </tr>
</table>
<p>&nbsp;</p>
<table width="629" border="0" cellspacing="0"  style="margin:12px ">
<tr>
    <th width="128" class="cabecalho">Fornecedor</th>
	<th width="80" class="cabecalho">Valor Bruto</th>
    <th width="82" class="cabecalho">Descontos</th>
    <th width="83" class="cabecalho">Acr�scimos</th>
    <th width="88" class="cabecalho">Abatimentos</th>
    <th width="90" class="cabecalho">Valor L�quido</th>
	<th width="64" class="cabecalho">&nbsp;</th>
  </tr>
  <tr>
  <td colspan="6">&nbsp;</td>
  </tr>
<?php

	$tabela  = '';
	$totalBruto = 0.0;
	$totalAcres = 0.0;
	$totalDesc  = 0.0;
	$totalDed   = 0.0;
	//$dt1 = urlencode( $dt1 );
//	$dt2 = urlencode( $dt2 );



	while( $dados = rsFetch( $rs ) )
	{

		$participacao = number_format( (( ( $dados['aPagar'] + $dados['acrescimos'] ) - ( $dados['descontos'] + $dados['abatimentos'] ) )* 100 ) / $totalGeral, 2 );
		$plano   	  = &restaurar( $dados['plano'] );
		$valorB  	  = &restaurar( $dados['aPagar'], 'valor');
		$desc    	  = &restaurar( $dados['descontos'], 'valor' );;
		$acres   	  = &restaurar( $dados['acrescimos'], 'valor');
		$abat    	  = &restaurar( $dados['abatimentos'], 'valor' );
		$valorL  	  = &restaurar((( $dados['aPagar'] + $dados['acrescimos'] ) - ( $dados['descontos'] + $dados['abatimentos'] )), 'valor');

	echo <<<TABELA
	  <tr>
	  	<td>$plano</td>
		<td align="right">$valorB</td>
		<td align="right">$desc</td>
		<td align="right">$acres</td>
		<td align="right">$abat</td>
		<td align="right">$valorL</td>
		<td align="right">$participacao %</td>
	  </tr>
TABELA;

	$totalBruto += $dados['aPagar'];
	$totalAcres += $dados['acrescimos'];
	$totalDesc  += $dados['descontos'];
	$totalDed   += $dados['abatimentos'];
}
?>
<tr>
  <td colspan="6">&nbsp;</td>
</tr>
<tr>
    <td height="22" class="rodape">&nbsp;</td>
    <td class="rodape"><?php echo restaurar( $totalBruto, 'valor' ); ?></td>
	<td class="rodape"><?php echo restaurar( $totalDesc, 'valor' ); ?></td>
	<td class="rodape"><?php echo restaurar( $totalAcres, 'valor' ); ?></td>
    <td class="rodape"><?php echo restaurar( $totalDed, 'valor' ); ?></td>
	<td class="rodape"><?php echo restaurar( ( $totalBruto + $totalAcres ) - ( $totalDesc + $totalDed ), 'valor' ); ?></td>
	<td class="rodape"> 100.00 %</td>
 </tr>
</table>
<p>&nbsp;</p>
</body>
</html>
