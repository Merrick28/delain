function ajoute()
{
	if (document.nouveau_message.joueur.value != '')
	{
		document.nouveau_message.dest.value = document.nouveau_message.dest.value + document.nouveau_message.joueur.value + ";";
	}
	return true;
}
function changeDestinataire(addFrom)
{
	if(addFrom==0 && document.nouveau_message.joueur.options[document.nouveau_message.joueur.selectedIndex].value != ""){
		var selectedValue = document.nouveau_message.joueur.options[document.nouveau_message.joueur.selectedIndex].value;
		var strDest = document.nouveau_message.dest.value;
		var intPosString = strDest.indexOf(selectedValue,0);
		var intPosString2 = strDest.indexOf(";" + selectedValue,0);
		
		//Si cela a été trouvé au début (donc pas de ";" avant) : on supprime
		if(intPosString == 0)
			document.nouveau_message.dest.value = strDest.substr(selectedValue.length, strDest.length-intPosString);
		
		//Si AUCUN n'a été trouvé avec un ";" avant : on ajoute
		else if(intPosString2 < 0)
			document.nouveau_message.dest.value = strDest + selectedValue.substr(0, selectedValue.length);
		
		//Sinon on supprime....
		else
			document.nouveau_message.dest.value = 
			strDest.substr(0,intPosString) + strDest.substr(intPosString+selectedValue.length,strDest.length);
			
		document.nouveau_message.joueur.selectedIndex = 0;
	}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}
