<?php
session_start();

//Autenticando o usuário:
if( !isset( $_SESSION['usuario'] ) || $_SESSION['nivel'] < 2 )
	header('Location: Index.php');
	

//Teste simples, depois melhore com a verificaçãodo usuário
if( !isset( $_GET['LSconta']) || !isset( $_GET['LSforma']) || !isset( $_GET['TXTnum']) || !isset( $_GET['TXTdt1']) || !isset( $_GET['TXTdt2']))
	die('Ocorreu um erro imprevisto, por favor contate o seu administrador.');

//Importando as bibliotecas principais:
	require_once('Engine/driver.php');
	require_once('Engine/sprintsql.php');
	require_once('Engine/restaurar.php');

//Valores default para cada item enviado: ( Em caso de terem sido deixados em branco )

	$conta  = ( $_GET['LSconta'] )? $_GET['LSconta'] : '%';
	$forma  = ( $_GET['LSforma'] )? $_GET['LSforma'] : '%';
	$num    = ( !empty( $_GET['TXTnum']) )? $_GET['TXTnum'].'%' : '%';
	
	//Construindo pares de datas, de acordo com as informações passadas pelo usuário
	list( $dt1, $dt2 ) = testarDatas( $_GET['TXTdt1'], $_GET['TXTdt2'] );

	
	//Mega query que pesquisa segundo os parametros fornecidos:
	$result = sprintsql('SELECT movimentacao.codMov, banco.nome AS banco, formaPag.formaPag, conta.cc, movimentacao.numero, movimentacao.dtMov, nPagamentos, movimentacao.valor
						 FROM movimentacao, conta, formaPag, banco
						 WHERE movimentacao.codForma = formaPag.codForma AND movimentacao.codConta = conta.codConta AND banco.codBanco = conta.codBanco AND
						 movimentacao.codConta LIKE %s AND movimentacao.codForma LIKE %s AND movimentacao.numero LIKE %s AND movimentacao.dtMov BETWEEN %t AND %t
						 ORDER BY movimentacao.dtMov ASC, movimentacao.codConta ASC, movimentacao.codForma ASC',
						 $conta, $forma, $num, $dt1, $dt2
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
<body onLoad="Status();">
<table width="764" border="0" cellspacing="0" class="topoGlobal">
  <tr class="topoSup">
    <td width="158" height="42" align="center"><img src="imagens/IntranetLogo.gif" width="141" height="13" border="0"></td>
    <td width="378" align="center">&nbsp;</td>
    <td width="220" align="center">Consultar Pagamentos</td>
  </tr>
  <tr class="topoBase">
    <td height="33" colspan="3"><a href="principal.php" title="Tela principal" onMouseOver="return Status();" onMouseOut="return Status();"><img src="Imagens/homeIco.gif" width="22" height="20" border="0" align="absmiddle" class="icones"></a>|<a href="Relatorios/movimentacoes.php?<?php echo $_SERVER['QUERY_STRING']; ?>" target="_blank" title="Imprimir resultados" onMouseOver="return Status();" onMouseOut="return Status();"><img src="Imagens/printer.gif" width="22" height="22" border="0" align="absmiddle" class="icones"></a><a href="consultarPag.php" title="Nova consulta" onMouseOver="return Status();" onMouseOut="return Status();"><img src="Imagens/pagsIco.gif" width="21" height="22" border="0" align="absmiddle" class="icones"></a></td>
  </tr>
</table>
<table width="764" border="0" cellspacing="0">
  <tr>
    <td width="389" height="43" valign="middle" class="line"><span class="tituloGrande3">Resultados da Pesquisa  : </span></td>
    <td width="371" valign="middle" class="line"><?php echo $total; ?> item(s) combina(m) com as especifica&ccedil;&otilde;es </td>
  </tr>
  <tr align="center">
    <td height="82" colspan="2" align="center" valign="top"><table width="710" border="0" cellspacing="0">
        <tr class="cabecalho">
          <td width="79">Data</td>
          <td width="185"> Conta</td>
          <td width="145">Forma de Pagamento </td>
          <td width="113">N&uacute;mero</td>
          <td width="99">Valor</td>
          <td width="77">&nbsp;</td>
        </tr>
        <?php
		
			$total = 0.0;
        
		while( $dados = rsFetch( $rs ) )
		{
			$cod   = $dados['codMov'];
			$data  = restaurar( $dados['dtMov'], 'data');
			$conta = restaurar( $dados['banco']).' - '.restaurar( $dados['cc'] );
			$forma = restaurar( $dados['formaPag']);
			$num   = ( $dados['numero'] )? restaurar( $dados['numero'] ): 'Sem número';
			$valor = restaurar( $dados['valor'], 'valor');
			$total += $dados['valor'];
			
			
echo <<<DADOS
		
        <tr class="celula">
          <td align="left" class="celula2" style="padding-left:10px">$data</td>
          <td align="left" class="celula2" style="padding-left:10px">$conta</td>
          <td align="left" class="celula2" style="padding-left:10px">$forma</td>
          <td align="left" class="celula2" style="padding-left:20px">$num</td>
          <td align="right" class="celula2" style="padding-left:10px">$valor</td>
          <td align="center" class="celula2"><a href="Relatorios/documentos.php?codigo=$cod" target="_blank" title="Visualizar documentos..." onMouseOver="return Status();" onMouseOut="return Status();"><img src="Imagens/docPrint.gif" border="0" align="absmiddle"></a> <a href="javascript:;" title="Restrito : Excluir Movimentação" onMouseOver="return Status();" onMouseOut="return Status();" onClick="abrirJanela('ExcluirMov.php?codigo=$cod');"><img src="Imagens/delMov.gif" border="0" align="absmiddle" border="0"></a></td>
          
        </tr>
         
DADOS;
		}
		?>
    </table></td>
  </tr>
  <tr align="center">
    <td height="27" valign="top" class="line">&nbsp;</td>
    <td height="27" align="right" valign="top" class="line">Valor total : <?php echo restaurar( $total, 'valor' ); ?></td>
  </tr>
  <tr align="left" valign="middle">
    <td height="95" colspan="2" class="result"><p><span class="tituloGrande3">  <br>
      Fique atento :</span></p>
      <ul>
        <li>A lista de resultados lhe permite visualizar os documentos que comp&otilde;em cada pagamento, basta clicar no &iacute;cone em forma de pasta/impressora ao lado de cada item. </li>
        <li>Tamb&eacute;m &eacute; poss&iacute;vel excluir uma movimenta&ccedil;&atilde;o, revertendo a situa&ccedil;&atilde;o de todos os pagamentos para 'a pagar', contudo &eacute; uma opera&ccedil;&atilde;o que s&oacute; administradores  poder&atilde;o confirmar com sua senha especial.</li>
      </ul></td>
  </tr>
  <tr align="center">
    <td height="24" colspan="2" valign="top" class="rodape">&nbsp;</td>
  </tr>
</table>
</body>
</html>
