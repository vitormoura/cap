<?php
session_start();

//Autenticando o usuário:
if( !isset( $_SESSION['usuario'] ) || $_SESSION['nivel'] < 2 )
	header('Location: Index.php');


//Teste simples, depois melhore com a verificaçãodo usuário
if( !isset( $_GET['TXTve1']) || !isset( $_GET['LSforn']) )
	die('Ocorreu um erro imprevisto, por favor contate o seu administrador.');

//Importando as bibliotecas principais:
	require_once('Engine/driver.php');
	require_once('Engine/sprintsql.php');
	require_once('Engine/restaurar.php');
	require_once('Engine/options.php');

//Valores default para cada item enviado: ( Em caso de terem sido deixados em branco )


	//Construindo pares de Emissao - Vencimento, de acordo com as informações passadas pelo usuário
	list( $venc1, $venc2 ) = testarDatas( $_GET['TXTve1'], $_GET['TXTve2'] );
		
	$forn   = ( $_GET['LSforn'] )? $_GET['LSforn'] : '%';
	$pagos  = ( isset( $_GET['CHKshow'] ) )? 'n' : '%';
		
	//Mega query que pesquisa segundo os parametros fornecidos:
	$result = sprintsql('SELECT tpdoc.tipo, colaborador.fantasia AS forn, loja.fantasia AS loja, documento.numero, documento.emissao, documento.valor, documento.gravacao,
						 documento.contabil, documento.codDoc, pagamento.codPag, pagamento.vencimento, pagamento.aPagar, pagamento.descontos,
						 pagamento.acrescimos, pagamento.abatimentos, pagamento.situacao, usuario.login
						 FROM documento, tpdoc, colaborador, pagamento, usuario, loja
						 WHERE tpdoc.codtp = documento.codtp AND documento.fornecedor = colaborador.codcolab AND documento.codDoc = pagamento.codDoc AND documento.codUser = usuario.codUser AND documento.loja = loja.codLoja
						 AND documento.fornecedor LIKE %s AND documento.vencimento BETWEEN %t AND %t AND pagamento.situacao LIKE %s ORDER BY documento.vencimento ASC, documento.fornecedor ASC, documento.loja ASC',
						 $forn, $venc1, $venc2, $pagos
						 );
						 
	$rs    = dbQuery( $result );
	$total = (int)howMany( $rs );
	
//Obtendo a lista de contas bancárias e formas de pagamento:
	$for    = dbQuery( 'SELECT conta.codConta, conta.cc, banco.nome  FROM conta, banco WHERE conta.codBanco = banco.codBanco ORDER BY conta.codConta ASC');
	$formas = 'SELECT codForma, formaPag FROM formapag ORDER BY codForma ASC';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Distrital Intranet - Resultados</title>
<link href="Misc/Estilos1.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript" src="Misc/common.js"></script>
<script language="javascript" type="text/javascript" src="Misc/DataCheck.js"></script>
<script language="javascript" type="text/javascript" src="Misc/fatura.js"></script>
</head>
<body onLoad="Init();">
<form action="central.php" method="post" onSubmit="return noBlank();">
<table width="764" border="0" cellspacing="0" class="topoGlobal">
  <tr class="topoSup">
    <td width="159" height="42" align="center"><img src="imagens/IntranetLogo.gif" width="141" height="13" border="0"></td>
    <td width="413" align="center">&nbsp;</td>
    <td width="186" align="center">Pagamentos</td>
  </tr>
  <tr class="topoBase">
    <td height="33" colspan="3">
    <!-- MENU DE NAVEGACAO -->
	<a href="javascript:;" onClick="abrirJanelinha();" title="Confirmar Pagamento" onMouseOver="return Status();" onMouseOut="return Status();"><img src="Imagens/calcIco2.gif" width="19" height="21" border="0" align="absmiddle" class="icones"></a>
	</td>
  </tr>
</table>
<table width="764" border="0" cellspacing="0">
  <tr>
    <td width="389" height="43" valign="middle" class="line"><span class="tituloGrande3">Pagamentos   : </span></td>
    <td width="371" valign="middle" class="line"><?php echo $total; ?> item(s) combina(m) com as especifica&ccedil;&otilde;es </td>
  </tr>
  <tr align="center">
    <td height="82" colspan="2" align="center" valign="top">

      <table width="761" border="0" cellspacing="0">
          <tr class="cabecalho">
            <td width="66"> Loja </td>
            <td width="99">Fornecedor</td>
            <td width="84"> N&uacute;mero </td>
            <td width="65">Emiss&atilde;o</td>
            <td width="73">Vencimento</td>
            <td width="63">Vl. Bruto </td>
            <td width="49">Desc.</td>
            <td width="51">Acresc.</td>
            <td width="49">Dedu&ccedil;.</td>
            <td width="63"> a Pagar </td>
            <td width="77">
	            <a href="javascript:;" onClick="selectAll()" onMouseOver="return Status();" onMouseOut="return Status();" title="Selecionar todos os pagamentos">Todos</a>
            </td>
          </tr>
          <?php
			
			$totalLiq = 0.0;	
        
		while( $dados = rsFetch( $rs ) )
		{
			
			//Recuperando os valores:
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
			$totalLiq += ( $dados['situacao'] == 'n' )? f( &$valorL ) : 0.0;
			
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
			//Caso o pagamento já esteja quitado, não mostramos os ícones de exclusão ou edição
			  	if( $stat == 'n' )
		  		{
echo <<<DADOS
             <input type="checkbox" name="select[]" id="select[]" value="$cod" onClick="gerarValor();"><input type="hidden" name="liq[$cod]" id="liq[$cod]" value="$valorL">
			 <a href="javascript:;" onMouseOver="return Status()" onMouseOut="return Status()" title="Editar Informações" onClick="abrirJanela('Editar.php?codigo=$cod');"><img src="Imagens/editIco.gif" width="15" height="14" align="absmiddle" border="0"></a>
             <a href="central.php?codigo=$cod&act=exc" onMouseOver="return Status()" onMouseOut="return Status()" title="Excluir documento" onClick="return Confirm('documento');"><img src="Imagens/excluir.gif" width="14" height="16" align="absmiddle" border="0"></a>
			 
			 
DADOS;
		  		}
		  
echo <<<DADOS
			<a href="javascript:;" onMouseOver="return Status()" onMouseOut="return Status()" title="$msg" onClick="abrirJanelaComum('documento.php?codigo=$cod');"><img src="Imagens/infoIco.gif" width="14" height="14" align="absmiddle" border="0"></a>
          </td>
        </tr>
DADOS;

		}
		?>
      </table>
    </td>
  </tr>
  <tr align="center">
    <td height="27" valign="top" class="line">&nbsp;</td>
    <td height="27" valign="top" class="line">&nbsp;</td>
  </tr>
  <tr align="left" valign="top">
  <!--INSTRUÇOES -->  
  <td height="95" colspan="2" class="result">
    <p><span class="tituloGrande3">Instru&ccedil;&otilde;es : </span></p>
      <ul>
        <li>Selecione  acima os documento que far&atilde;o parte desta fatura de pagamento, em seguida clique no &iacute;cone da calculadora para preencher os detalhes finais do pagamento, concluindo assim a operação.</li>
        <li>Os pagamentos da lista que n&atilde;o exibem mais &iacute;cones para edi&ccedil;&atilde;o ou exclus&atilde;o representam itens j&aacute; quitados. </li>
        <li>Para pesquisar outros pagamentos já efetuados, basta clicar no ícone "Consultar Pagamentos&quot;. <br>
        </li>
        </ul>
      </td>
   <!-- FIM DAS INSTRUÇOES -->
  </tr>
  <tr align="center">
    <td height="24" colspan="2" valign="top" class="rodape">&nbsp;</td>
  </tr>
</table>

<!-- JANELA COM OS DADOS PARA A QUITAÇÂO DOS PAGAMENTOS -->
<div id="Layer1" style="position:absolute; left:38px; top:86px; width:392px; height:214px; z-index:1;">
  <table width="393" border="0" align="right" cellspacing="0" id="box" style="margin:0">
    <tr>
      <td width="142" height="27" class="colunaEsquerda">&nbsp;</td>
      <td class="colunaDireita">
     </td>
    </tr>
    <tr>
      <td class="colunaEsquerda"><label for="LSconta">Conta :</label></td>
      <td width="243" class="colunaDireita">
	  <select name="LSconta" id="LSconta" onKeyDown="NextField('LSforma', event );">
		  <option value="" selected>Selecionar...</option>
		  <?php
				
		  $options = '';
				
		  while ( $linha = rsFetch( $for ) )
		  {
			echo '<option value="'.$linha['codConta'].'">';
			echo $linha['nome'].' - '.$linha['cc']."</option>\n";
		  }
						
		  ?>
	  </select>
	  </td>
    </tr>
    <tr>
      <td class="colunaEsquerda"><label for="LSforma">Forma de Pagamento :</label></td>
      <td class="colunaDireita">
	  <select name="LSforma" id="LSforma" onKeyDown="NextField('TXTdata', event );">
	    <option value="">Selecionar...</option>
		<?php
        //Escrevendo os fornecedores:
        options( $formas );
        ?>
      </select>
      </td>
    </tr>
    <tr>
      <td class="colunaEsquerda"><label for="TXTdata">Data :</label></td>
      <td class="colunaDireita"><input name="TXTdata" type="text" id="TXTdata" maxlength="10" onKeyPress="mascarar(this, '00-00-0000');" onFocus="this.select();" onBlur="DataCheck( this );" onKeyDown="NextField('TXTnum', event );"></td>
    </tr>
    <tr>
      <td class="colunaEsquerda"><label for="TXTnum">N&uacute;mero : </label></td>
      <td class="colunaDireita"><input name="TXTnum" type="text" id="TXTnum" maxlength="12" onFocus="this.select();" onKeyDown="NextField('RDbatch', event );"></td>
    </tr>
    <tr>
      <td height="28" class="colunaEsquerda">Total a Pagar : </td>
      <td class="colunaDireita" id="result" name="result">&nbsp;</td>
    </tr>
    <tr>
      <td height="26" class="colunaEsquerda">Processar : </td>
      <td class="colunaDireita"><input name="RDbatch" type="radio" value="block" onKeyDown="NextField('submit', event );" checked >
        <a href="javascript:;" title="Neste modo, será criada apenas uma movimentação, representando o total dos documentos selecionados.">Conjunto</a><br />
          <input name="RDbatch" type="radio" value="single" onKeyDown="NextField('submit', event );">
        <a href="javascript:;" title="Neste modo, serão criadas diversas movimentações, uma para cada documento selecionado.">Individual</a></td>
    </tr>
    <tr>
      <td height="26" class="colunaEsquerda">&nbsp;</td>
      <td align="center" class="colunaDireita">&nbsp;</td>
    </tr>
    <tr>
      <td height="26" class="colunaEsquerda">
          <input name="HDtotal" type="hidden" id="HDtotal">
      </td>
      <td align="center" class="colunaDireita">
		  <input type="submit" name="Submit" value="Confirmar">&nbsp;
		  <input type="button" name="Button" value=" X " onClick="fecharJanelinha();">
	  </td>
    </tr>
  </table>
</div>
<!-- FIM DA JANELA -->
</form>
</body>
</html>
