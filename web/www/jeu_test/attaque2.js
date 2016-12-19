function surligne(cellule) {
	var isNS = (navigator.appName == "Netscape" && parseInt(navigator.appVersion) < 5);
	var isGecko = (navigator.appName == "Netscape" && parseInt(navigator.appVersion) >= 5);
	if (isGecko) 
	{
		document.getElementById('cell7987').className='navon';
	}
	else
	{
		if (isNS) 
		{
			document.getElementById('cell7987').className='navon';
		}
		else
		{
			cellule.className='navon';
		}
	}
}
function end_surligne(cellule) {
	var isNS = (navigator.appName == "Netscape" && parseInt(navigator.appVersion) < 5);
	var isGecko = (navigator.appName == "Netscape" && parseInt(navigator.appVersion) >= 5);
	if (isGecko) 
	{
		document.getElementById("cellule").style.border='0px';
	}
	else
	{
		if (isNS) 
		{
			document.getElementById("cellule").style.border='0px';
		}
		else
		{
			cellule.className='navoff';
		}
	}
}