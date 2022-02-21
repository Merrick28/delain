var sourisX, sourisY;
function cacheInfo(contenant)
{
	contenant.style.display = "none";
}

function montreInfo(contenant, contenu)
{
	contenant.innerHTML = "";
	contenant.innerHTML = contenu + '<hr /><a href="javascript:cacheInfo(document.getElementById(\'' + contenant.id + '\'));"><em>Fermer</em></a>';
	contenant.style.left = '' + sourisX + 'px';
	contenant.style.top = '' + sourisY + 'px';
	contenant.style.display = "block";
}

function changeInfo(contenant, contenu)
{
	cacheInfo(contenant);
	montreInfo(contenant, contenu);
}

function changeInfo_tableau(contenant, tableau, indice)
{
	cacheInfo(contenant);
	montreInfo(contenant, tableau[indice]);
}

function position(e)
{
	if (document.all)
	{
		sourisX = event.x + document.body.scrollLeft;
		sourisY = event.y + document.body.scrollTop;
	}
	else
	{
		sourisX = e.pageX;
		sourisY = e.pageY;
	}
}

document.onmousemove = position;