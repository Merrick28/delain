<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<head>
<title>ricardo-test</title>
	<link rel="stylesheet" type="text/css" href="{g_url}style.css" title="essai">
	<link rel="meta" href="{g_url}labels.rdf" type="application/rdf+xml" title="ICRA labels" />
	<link rel="shortcut icon" href="{g_url}drake_head_red.ico" type="image/gif" />

 	<script src="{g_url}scripts/prototype.js" type="text/javascript"></script>

	<script type="text/javascript">
	function execJS(node) {
var bSaf = (navigator.userAgent.indexOf('Safari') != -1);
var bOpera = (navigator.userAgent.indexOf('Opera') != -1);
var bMoz = (navigator.appName == 'Netscape');
  var st = node.getElementsByTagName('SCRIPT');
  var strExec;
  for(var i=0;i<st.length; i++) {     
    if (bSaf) {
      strExec = st[i].innerHTML;
    }
    else if (bOpera) {
      strExec = st[i].text;
    }
    else if (bMoz) {
      strExec = st[i].textContent;
    }
    else {
      strExec = st[i].text;
    }
    try {
      eval(strExec);
    } catch(e) {
      alert(e);
    }
  }
}
		function send(nomform,destination){
			var params = Form.serialize($(nomform));
			var xhr_object = null;
     		var position = 'droite';
      	if(window.XMLHttpRequest) xhr_object = new XMLHttpRequest();
      	else
      	if (window.ActiveXObject) xhr_object = new ActiveXObject("Microsoft.XMLHTTP");
  			// message d'attente ?
  			document.getElementById(position).innerHTML = 'Chargement...';
			new Ajax.Updater('droite',destination, {asynchronous:true, parameters:params});
			execJS(document.getElementById('droite'));
		}
	</script>
	
	<script type="text/javascript">
	ns4 = document.layers; 
	ie = document.all; 
	ns6 = document.getElementById && !document.all; 
	function changeStyles (id, mouse) { 
   if (ns4) { 
      alert ("Sorry, but NS4 does not allow font changes."); 
      return false; 
   } 
   else if (ie) { 
      obj = document.all[id]; 
      } 
   else if (ns6) { 
      obj = document.getElementById(id); 
       } 
   if (!obj) { 
      alert("unrecognized ID"); 
      return false; 
   } 
   obj.className = mouse; 
   return true; 
	}
	
	</script>


	<script type="text/javascript">
		function montre_vue()
		{
			changeStyles('contenant_droite','invisible');
			changeStyles('contenant_vue','visible');
			changeStyles('vue_hg','visible');
			return true; 
		}
		function cache_vue()
		{
			changeStyles('contenant_droite','visible');
			changeStyles('contenant_vue','invisible');
			changeStyles('vue_hg','invisible');
			return true; 
		}
	</script>
	
	<script type="text/javascript">
	function envoieRequete(url,id)
	{
     	var xhr_object = null;
     	var position = id;
	   if(window.XMLHttpRequest) xhr_object = new XMLHttpRequest();
      else
      if (window.ActiveXObject) xhr_object = new ActiveXObject("Microsoft.XMLHTTP");
  		// message d'attente ?
  		document.getElementById(position).innerHTML = 'Chargement...';
     	// On ouvre la requete vers la page d�sir�e
     	xhr_object.open("GET", url, true);
     	xhr_object.onreadystatechange = function(){
     		if ( xhr_object.readyState == 4 )
    		{
	      	// j'affiche dans la DIV sp�cifi�es le contenu retourn� par le fichier
        		document.getElementById(position).innerHTML = xhr_object.responseText;
     		}
   	}
   // dans le cas du get
  
   xhr_object.send(null);
    execJS(document.getElementById(id));
 	}
	</script>
  
<style type="text/css">
body
{
	font-family:verdana,helvetica,sans-serif;
	font-size:9pt;
	text-align:left;
	padding-right:0px;
	padding-left:0px;
	padding-top:0px;
	padding-bottom:0px;
}
 
#header
{
	height: 75px;
	background-color: #99CCCC;
}
  
#conteneur
{
	position: absolute;
	width: 98%;
	height:98%;
	background : url('../images/fond5.gif');
}
  
#contenant_droite
{
	margin-left: 175px;
	background-color: #99CCCC;
}
#contenant_vue
{
	margin-left: 175px;
	background-color: #99CCCC;
}
 
  
#contenant_gauche
{
	position: absolute;
	top:0px;
	left:0px;
	height:1px;
	width: 175px;
}
#vue_haut
{
	height:350px;
	width: 100%;
	height:100%;
	background-color: #88AABB;
}

#vue_hg
{
	background-color: #99AACC;
	width:350px;
	height:350px;
	float:left;
}
#vue_hd
{
	float:left;
	background : url('../images/fond5.gif');
	height:350px;
	margin-top: 0px;
}
#vue_bas
{
	position:relative;
	margin-top: 350px;
	background-color: #99CCCC;
}

.invisible
{
	display:none;
}
.visible
{
	display:block;
}
</style>

</head>
  
<body onLoad="cache_vue();envoieRequete('{menu_haut}','menu_haut');envoieRequete('{menu_bas}','menu_bas');envoieRequete('{page_droite}','droite');" background="{img_path}fond5.gif">

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-1534416-3', 'auto');
  ga('require', 'GTM-M2VTQQW');
  ga('send', 'pageview');

</script>
      <div id="conteneur">
      <div id="contenant_gauche">
      	<table width="100%" bgcolor="#EBE7E7" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td width="10" background="{img_path}coin_hg.gif"><img src="{img_path}del.gif" height="8" width="10"></td>
					<td background="{img_path}ligne_haut.gif"><img src="{img_path}del.gif" height="8" width="10"></td>
					<td width="10" background="{img_path}coin_hd.gif"><img src="{img_path}del.gif" height="8" width="10"></td>
				</tr>
				<tr>
					<td width="10" background="{img_path}ligne_gauche.gif">&nbsp;</td>
					<td>
						<p class="titre">{nom_perso}</p>
						<div id="menu_haut" style="text-align:center;"></div>
					
					</td>
					<td width="10" background="{img_path}ligne_droite.gif">&nbsp;</td>
				</tr>
				<tr>
					<td height="10" width="10" background="{img_path}coin_bg.gif"><img src="{img_path}del.gif" height="2"></td>
					<td height="10" background="{img_path}ligne_bas.gif"></td>
					<td height="10" width="10" background="{img_path}coin_bd.gif"></td>
				</tr>
			</table>
			<table width="100%" bgcolor="#EBE7E7" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td width="10" background="{img_path}coin_hg.gif"><img src="{img_path}del.gif" height="8" width="10"></td>
					<td background="{img_path}ligne_haut.gif"><img src="{img_path}del.gif" height="8" width="10"></td>
					<td width="10" background="{img_path}coin_hd.gif"><img src="{img_path}del.gif" height="8" width="10"></td>
				</tr>
				<tr>
					<td width="10" background="{img_path}ligne_gauche.gif">&nbsp;</td>
					<td>
						<div id="menu_bas"></div>
					</td>
					<td width="10" background="{img_path}ligne_droite.gif">&nbsp;</td>
				</tr>
				<tr>
					<td height="10" width="10" background="{img_path}coin_bg.gif"><img src="{img_path}del.gif" height="2"></td>
					<td height="10" background="{img_path}ligne_bas.gif"></td>
					<td height="10" width="10" background="{img_path}coin_bd.gif"></td>
				</tr>
			</table>
      </div>
      <div id="contenant_droite">
      	<table width="100%" bgcolor="#EBE7E7" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td width="10" background="{img_path}coin_hg.gif"><img src="{img_path}del.gif" height="8" width="10"></td>
				<td background="{img_path}ligne_haut.gif"><img src="{img_path}del.gif" height="8" width="10"></td>
				<td width="10" background="{img_path}coin_hd.gif"><img src="{img_path}del.gif" height="8" width="10"></td>
				</tr>
				<tr>
				<td width="10" background="{img_path}ligne_gauche.gif">&nbsp;</td>
				<td>
					<div id="droite">Chargement en cours....</div>
						
				</td>
				<td width="10" background="{img_path}ligne_droite.gif">&nbsp;</td>
			</tr>
			<tr>
				<td height="10" width="10" background="{img_path}coin_bg.gif"><img src="{img_path}del.gif" height="2"></td>
				<td height="10" background="{img_path}ligne_bas.gif"></td>
				<td height="10" width="10" background="{img_path}coin_bd.gif"></td>
			</tr>
		</table>
      </div>
      <div id="contenant_vue">
      <div id="vue_haut">
						<div id="vue_hg"></div><div id="vue_hd">automap</div>
						</div>
						<div id="vue_bas">Vue du bas...</div>
		</div>
      </div>
 </body>
 </html>