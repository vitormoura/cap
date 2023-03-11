<?php
die('Arquivo bloqueado');

//Importando as bibliotecas principais:
	require_once('Engine/driver.php');
	require_once('Engine/sprintsql.php');

	//Lista dos números das contas telefonicas:
	//$numeros    = array('3466198', '3456867');
	
	$user       = 1;		         //Codigo do usuario
	$dtGravacao = time(); 	         //Capturando a hora atual
	$tipo       = 1;		         //Tipo de documento
	$nota       = 0;		         //Comentario
	$loja       = 22;		         //Codigo da loja
	$forn       = 51;		         // codigo do fornecedor
	$emiss      = '01-01-2006';		 //date("d-m-Y");
	$valor      = '0,01';	         //Valor simbólico
	$cont       = 'n';		         //Contabil ?
	$plano		= 20600;
	$mes 		= 1; //A partir do mês...
	$dia 		= 30;
	$num        = 0;

//Descomente o foreach seguinte so se for necessario pre-agendar muitos numeros de documentos	
//	foreach( $numeros as $num )
	//{
		//	$mes = 1; //A partir do mês...
			//$dia = 19;
				
		while( $mes <= 12 )
		{		
			$venc       = $dia.'-'.$mes.'-2006';
//			$num        = $mes.'/2005';
			
	
			//Agora inserimos os dados do documento propriamente dito, tudo processado pelas funções de formatação do sprintsql:
			$insertSql  = sprintsql('INSERT INTO documento ( codTp, codNote, codUser, gravacao, loja, fornecedor, numero, emissao, vencimento, valor, contabil )
					  VALUES ( %d, %d, %d, %d, %d, %d, %s, %t, %t, %f, %s )',
					   
						$tipo, $nota, $user, $dtGravacao, $loja, $forn, $num, $emiss, $venc, $valor, $cont );

			//Banco de dados executa...
			$cod        = dbQuery( $insertSql );
	
			//Por fim preenchemos a fatura com o restante dos dados:
			$insertSql  = sprintsql('INSERT INTO pagamento ( codDoc, vencimento, aPagar, descontos, acrescimos, abatimentos, comentario, situacao, codPlano )
							 VALUES ( %d, %t, %f, %f, %f, %f, %s, "n", %d )',
						$cod, $venc, $valor, '0,0', '0,0', '0,0','', $plano );
					
			//Banco de dados executa... e fim do processo.						
			dbQuery( $insertSql );
	
			$mes++;
		}
//	}
		

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Untitled Document</title>
</head>
<body>
<p>Prontinho ! Pré-Agendamentos gravados.</p>
</body>
</html>
