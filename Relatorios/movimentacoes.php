<?php
session_start();

//Autenticando o usuário:
if( !isset( $_SESSION['usuario'] ) || $_SESSION['nivel'] < 2 )
	header('Location: Index.php');


//Teste simples, depois melhore com a verificaçãodo usuário
if( !isset( $_GET['LSconta']) || !isset( $_GET['LSforma']) || !isset( $_GET['TXTnum']) || !isset( $_GET['TXTdt1']) || !isset( $_GET['TXTdt2']))
	die('Ocorreu um erro imprevisto, por favor contate o seu administrador.');

//Importando as bibliotecas principais:
	require_once('../Engine/driver.php');
	require_once('../Engine/sprintsql.php');
	require_once('../Engine/restaurar.php');

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
						 ORDER BY movimentacao.dtMov, movimentacao.codConta ASC, movimentacao.codForma ASC',
						 $conta, $forma, $num, $dt1, $dt2
						 );
						 
	$rs    = dbQuery( $result );
	$total = (int)howMany( $rs );
	
	if( !$total )
		die( 'Ocorreu um erro imprevisto, por favor contate o administrador');
		
	
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
<table width="710" border="0" cellspacing="0">
  <tr>
    <td width="155" height="30" align="center"><img src="../Imagens/IntraLogo.gif" width="138" height="9"></td>
    <td width="335" align="center">&nbsp;</td>
    <td width="322" align="center">&nbsp;</td>
  </tr>
</table>
<table width="710" border="0" cellspacing="0">
  <tr>
    <td width="402" height="43" valign="middle" class="destaque2">Resultados da Pesquisa  : </td>
    <td width="357" valign="middle" class="destaque"><?php echo $total; ?> item(s) combina(m) com as especifica&ccedil;&otilde;es </td>
  </tr>
  <tr align="left">
    <td height="61" colspan="2" valign="top"><table width="710" border="0" cellspacing="0">
      <tr class="cabecalho">
        <td width="79">Data</td>
        <td width="185"> Conta </td>
        <td width="145">Forma de Pagamento </td>
        <td width="113">N&uacute;mero</td>
        <td width="99">Valor</td>
      </tr>
	  <tr>
	  <td colspan="5">&nbsp;</td>
	  </tr>
      <?php
		
		$total = 0.0;
        
		while( $dados = rsFetch( $rs ) )
		{

			$data  = restaurar( $dados['dtMov'], 'data');
			$conta = restaurar( $dados['banco']).' - '.restaurar( $dados['cc'] );
			$forma = restaurar( $dados['formaPag']);
			$num   = ( $dados['numero'] )? restaurar( $dados['numero'] ): 'Sem n&uacute;mero';
			$valor = restaurar( $dados['valor'], 'valor');
			$total += $dados['valor'];
			
echo <<<DADOS
		
        <tr class="celula">
          <td align="left" class="celula2" style="padding-left:10px">$data</td>
          <td align="left" class="celula2" style="padding-left:10px">$conta</td>
          <td align="left" class="celula2" style="padding-left:10px">$forma</td>
          <td align="left" class="celula2" style="padding-left:20px">$num</td>
          <td align="right" class="celula2" style="padding-left:10px">$valor</td>
       </tr>
         
DADOS;
			  	
		}
		?>
		<tr>
	  <td colspan="5">&nbsp;</td>
	  </tr>
		<tr class="cabecalho">
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td align="right"><?php echo restaurar( $total, 'valor'); ?></td>
      </tr>
    </table></td>
  </tr>
  <tr align="center">
    <td height="27" colspan="2" valign="top">&nbsp;</td>
  </tr>
  <tr align="left" valign="middle">
    <td colspan="2" class="result"><p><span class="tituloGrande3">  <br>
      </span></p>
    </td>
  </tr>
</table>
</body>
</html>
