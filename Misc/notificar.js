// JavaScript Document

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
	
	//Agora testaremos as datas...
	data1 = document.getElementById('TXTdata').value.split("-");
	data2   = new Date(); // HOJE
	
	var n2 = new Date( data2.getFullYear(), data2.getMonth()+1, data2.getDate(), 0,0,0,0); //UNIXTIMESTAMP de hoje
	var n1 = new Date(data1[2],data1[1],data1[0],0,0,0,0); //UNIXTIMESTAMP da data
	

	//Testando se a data � menor que hoje...
	if ( n1 <= n2 )
	{
		window.alert('Faz sentido ser notificado em uma data passada ?');;
		return false;
	}
	
	//tudo deu certo, as informa��es podem prosseguir...
	return true;
}

function Init()
{
/**
Esta fun��o tem por objetivo preparar o scripting das p�ginas de acordo com a op��o desejada:
**/	
	Status();
					
		//Posiciona o cursor no primeiro campo:
		document.forms[0].elements[0].focus();		
	
		//Lista de campos que n�o podem permanecer em branco:
		itens = new Array( document.getElementById('TXTdata'),
						   document.getElementById('LStipo'),
						   document.getElementById('TEXTmsg')
						   );
		
		//Mensagem de erro, caso o usu�rio n�o preencha as informa��es
		msg   = 'Todas as informa��es precisam ser preenchidas';
		
}

