function affiche(x, y) {
	var isNS = (navigator.appName == "Netscape" && parseInt(navigator.appVersion) < 5);
	var isGecko = (navigator.appName == "Netscape" && parseInt(navigator.appVersion) >= 5);
	var cible_vue;
	var tab_vue;
	if (isGecko) {
   	var lacible = document.getElementById("lacible");
   	tab_vue = document.getElementById("tab_vue");
   	cible_vue = lacible.style;
	}
	else
	{
   	cible_vue= (isNS) ? document.objets.lacible : document.all.lacible.style;
   	tab_vue= (isNS) ? document.tab_vue : document.all.tab_vue;
   	var lacible= (isNS) ? document.objets.lacible : document.all.lacible;
	}
	if (!cible_vue) return;
	cible_vue.left= (isNS) ? tab_vue.rows[y].cells[x].left : tab_vue.rows[y].cells[x].offsetLeft;
	cible_vue.top= (isNS) ? tab_vue.rows[y].cells[x].top : tab_vue.rows[y].cells[x].offsetTop;
	cible_vue.visibility= (isNS)? "show" : "visible";
}
function cache() {
	var isNS = (navigator.appName == "Netscape" && parseInt(navigator.appVersion) < 5);
	var isGecko = (navigator.appName == "Netscape" && parseInt(navigator.appVersion) >= 5);
	var tab_vue;
	var cible_vue;
	if (isGecko) {
   	tab_vue = document.getElementById("tab_vue");
   	var lacible = document.getElementById("lacible");
   	cible_vue = lacible.style;
	}
	else
	{
   	 tab_vue= (isNS) ? document.tab_vue : document.all.tab_vue;
   	cible_vue= (isNS) ? document.objets.lacible : document.all.lacible.style;
   	var lacible= (isNS) ? document.objets.lacible : document.all.lacible;
	}
	if (!cible_vue) return;
	cible_vue.visibility="hidden";
}