
<script type="text/javascript">
// + --------------------------------------------------------------------------------------
// + XHRConnection
// + V1.3
// + Thanh Nguyen, http://www.sutekidane.net
// + 20.10.2005
// + http://creativecommons.org/licenses/by-nc-sa/2.0/fr/deed.fr
// + --------------------------------------------------------------------------------------
function XHRConnection() {
	
	// + ----------------------------------------------------------------------------------
	var conn = false;
	var debug = false;
	var datas = new String();
	var areaId = new String();
	// Objet XML
	var xmlObj;
	// Type de comportement au chargement du XML
	var xmlLoad;
	
	// + ----------------------------------------------------------------------------------
	try {
		conn = new XMLHttpRequest();		
	}
	catch (error) {
		if (debug) { alert('Erreur lors de la tentative de création de l\'objet \nnew XMLHttpRequest()\n\n' + error); }
		try {
			conn = new ActiveXObject("Microsoft.XMLHTTP");
		}
		catch (error) {
			if (debug) { alert('Erreur lors de la tentative de création de l\'objet \nnew ActiveXObject("Microsoft.XMLHTTP")\n\n' + error); }
			try {
				conn = new ActiveXObject("Msxml2.XMLHTTP");
			}
			catch (error) {
				if (debug) { alert('Erreur lors de la tentative de création de l\'objet \nnew ActiveXObject("Msxml2.XMLHTTP")\n\n' + error); }
				conn = false;
			}
		}
	}

	// + ----------------------------------------------------------------------------------
	// + setDebugOff
	// + Désactive l'affichage des exceptions
	// + ----------------------------------------------------------------------------------
	this.setDebugOff = function() {
		debug = false;
	};

	// + ----------------------------------------------------------------------------------
	// + setDebugOn
	// + Active l'affichage des exceptions
	// + ----------------------------------------------------------------------------------
	this.setDebugOn = function() {
		debug = true;
	};
	
	// + ----------------------------------------------------------------------------------
	// + resetData
	// + Permet de vider la pile des données
	// + ----------------------------------------------------------------------------------
	this.resetData = function() {
		datas = new String();
		datas = '';
	};
	
	// + ----------------------------------------------------------------------------------
	// + appendData
	// + Permet d'empiler des données afin de les envoyer
	// + ----------------------------------------------------------------------------------
	this.appendData = function(pfield, pvalue) {
		datas += (datas.length == 0) ? pfield+ "=" + escape(pvalue) : "&" + pfield + "=" + escape(pvalue);
	};
	
	// + ----------------------------------------------------------------------------------
	// + setRefreshArea
	// + Indique quel elment identifié par id est valoris lorsque l'objet XHR reoit une réponse
	// + ----------------------------------------------------------------------------------
	this.setRefreshArea = function(id) {
		areaId = id;
	};
	
	// + ----------------------------------------------------------------------------------
	// + createXMLObject
	// + Méthode permettant de créer un objet DOM, retourne la réfrence
	// + Inspiré de: http://www.quirksmode.org/dom/importxml.html
	// + ----------------------------------------------------------------------------------
	this.createXMLObject = function() {
		try {
			 	xmlDoc = document.implementation.createDocument("", "", null);
				xmlLoad = 'onload';
		}
		catch (error) {
			try {
				xmlDoc = new ActiveXObject("Microsoft.XMLDOM");
				xmlLoad = 'onreadystatechange ';
			}
			catch (error) {
				if (debug) { alert('Erreur lors de la tentative de création de l\'objet XML\n\n'); }
				return false;
			}
		}
		return xmlDoc;
	}
	
	// + ----------------------------------------------------------------------------------
	// + Permet de définir l'objet XML qui doit être valorisé lorsque l'objet XHR reoit une réponse
	// + ----------------------------------------------------------------------------------
	this.setXMLObject = function(obj) {
		if (obj == undefined) {
				if (debug) { alert('Paramètre manquant lors de l\'appel de la méthode setXMLObject'); }
				return false;
		}
		try {
			//xmlObj = this.createXMLObject();
			xmlObj = obj;
		}
		catch (error) {
				if (debug) { alert('Erreur lors de l\'affectation de l\'objet XML dans la méthode setXMLObject'); }
		}
	}
	
	// + ----------------------------------------------------------------------------------
	// + loadXML
	// + Charge un fichier XML
	// + Entrées
	// + 	xml			String		Le fichier XML à charger
	// + ----------------------------------------------------------------------------------
	this.loadXML = function(xml, callBack) {
		if (!conn) return false;
		// Chargement pour alimenter un objet DOM
		if (xmlObj && xml) {
			if (typeof callBack == "function") {
				if (xmlLoad == 'onload') {
					xmlObj.onload = function() {
						callBack(xmlObj);
					}
				}
				else {
					xmlObj.onreadystatechange = function() {
						if (xmlObj.readyState == 4) callBack(xmlObj)
					}
				}
			}
			xmlObj.load(xml);
			return;
		}		
	}

	// + ----------------------------------------------------------------------------------
	// + sendAndLoad
	// + Connexion à la page désirée avec envoie des données, puis mise en attente de la réponse
	// + Entrées
	// + 	Url			String		L'url de la page à laquelle l'objet doit se connecter
	// + 	httpMode		String		La méthode de communication HTTP : GET, HEAD ou POST
	// + 	callBack		Objet		Le nom de la fonction de callback
	// + ----------------------------------------------------------------------------------
	this.sendAndLoad = function(Url, httpMode, callBack) {
		httpMode = httpMode.toUpperCase();
		conn.onreadystatechange = function() {
			if (conn.readyState == 4 && conn.status == 200) {
				// Si une fonction de callBack a été définie
				if (typeof callBack == "function") {
					callBack(conn);
					return;
				}
				// Si une zone destinée à récupérer le résultat a été définie
				else if (areaId.length > 0){
					try {
						document.getElementById(areaId).innerHTML = conn.responseText;
					}
					catch(error) {
						if (debug) { alert('Echec, ' + areaId + ' n\'est pas un objet valide'); }
					}
					return;
				}
			}
		};
		switch(httpMode) {
			case "GET":
				try {
					Url = (datas.length > 0) ? Url + "?" + datas : Url;
					conn.open("GET", Url);
					conn.send(null);
				}
				catch(error) {
					if (debug) { alert('Echec lors de la transaction avec ' + Url + ' via la méthode GET'); }
					return false;
				}
			break;
			case "POST":
				try {
					conn.open("POST", Url); 
					conn.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
					conn.send(datas);
				}
				catch(error) {
					if (debug) { alert('Echec lors de la transaction avec ' + Url + ' via la mthode POST'); }
					return false;
				}
			break;
			default :
				return false;
			break;
		}
		return true;
	};
	return this;
}
// Déclaration de la fonction de Callback
// + ----------------------------------------------------------------------------------
// + afficherResultats
// + Affiche ou non le conteneur en fonction du résultat de la recherche
// + ----------------------------------------------------------------------------------
function mettrevaleur(textevaleur) {
	//window.parent.droite.document.sadmin.num_perso.value = textevaleur;
	if(document.getElementById('num_perso')){
	document.login2.num_perso.value = textevaleur;}
	if (document.getElementById('mod_perso_cod')){
	document.login2.mod_perso_cod.value = textevaleur;}
	document.getElementById('zoneResultats').style.visibility = 'hidden';
}
function mettrevaleur2(textevaleur) {
	//window.parent.droite.document.sadmin.num_perso.value = textevaleur;
	document.login2.foo.value = textevaleur;
	document.getElementById('zoneResultats').style.visibility = 'hidden';
}
function afficherResultats(obj) {
     // Construction des noeuds
     var tabResult = obj.responseXML.getElementsByTagName('resultat');
     document.getElementById('zoneResultats').innerHTML = '';
     if (tabResult.length > 0) {
          // On définit la hauteur de la liste en fonction du nombre de rsultats et de la hauteur de ligne
          var hauteur = tabResult.length * 22; 
          with(document.getElementById('zoneResultats').style) {
               visibility = 'visible';
               height = hauteur + 'px';
          };
          for (var i = 0; i < tabResult.length; i++) {
               resultat = tabResult.item(i); 
               titre = resultat.getAttribute('titre');
               var egt = document.createElement('li');
               var lnk = document.createElement('a');
               var texte = document.createTextNode(resultat.getAttribute('titre'));
               lnk.appendChild(texte);
               //lnk.setAttribute("onClick","document.formulaire.foo.value = titre");
               lnk.setAttribute('href', resultat.getAttribute('url'));
               lnk.setAttribute('title', resultat.getAttribute('titre'));
               egt.appendChild(lnk);
               document.getElementById('zoneResultats').appendChild(egt);
          }
     }
     else {
          document.getElementById('zoneResultats').style.visibility = 'hidden';
     }
}
// Déclaration de la fonction qui lance la recherche
function loadData() {
     // Création de l'objet
     var XHR = new XHRConnection();
     XHR.appendData("foo", document.getElementById('foo').value);
     // On soumet la requête
     // Signification des paramètres:               
     //      + On indique à l'objet qu'il faut appeler le fichier search.php
     //      + On utilise la méthode POST, adaptée l'envoi d'information
     //      + On indique quelle fonction appeler lorsque l'opération a été effectuée
     XHR.sendAndLoad("sadsearch.php", "POST", afficherResultats);
}
function loadData2() {
     // Création de l'objet
     var XHR = new XHRConnection();
     XHR.appendData("foo", document.getElementById('foo').value);
     // On soumet la requête
     // Signification des paramètres:               
     //      + On indique à l'objet qu'il faut appeler le fichier sadsearch2.php
     //      + On utilise la méthode POST, adaptée l'envoi d'information
     //      + On indique quelle fonction appeler lorsque l'opération a été effectuée
     XHR.sendAndLoad("sadsearch2.php", "POST", afficherResultats);
    }
function loadData3() {
     // Création de l'objet
     var XHR = new XHRConnection();
     XHR.appendData("foo", document.getElementById('foo').value);
     // On soumet la requête : remplissage des objets d'une liste de respawn cachettes
     // Signification des paramètres:               
     //      + On indique à l'objet qu'il faut appeler le fichier sadsearch3.php
     //      + On utilise la méthode POST, adaptée l'envoi d'information
     //      + On indique quelle fonction appeler lorsque l'opération a été effectuée
     XHR.sendAndLoad("sadsearch3.php", "POST", afficherResultats);
    }    
function supprimervaleur(textevaleur,textenom) {
	document.liste_modif.obj_sup.value = document.liste_modif.obj_sup.value + textevaleur + ",";
	document.liste_modif.obj_sup_nom.value = document.liste_modif.obj_sup_nom.value + textenom + "\n";
}
function explode(textevaleur) {
	chaine_explode = textevaleur.split("#"); 	
	document.liste_modif.obj_ajout.value = document.liste_modif.obj_ajout.value + chaine_explode[0] + ",";
	document.liste_modif.obj_ajout_nom.value = document.liste_modif.obj_ajout_nom.value + chaine_explode[1] + "\n";
}
function explode_cible(textevaleur, cible1, cible2) {
	var chaine_explode = textevaleur.split("#");
	if (cible1)
		cible1.value = cible1.value + chaine_explode[0] + ",";
	if (cible2)
		cible2.value = cible2.value + chaine_explode[1] + "\n";
}
function loadData4() {
     // Création de l'objet
     var XHR = new XHRConnection();
     XHR.appendData("foo", document.getElementById('foo').value);
     // On soumet la requête
     // Signification des paramètres:               
     //      + On indique à l'objet qu'il faut appeler le fichier sadsearch2.php
     //      + On utilise la méthode POST, adaptée l'envoi d'information
     //      + On indique quelle fonction appeler lorsque l'opération a été effectuée
     XHR.sendAndLoad("sadsearch4.php", "POST", afficherResultats);
    }
</script>
