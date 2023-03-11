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
	
	//Construindo pares de Emissao - Vencimento, de acordo com as informações passadas pelo usuário
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
						 pagamento.acrescimos, pagamento.abatimentos, pagamento.situacao
						 FROM documento, tpdoc, colaborador, pagamento, loja
						 WHERE tpdoc.codtp = documento.codtp AND documento.fornecedor = colaborador.codcolab AND documento.codDoc = pagamento.codDoc
						 AND documento.loja = loja.codLoja AND documento.loja LIKE %s AND documento.fornecedor LIKE %s AND documento.codUser LIKE %s AND documento.numero LIKE %s AND documento.gravacao BETWEEN %t AND %t AND documento.emissao BETWEEN %t AND %t 
						 AND documento.vencimento BETWEEN %t AND %t AND documento.codTp LIKE %s AND documento.contabil LIKE %s AND pagamento.codPlano LIKE %s AND pagamento.situacao LIKE %s $ordenarPor", 
						 $loja, $forn, $user, $num, $agen1, $agen2, $emis1, $emis2, $venc1, $venc2, $doc, $fisc, $plano, $stat, $ordenarPor
						 );
						 
	$rs    = dbQuery( $result );
	$total = (int)howMany( $rs );

//Abrindo o arquivo:
	$fp    = fopen('gerado.txt','w');
	
//Definindo os campos da primeira linha:	
	$info[0]   = 'Loja';
	$info[1]   = 'Fornecedor';
	$info[2]   = 'Documento';
	$info[3]   = 'Numero';
	$info[4]   = 'Emissão';
	$info[5]   = 'Vencimento';
	$info[6]   = 'Valor';
	$info[7]   = 'Descontos';
	$info[8]   = 'Acrescimos';
	$info[9]   = 'Abatimentos';
	$info[10]  = 'Valor Líquido';
	$info[11]  = 'Pago';
	$info[12]  = 'Fiscal';
	$info[13]  = "\n\r";			//Quebra de linha
	
//Unindo todos os campos em uma só linha...	( delimitando com um TAB )
	$string    = implode("\t", $info );
			
//Gravando a linha gerada no arquivo...
	fwrite( $fp, $string );
		

	//Iniciando iteracao pelos dados do resultado:
	while( $dados = rsFetch( $rs ) )
	{
					
		//Gravando cada informação no seu camp respectivo:
		$info[0]   = &restaurar( $dados['loja']);
		$info[1]   = &restaurar( $dados['forn']);
		$info[2]   = &restaurar( $dados['tipo']);
		$info[3]   = &restaurar( $dados['numero']);
		$info[4]   = &restaurar( $dados['emissao'], 'data' );
		$info[5]   = &restaurar( $dados['vencimento'], 'data');
		$info[6]   = &restaurar( $dados['valor'], 'valor');
		$info[7]   = &restaurar( $dados['descontos'], 'valor' );;
		$info[8]   = &restaurar( $dados['acrescimos'], 'valor');
		$info[9]   = &restaurar( $dados['abatimentos'], 'valor' );
		$info[10]  = &restaurar((( $dados['valor'] + $dados['acrescimos'] ) - ( $dados['descontos'] + $dados['abatimentos'] )), 'valor');
		$info[11]  = &restaurar( $dados['situacao']);
		$info[12]  = &restaurar( $dados['contabil']);
		$info[13]  = "\n";
		
		//Unindo todos os campos em uma só linha...		
		$string    = implode("\t", $info );
		
		//Gravando a linha gerada no arquivo...
		fwrite( $fp, $string );
	
	}
//Fechando o arquivo...		
	fclose( $fp );
	
//Enviando os header's indicando que se trata de um download de arquivo...
	header('Content-type: application/xls');
// O arquivo enviado será chamado resultado.txt
	header('Content-Disposition: attachment; filename="resultado.txt"');
// A fonte do arquivo enviado é gerado.txt
	readfile('gerado.txt');

//Excluindo o arquivo gerado pelas operações:
	unlink('gerado.txt');
	
	exit();
	
?>