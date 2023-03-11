// JavaScript Document

function noBlank()
{
	var f1 = document.getElementById('TXTem1').value;
	var f2 = document.getElementById('TXTem2').value;
	var f3 = document.getElementById('TXTve1').value;
	var f4 = document.getElementById('TXTve2').value;
	
	var em1 = f1.split("-");
	var em2 = f2.split("-");
	var ve1 = f3.split("-");
	var ve2 = f4.split("-");
	
	if(  f1 != '' && f2 != '' && em2 < em1 )
	{
		window.alert('Periodo informado nos campos "Emissao" foram considerados inválidos');
		return false;
	}
	else if( f3 != '' && f4 != '' && ve2 < ve1 )
	{
		window.alert('Periodo informado nos campos "Vencimento" foram considerados inválidos');
		return false;
	}
	
}