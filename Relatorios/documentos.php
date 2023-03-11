<?php
session_start();

//Autenticando o usuário:
if( !isset( $_SESSION['usuario'] ) || $_SESSION['nivel'] < 2 )
	header('Location: Index.php');


//Teste simples, depois melhore com a verificaçãodo usuário
if( empty( $_GET['codigo']))
	die('Ocorreu um erro imprevisto, por favor contate o seu administrador.');

//Importando as bibliotecas principais:
	require_once('../Engine/driver.php');
	require_once('../Engine/sprintsql.php');
	require_once('../Engine/restaurar.php');

//Mega query que pesquisa segundo os parametros fornecidos:
	$result = sprintsql("SELECT tpdoc.tipo, colaborador.fantasia AS forn, loja.fantasia AS loja, documento.numero, documento.emissao, documento.valor,
						 documento.contabil, documento.codDoc, pagamento.codPag, pagamento.vencimento, pagamento.aPagar, pagamento.descontos,
						 pagamento.acrescimos, pagamento.abatimentos, pagamento.situacao, usuario.login
						 FROM documento, tpdoc, colaborador, pagamento, usuario, loja
						 WHERE tpdoc.codtp = documento.codtp AND documento.fornecedor = colaborador.codcolab AND documento.codDoc = pagamento.codDoc AND documento.codUser = usuario.codUser
						 AND documento.loja = loja.codLoja AND
						 pagamento.codMov = %d AND pagamento.situacao = 'p' ORDER BY documento.vencimento, documento.loja",
						 $_GET['codigo']
						 );

	$rs = dbQuery( $result );
	$total = (int)howMany( $rs );
	 
	if( !$total )
		die( 'Ocorreu um erro imprevisto, contate o administrador');
		
	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Distrital Intranet - Relat&oacute;rio</title>
<script language="javascript" type="text/javascript" src="../Misc/common.js"></script>
<link href="../Misc/reports.css" rel="stylesheet" type="text/css">
</head>
<body onLoad="Status(); self.print();">
<table width="921" border="0" cellspacing="0">
  <tr>
    <td width="155" height="30" align="center"><img src="../Imagens/IntraLogo.gif" width="138" height="9"></td>
    <td width="335" align="center">&nbsp;</td>
    <td width="322" align="center">&nbsp;</td>
  </tr>
</table>
<table width="921" border="0" cellspacing="0">
  <tr>
    <td width="553" height="43" valign="middle" class="destaque2">Resultados da Pesquisa : </td>
    <td width="364" valign="middle" class="destaque"><?php echo $total; ?> item(s) combina(m) com as especifica&ccedil;&otilde;es </td>
  </tr>
  <tr align="center">
    <td height="61" colspan="2" align="center" valign="top"><table width="906" border="0" cellspacing="0">
        <tr>
          <td width="71" class="cabecalho"> Loja </td>
          <td width="111" class="cabecalho">Fornecedor</td>
          <td width="116" class="cabecalho">Documento</td>
          <td width="95" class="cabecalho"> N&uacute;mero </td>
          <td width="66" class="cabecalho">Emiss&atilde;o</td>
          <td width="78" class="cabecalho">Vencimento</td>
          <td width="60" class="cabecalho">Bruto </td>
          <td width="45" class="cabecalho">Desc.</td>
          <td width="52" class="cabecalho">Acresc.</td>
          <td width="47" class="cabecalho">Dedu&ccedil;.</td>
          <td width="66" class="cabecalho"> L&iacute;quido</td>
          <td width="41" class="cabecalho">Fiscal</td>
          <td width="32" class="cabecalho">Pg</td>
        </tr>
        <tr>
          <td colspan="13">&nbsp;</td>
        </tr>
        <?php
		
			$totalBruto = 0.0;
			$totalAcres = 0.0;
			$totalDesc  = 0.0;
			$totalDed   = 0.0;
			        
		while( $dados = rsFetch( $rs ) )
		{
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
			$doc     = $dados['tipo'];
			$contabil = $dados['contabil'];
			$stat    = &restaurar( $dados['situacao']);
			
			$totalBruto += $dados['valor'];
			$totalAcres += $dados['acrescimos'];
			$totalDesc  += $dados['descontos'];
			$totalDed   += $dados['abatimentos'];
			
echo <<<DADOS
		
        <tr class="celula">
          <td align="left" class="celula2" style="padding-left:10px">$loja</td>
          <td align="left" class="celula2" style="padding-left:10px">$forn</td>
          <td align="left" class="celula2">$doc</td>
          <td align="left" class="celula2" style="padding-left:10px">$num</td>
          <td align="center" class="celula2">$emissao</td>
          <td align="center" class="celula2">$venc</td>
          <td align="right" class="celula2">$valorB</td>
          <td align="right" class="celula2">$desc</td>
          <td align="right" class="celula2">$acres</td>
          <td align="right" class="celula2">$abat</td>
          <td align="right" class="celula2">$valorL</td>
		  <td class="celula2" align="center">$contabil</td>
          <td class="celula2" align="center">$stat</td>
		</tr>
DADOS;
		}
?>
        <tr>
          <td colspan="13">&nbsp;</td>
        </tr>
        <tr>
          <td align="left" class="rodape" style="padding-left:10px">&nbsp;</td>
          <td align="left" class="rodape" style="padding-left:10px">&nbsp;</td>
          <td align="left" class="rodape" style="padding-left:10px">&nbsp;</td>
          <td align="left" class="rodape" style="padding-left:10px">&nbsp;</td>
          <td align="center" class="rodape">&nbsp;</td>
          <td align="center" class="rodape">&nbsp;</td>
          <td align="right" class="rodape"><?php echo restaurar( $totalBruto, 'valor' ); ?></td>
          <td align="right" class="rodape"><?php echo restaurar( $totalDesc, 'valor' ); ?></td>
          <td align="right" class="rodape"><?php echo restaurar( $totalAcres, 'valor' ); ?></td>
          <td align="right" class="rodape"><?php echo restaurar( $totalDed, 'valor' ); ?></td>
          <td align="right" class="rodape"><?php echo restaurar( ( $totalBruto + $totalAcres ) - ( $totalDesc + $totalDed ), 'valor' ); ?></td>
          <td align="right" class="rodape">&nbsp;</td>
          <td align="right" class="rodape">&nbsp;</td>
        </tr>
    </table></td>
  </tr>
  <tr align="center">
    <td height="27" colspan="2" valign="top">&nbsp;</td>
  </tr>
  <tr align="left" valign="middle">
    <td colspan="2" class="result"><p><span class="tituloGrande3"> <br>
    </span></p></td>
  </tr>
</table>
</body>
</html>
