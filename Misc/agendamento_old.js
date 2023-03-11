function noSubmition ( form ) { 
/*
Funcao     : O botao submit do formulario fica inativo ate o usuario preencher todos os campos. 
Autor      : Vitor ( Indrema )
Escrito em : 28/08/2004
*/

	var i;
	var x = 0;
	
	
	for(i = 0; i < form.elements.length; i++) { 	 //Inicio do loop que vai percorrer todo o formulario
		if ( form.elements[i].value != '' ) { 		 // Testa se o campo atual nao esta em branco
			x++; 									 // Soma 1 a variavel x, nosso flag de teste
			
			if ( form.elements[i].type == 'submit' ) {// Identifica se o elemento atual e o botao submit
				var button = form.elements[i] 		  // Se sim, grava o elemento na variavel button
			}
		}
	}
		//Depois de analizar cada elemento do form ele testa a quantidade de elementos que nao estao vazios
		//este valor precisa ser igual ao numero de elementos do form para que o botao possa ser acionado
		( form.elements.length == x ) ? button.disabled = false : button.disabled = true;
	
}

function noBlank( itens, msg )
{ 
	var x;
	var n  = itens.length;
	
	//Testamos se todos os campos foram preenchidos certinho...
	for(x = 0; x < n; x++)
	{
		if( itens[x].value == '' )
		{
			window.alert( msg );
			return false
		}
			
	}

	//Testamos se o campo n�mero foi preenchido s� com zeros...
	if( parseFloat( itens[2].value ) == 0 )
	{
		window.alert('O n�mero do documento informado � inv�lido');
		return false;
	}

	
	//Agora testaremos as datas...
	data1 = document.getElementById('TXTemis').value.split("-");
	data2 = document.getElementById('TXTvenc').value.split("-");
	
	var n1 = new Date(data1[2],data1[1],data1[0],0,0,0,0); //UNIXTIMESTAMP da emissao
	var n2 = new Date(data2[2],data2[1],data2[0],0,0,0,0); //UNIXTIMESTAMP do vencimento

	//Testando se a emiss�o n�o � maior que o vencimento...
	if ( n1 > n2 )
	{
		window.alert('A emiss�o do documento n�o pode ocorrer depois de seu vencimento');
		return false;
	}
	
	//Iniciando o teste dos valores digitados:
	var acres = document.getElementById('TXTacres').value.replace('.','').replace(',','.');
	var ded   = document.getElementById('TXTded').value.replace('.','').replace(',','.');
	var valor = document.getElementById('TXTval').value.replace('.','').replace(',','.');
	var desc  = document.getElementById('TXTdesc').value.replace('.','').replace(',','.');

	//Depois de limpar as mascaras testamos se existem valores, se sim os convertemos em float's para calculos
	desc  = ( desc ) ? parseFloat(desc)  : 0.0;
	ded   = ( ded )  ? parseFloat(ded)   : 0.0;
	acres = ( acres )? parseFloat(acres) : 0.0;

	//Os descontos e dedu��es n�o podem exceder o valor do documento
	if( valor < ( desc + ded ))
	{
		window.alert('Os descontos e dedu��es excedem o total bruto do documento, revise os valores digitados');
		return false;
	}
	
	//A interface dispara um alerta que as informa��es est�o sendo processadas...
	document.getElementById('statusMsg').style.visibility = 'visible';
	
	//tudo deu certo, as informa��es podem prosseguir...
	return true;

}

function Init()
{
/**
Esta fun��o tem por objetivo preparar o scripting das p�ginas de acordo com a op��o desejada:
**/
	MM_reloadPage(true);
	Status();
	
	//Ocultamos a mensagem que indica o processamento da requisi��o...
	document.getElementById('statusMsg').style.visibility = 'hidden';
			
		//Efetua o reload da pagina principal para exibir os novos dados: ( resolvi desligar )
		//opener.document.location.reload();
		
		//Define algumas teclas de atalho:
		document.getElementById('btnBox').accessKey    = 'a';
		document.getElementById('btnBoxObs').accessKey = 'o';
		
		//Posiciona o cursor no primeiro campo:
		document.forms[0].elements[0].focus();		
	
		//Lista de campos que n�o podem permanecer em branco:
		itens = new Array( document.getElementById('LSloja'),
						   document.getElementById('LSforn'),
						   document.getElementById('TXTnum'),
						   document.getElementById('TXTemis'),
						   document.getElementById('TXTvenc'),
						   document.getElementById('TXTval'),
						   document.getElementById('LSdoc'),
						   document.getElementById('RDfiscal')
						   );
		
		//Mensagem de erro, caso o usu�rio n�o preencha as informa��es
		msg   = 'Informa��es importantes acerca deste documento n�o foram preenchidas';
		
}



function abrirBox( qual )
{
	switch ( qual )
	{
		case 'num':
		document.getElementById('box').style.visibility = 'visible';
		document.getElementById('TXTacres').focus();
		break;
		
		case 'obs':
		document.getElementById('boxObs').style.visibility = 'visible';
		document.getElementById('TEXTobs').focus();
		break;
	}
}

function fecharBox( qual )
{
	switch( qual )
	{
		case 'num':
		document.getElementById('box').style.visibility = 'hidden';
		document.getElementById('LSdoc').focus();
		break;
		
		case 'obs':
		document.getElementById('boxObs').style.visibility = 'hidden';
		document.getElementById('LSdoc').focus();
		break;
	}
}
