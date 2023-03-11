// JavaScript Document

function abrirJanelinha()
{
	//Abre a janela de confirmação dos dados do pagamento
	document.getElementById('box').style.visibility = 'visible';
	document.getElementById('LSconta').focus();
	gerarValor();
}

function fecharJanelinha()
{
	document.getElementById('box').style.visibility = 'hidden';
}

function Init()
{
	self.focus();
	return Status();
}

function gerarValor()
{
	var chks = document.forms[0].elements;
	var x;
	var total = 0.0;
	var temp  = document.getElementById('result'); //Este é o campo que abriga e exibe o resultado para o usuario
	var campo = document.getElementById('HDtotal'); //Este é um campo secreto, que será enviado ao servidor para processamento
	
	//Itera sobre cada item de checkbox da lista de pagamentos, capturando seu valor liquido caso estja selesionado
	for(x = 0; x < chks.length; x++ )
	{
		if( chks[x].type == 'checkbox' && chks[x].checked )
		{
			total += parseFloat( chks[x+1].value );
		}
	}
	
	//Objeto número, necessário para formatação em duas casas decimais
	total = new Number( total );
	
	//Escrevemos os novos valores e atualizamos o campo secreto
	temp.firstChild.nodeValue = total.toFixed(2);
	campo.value = total;
		
}

function noBlank()
{
	var campos = new Array( document.getElementById("LSconta"),
							document.getElementById("LSforma"),
							document.getElementById("TXTdata"),
							document.getElementById("TXTnum"),
							document.getElementById("HDtotal"));
	
	//Iniciando teste de validação...
	if( campos[0].value == '' || campos[1].value == '' || campos[2].value == '' || campos[4].value == '' )
	{
		window.alert('Informações importantes acerca deste pagamento não foram preenchidas');
		return false;
	}
	else if( parseFloat( campos[4].value ) <= 0.0 )
	{
		window.alert('Selecione pelo menos um documento da listagem para popular este pagamento');
		return false;
	}
	
							
}

function selectAll()
{
	var n = document.forms[0].length;
	var c = document.forms[0].elements;
	var x;
	
	for( x = 0; x < n; x++ )
	{
		if( c[x].type == 'checkbox' )
			c[x].checked = true;
	}
	
	gerarValor();

}
			