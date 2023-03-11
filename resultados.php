<?php
session_start();

//Autenticando o usuário:
if( !isset( $_SESSION['usuario'] ) || $_SESSION['nivel'] < 1 )
	header('Location: Index.php');
	
	
//Teste simples, depois melhore com a verificaçãodo usuário
if( !isset( $_GET['LSforn']) || !isset( $_GET['LSloja']) || !isset( $_GET['TXTnum']))
	die('Ocorreu um erro imprevisto, por favor contate o seu administrador.');

//Importando as bibliotecas principais:
	require_once('Engine/driver.php');
	require_once('Engine/sprintsql.php');
	require_once('Engine/restaurar.php');

//Valores default para cada item enviado: ( Em caso de terem sido deixados em branco )

	$loja  = ( $_GET['LSloja'] )? $_GET['LSloja'] : '%';
	$forn  = ( $_GET['LSforn'] )? $_GET['LSforn'] : '%';
	$user  = ( $_GET['LSuser'] )? $_GET['LSuser'] : '%';
	$num   = ( $_GET['TXTnum'] )? $_GET['TXTnum'].'%' : '%';
	$plano = ( $_GET['LSplano'] )? $_GET['LSplano'] : '%';
	
	//Construindo pares de Emissao - Vencimento e Agendamento, de acordo com as informações passadas pelo usuário
	list( $emis1, $emis2 ) = testarDatas( $_GET['TXTem1'], $_GET['TXTem2'] );
	list( $venc1, $venc2 ) = testarDatas( $_GET['TXTve1'], $_GET['TXTve2'] );
	list( $agen1, $agen2 ) = testarDatas( $_GET['TXTag1'], $_GET['TXTag2'] );
	
	$doc    = ( $_GET['LSdoc'] )? $_GET['LSdoc'] : '%';
	$fisc   = ( isset( $_GET['RDfiscal']) )? $_GET['RDfiscal'] : '%';
	$stat   = ( isset( $_GET['RDpago']) )?   $_GET['RDpago']   : '%';
	
	//Listando as strings que serão utilizadas para construir o sistema de ordenação dos resultados:
	$campos = array( 1 =>' documento.vencimento ASC,',
					 3 =>' documento.fornecedor ASC,',
					 2 =>' documento.emissao ASC,',
					 4 =>' documento.loja ASC,'
					);

	//Ordenação começando...
	$ordenarPor = 'ORDER BY';
	
	//Usuário definiu algum critério ?
	if( isset( $_GET['LSordem'] ))
	{
		
		$ordem  = $_GET['LSordem'];	
		
		foreach ( $ordem AS $value )
		{
			$ordenarPor .= $campos[$value];
		}
		
		//Eliminando a última vírgula depois da iteração pelo array
		$ordenarPor = substr( $ordenarPor, 0, strlen( $ordenarPor ) - 1 );		
	}
	else
		//Ordenação padrão:
		$ordenarPor .= ' documento.codDoc DESC';
	
	
	//Mega query que pesquisa segundo os parametros fornecidos:
	$result = sprintsql("SELECT tpdoc.tipo, colaborador.fantasia AS forn, loja.fantasia AS loja, documento.numero, documento.emissao, documento.valor, documento.gravacao,
						 documento.contabil, documento.codDoc, pagamento.codPag, pagamento.vencimento, pagamento.aPagar, pagamento.descontos,
						 pagamento.acrescimos, pagamento.abatimentos, pagamento.situacao, usuario.login
						 FROM documento, tpdoc, colaborador, pagamento, usuario, loja
						 WHERE tpdoc.codtp = documento.codtp AND documento.fornecedor = colaborador.codcolab AND documento.codDoc = pagamento.codDoc AND documento.codUser = usuario.codUser
						 AND documento.loja = loja.codLoja AND documento.loja LIKE %s AND documento.fornecedor LIKE %s AND documento.codUser LIKE %s AND documento.numero LIKE %s AND documento.gravacao BETWEEN %t AND %t AND documento.emissao BETWEEN %t AND %t 
						 AND documento.vencimento BETWEEN %t AND %t AND documento.codTp LIKE %s AND documento.contabil LIKE %s AND pagamento.codPlano LIKE %s AND pagamento.situacao LIKE %s $ordenarPor", 
						 $loja, $forn, $user, $num, $agen1, $agen2, $emis1, $emis2, $venc1, $venc2, $doc, $fisc, $plano, $stat, $ordenarPor
						 );
						 
	$rs    = dbQuery( $result );
	$total = (int)howMany( $rs );
		
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Distrital Intranet - Resultados</title>
<link href="Misc/Estilos1.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript" src="Misc/common.js"></script>
</head>
<body onLoad="self.focus(); Status();">
<table width="764" border="0" cellspacing="0" class="topoGlobal">
  <tr class="topoSup">
    <td width="159" height="42" align="center"><img src="imagens/IntranetLogo.gif" width="141" height="13" border="0"></td>
    <td width="413" align="center">&nbsp;</td>
    <td width="186" align="center">Resultados</td>
  </tr>
  <tr class="topoBase">
    <td height="33" colspan="3">      <?php if( $total ) { ?><a href="Relatorios/relatorio.php?<?php echo $_SERVER['QUERY_STRING']; ?>" target="_blank" title="Relatório simples" onMouseOver="return Status();" onMouseOut="return Status();"><img src="Imagens/printer.gif" width="22" height="22" border="0" align="absmiddle" class="icones"></a><a href="Relatorios/relatorio_subtotalizado.php?<?php echo $_SERVER['QUERY_STRING']; ?>" target="_blank" title="Relat&oacute;rio subtotalizado por Fornecedor" onMouseOver="return Status();" onMouseOut="return Status();"><img src="Imagens/printer_subtotal.gif" width="22" height="22" border="0" align="absmiddle" class="icones"></a><a href="exportacao.php?<?php echo $_SERVER['QUERY_STRING']; ?>" title="Exportar dados" onMouseOver="return Status();" onMouseOut="return Status();"><img src="Imagens/expIco.gif" width="18" height="19" border="0" align="absmiddle" class="icones"></a><a href="Relatorios/relatorioPdf.php?<?php echo $_SERVER['QUERY_STRING']; ?>" target="" title="Gerar PDF" onMouseOver="return Status();" onMouseOut="return Status();"><img src="Imagens/pdfIco.gif" width="22" height="22" border="0" align="absmiddle" class="icones"></a><?php } ?></td>
  </tr>
</table>
<table width="764" border="0" cellspacing="0">
  <tr>
    <td width="361" height="43" valign="middle" class="line"><span class="tituloGrande3">Resultados da Pesquisa  : </span></td>
    <td width="399" valign="middle" class="line"><?php echo $total; ?> item(s) combina(m) com as especifica&ccedil;&otilde;es </td>
  </tr>
  <tr align="center">
    <td height="82" colspan="2" align="center" valign="top"><table width="761" border="0" cellspacing="0">
        <tr class="cabecalho">
          <td width="66"> Loja </td>
          <td width="99">Fornecedor</td>
          <td width="77"> N&uacute;mero </td>
          <td width="74">Emiss&atilde;o</td>
          <td width="79">Vencimento</td>
          <td width="61">Vl. Bruto </td>
          <td width="50">Desc.</td>
          <td width="49">Acresc.</td>
          <td width="49">Dedu&ccedil;.</td>
          <td width="65"> a Pagar </td>
          <td width="70">&nbsp;</td>
        </tr>
        <?php
		
		$totalLiq = 0.0;
        
		while( $dados = rsFetch( $rs ) )
		{
			$cod     = $dados['codDoc'];
			$forn    = &restaurar( $dados['forn']);
			$loja    = &restaurar( $dados['loja']);
			$num     = &restaurar( $dados['numero']);
			$emissao = &restaurar( $dados['emissao'], 'data' );
			$venc    = &restaurar( $dados['vencimento'], 'data');
			$valorB  = &restaurar( $dados['valor'], 'valor');
			$desc    = &restaurar( $dados['descontos'], 'valor' );;
			$acres   = &restaurar( $dados['acrescimos'], 'valor');
			$abat    = &restaurar( $dados['abatimentos'], 'valor' );
			$valorL  = &restaurar((( $dados['valor'] + $dados['acrescimos'] ) - ( $dados['descontos'] + $dados['abatimentos'] )), 'valor');
			$msg     = $dados['tipo'].' agendado(a) por '.ucfirst( $dados['login']).' em '.restaurar( $dados['gravacao'], 'data' );
			$stat    = &restaurar( $dados['situacao']);
			$totalLiq += ( $dados['valor'] + $dados['acrescimos'] ) - ( $dados['descontos'] + $dados['abatimentos'] );
			
echo <<<DADOS
		
        <tr class="celula">
          <td align="left" class="celula2" style="padding-left:10px">$loja</td>
          <td align="left" class="celula2" style="padding-left:10px">$forn</td>
          <td align="left" class="celula2" style="padding-left:10px">$num</td>
          <td align="center" class="celula2">$emissao</td>
          <td align="center" class="celula2">$venc</td>
          <td align="right" class="celula2">$valorB</td>
          <td align="right" class="celula2">$desc</td>
          <td align="right" class="celula2">$acres</td>
          <td align="right" class="celula2">$abat</td>
          <td align="right" class="celula2">$valorL</td>
          <td class="celula2" align="right">
DADOS;
			  	if( $stat == 'n' )
		  		{
echo <<<DADOS
             <a href="javascript:;" onMouseOver="return Status()" onMouseOut="return Status()" title="Editar Informações" onClick="abrirJanela('Editar.php?codigo=$cod');"><img src="Imagens/editIco.gif" width="15" height="14" align="absmiddle" border="0"></a>
             <a href="central.php?codigo=$cod&act=exc" onMouseOver="return Status()" onMouseOut="return Status()" title="Excluir documento" onClick="return Confirm('documento');"><img src="Imagens/excluir.gif" width="14" height="16" align="absmiddle" border="0"></a>
             <a href="javascript:;" onMouseOver="return Status()" onMouseOut="return Status()" title="Notificar falta de boleto..." onClick="abrirJanelaModal('notificado.php?codigo=$cod');"><img src="Imagens/notifIco.gif" width="15" height="14" align="absmiddle" border="0"></a>
DADOS;
		  		}
		  
echo <<<DADOS
			<a href="javascript:;" onMouseOver="return Status()" onMouseOut="return Status()" title="$msg" onClick="abrirJanelaComum('documento.php?codigo=$cod');"><img src="Imagens/infoIco.gif" width="14" height="14" align="absmiddle" border="0"></a>
          </td>
        </tr>
DADOS;

		}
		?>
    </table></td>
  </tr>
  <tr align="center">
    <td height="27" class="line">&nbsp;</td>
    <td height="27" class="line" align="right" style="padding-right:74px">Total : <?php echo restaurar( $totalLiq, 'valor' ); ?></td>
  </tr>
  <tr align="left" valign="middle">
    <td height="95" colspan="2" class="result">&nbsp;</td>
  </tr>
  <tr align="center">
    <td height="24" colspan="2" valign="top" class="rodape">&nbsp;</td>
  </tr>
</table>
</body>
</html>
