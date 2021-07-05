console.log('chargement admin_etage_meca');

var defautImageUrl = "/images/del.gif";
var cheminImages = "/images/";
var Fonds = {};
Fonds.donnees = new Array();
Fonds.donnees[-1] = { id: -1 };
Fonds.isDefaut = function (id) { return id == -1; };
Fonds.getUrl = function (id) { return (Fonds.isDefaut(id)) ? defautImageUrl : cheminImages + 'f_' + Etage.style + '_' + id + '.png'; };
Fonds.getClass = function (id) { return 'v' + id; };
Fonds.getId = function (id) { return 'fond' + id; };
Fonds.type = "Fond";
Fonds.enlevable = false;
Fonds.getSousType = function (id) { return true; }

var Murs = {};
Murs.donnees = new Array();
Murs.isDefaut = function (id) { return id == 0; };
Murs.getUrl = function (id) { return ( Murs.isDefaut(id)) ? defautImageUrl : cheminImages + 't_' + Etage.style + '_mur_' + id + '.png';};
Murs.getClass = function (id) { return (Murs.isDefaut(id)) ? "" : 'mur_' + id;};
Murs.getId = function (id) { return 'mur' + id;};
Murs.type = "Mur";
Murs.enlevable = true;
Murs.getSousType = function (id) { return true; }

var Decors = {};
Decors.donnees = new Array();
Decors.isDefaut = function (id) { return id == 0; };
Decors.getUrl = function (id) { return (Decors.isDefaut(id)) ? defautImageUrl : cheminImages + 'dec_' + id + '.gif';};
Decors.getClass = function (id) { return (Decors.isDefaut(id)) ? "" : 'decor' + id;};
Decors.getId = function (id) { return 'decor' + id;};
Decors.type = "Decor";
Decors.enlevable = true;
Decors.getSousType = function (id) { return true; }

var DecorsDessus = {};
DecorsDessus.donnees = Decors.donnees;
DecorsDessus.isDefaut = Decors.isDefaut;
DecorsDessus.getUrl = Decors.getUrl;
DecorsDessus.getClass = Decors.getClass;
DecorsDessus.getId = function (id) { return 'decordessus' + id;};
DecorsDessus.type = "DecorDessus";
DecorsDessus.enlevable = true;
DecorsDessus.getSousType = function (id) { return true; }

var Pinceau = { type: "", action: "standard", element: 0, rayonX: 0, rayonY: 0, maxX: 4, maxY: 4 };

Pinceau.dessineListe = function (objet, parent) {
	var imagesParLigne = 10;
	var lignesVisibles = 8;
	var nombreDeLignes = objet.donnees.length / imagesParLigne;
	var largeurPX = (imagesParLigne * 30);
	var hauteurPX = (nombreDeLignes * 30);

	var viewPortHauteur = lignesVisibles * 30;
	var viewPortLargeur = largeurPX;
	if (nombreDeLignes > lignesVisibles)	// Barre de défilement
		viewPortLargeur += 20;

	var divViewPort = document.createElement("div");
	divViewPort.style.overflow = "auto";
	divViewPort.style.maxWidth = viewPortLargeur.toString() + "px";
	divViewPort.style.maxHeight = viewPortHauteur.toString() + "px";
	divViewPort.style.padding = "5px";
	if (nombreDeLignes > lignesVisibles) {	// Barre de défilement
		divViewPort.style.width = viewPortLargeur.toString() + "px";
		divViewPort.style.padding = "0px";
	}

	var divConteneur = document.createElement("div");
	divConteneur.style.width = largeurPX.toString() + "px";
	divConteneur.style.heigth = hauteurPX.toString() + "px";

	var shift = objet.donnees[-1] ? -1: 0 ;
	for (var i = shift; i < objet.donnees.length; i++) {
		var debutLigne = ((i-shift) % imagesParLigne == 0);
		var idObj = objet.donnees[i].id;

		var elementImage = document.createElement("img");
		elementImage.className = "pinceau";
		if (debutLigne) elementImage.style.clear = "left";
		elementImage.src = objet.getUrl(idObj);
		if (objet.isDefaut(idObj)) {
			elementImage.width = 28;
			elementImage.height = 28;
		}
		elementImage.alt = idObj;
		elementImage.title = idObj;
		elementImage.data = idObj;
		elementImage.onclick = function () {
			Pinceau.miseAJour(objet.type, this.data);
		};
		elementImage.onmouseover = function () {
			ManipCss.ajouteClasse(this, "pinceausurligne");
		};
		elementImage.onmouseout = function () {
			ManipCss.enleveClasse(this, "pinceausurligne");
		};
		divConteneur.appendChild(elementImage);
	}
	divViewPort.appendChild(divConteneur);
	parent.appendChild(divViewPort);
};
