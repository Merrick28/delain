var defautImageUrl = "http://images.jdr-delain.net/del.gif";
var cheminImages = "http://images.jdr-delain.net/";
var Fonds = {};
Fonds.donnees = new Array();
Fonds.isDefaut = function (id) { return false; };
Fonds.getUrl = function (id) { return cheminImages + 'f_' + Etage.style + '_' + id + '.png';};
Fonds.getClass = function (id) { return 'v' + id;};
Fonds.getId = function (id) { return 'fond' + id;};
Fonds.type = "Fond";
Fonds.enlevable = false;
Fonds.getSousType = function (id) { return true; }

var Murs = {};
Murs.donnees = new Array();
Murs.isDefaut = function (id) { return id == 0; };
Murs.getUrl = function (id) { return cheminImages + 't_' + Etage.style + '_mur_' + id + '.png';};
Murs.getClass = function (id) { return (Murs.isDefaut(id)) ? "" : 'mur_' + id;};
Murs.getId = function (id) { return 'mur' + id;};
Murs.type = "Mur";
Murs.enlevable = true;
Murs.getSousType = function (id) { return true; }

var Decors = {};
Decors.donnees = new Array();
Decors.isDefaut = function (id) { return id == 0; };
Decors.getUrl = function (id) { return cheminImages + 'dec_' + id + '.gif';};
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

var Speciaux = {};
Speciaux.donnees = [
	{id: 'passageOK', valeur: true, nom: 'Passage autorisé', type: 'passage', url: '', css: 'pinceauOn'},
	{id: 'passageNOK', valeur: false, nom: 'Passage interdit', type: 'passage', url: '', css: 'pinceauOff'},
	{id: 'pvpOK', valeur: true, nom: 'PVP autorisé', type: 'pvp', url: '', css: 'pinceauOn'},
	{id: 'pvpNOK', valeur: false, nom: 'PVP interdit', type: 'pvp', url: '', css: 'pinceauOff'},
	{id: 'creusableOK', valeur: true, nom: 'Mur creusable', type: 'creusable', url: '', css: 'pinceauOn'},
	{id: 'creusableNOK', valeur: false, nom: 'Mur non creusable', type: 'creusable', url: '', css: 'pinceauOff'},
	{id: 'tangibleOK', valeur: true, nom: 'Mur tangible', type: 'tangible', url: '', css: 'pinceauOn'},
	{id: 'tangibleNOK', valeur: false, nom: 'Mur intangible', type: 'tangible', url: '', css: 'pinceauOff'},
	{id: 'areneOK', valeur: true, nom: 'Entrée arène', type: 'entree_arene', url: '', css: 'pinceauOn'},
	{id: 'areneNOK', valeur: false, nom: 'Entrée arène', type: 'entree_arene', url: '', css: 'pinceauOff'},
];
Speciaux.isDefaut = function (id) { return false; };
Speciaux.getUrl = function (id) { var idx = Speciaux.getIdxFromId(id); return (idx > -1) ? cheminImages + Speciaux.donnees[idx].url : defautImageUrl; };
Speciaux.getClass = function (id) { var idx = Speciaux.getIdxFromId(id); return (idx > -1) ? Speciaux.donnees[idx].css : ''; };
Speciaux.getId = function (id) { return 'speciaux' + id;};
Speciaux.getNom = function (id) { var idx = Speciaux.getIdxFromId(id); return (idx > -1) ? Speciaux.donnees[idx].nom : 'Erreur';};
Speciaux.getSousType = function (id) { var idx = Speciaux.getIdxFromId(id); return (idx > -1) ? Speciaux.donnees[idx].type : 'Erreur';};
Speciaux.getIdxFromId = function (id) {
	for (var i = 0; i < Speciaux.donnees.length; i++) {
		if (Speciaux.donnees[i].id == id) return i;
	}
	return -1;
};
Speciaux.type = "Speciaux";
Speciaux.enlevable = false;
Speciaux.getIdFromValeur = function (type, valeur) {
	for (var i = 0; i < Speciaux.donnees.length; i++) {
		if (Speciaux.donnees[i].type == type && Speciaux.donnees[i].valeur == valeur) return Speciaux.donnees[i].id;
	}
	return '';
};

var Pinceau = { type: "", action: "standard", element: 0, rayonX: 0, rayonY: 0, maxX: 4, maxY: 4 };

Pinceau.miseAJour = function(type, idObj) {
	Pinceau.type = type;
	Pinceau.element = idObj;

	var spanType = document.getElementById("typePinceau");
	var imgPinceau = document.getElementById("imgPinceau");
	switch (Pinceau.type) {
		case Fonds.type:
			spanType.innerHTML = "Fond";
			imgPinceau.src = Fonds.getUrl(Pinceau.element);
			Etage.ModeVisu.EnleveSpeciaux ();
		break;
		case Decors.type:
			spanType.innerHTML = "Décor standard";
			imgPinceau.src = Decors.getUrl(Pinceau.element);
			Etage.ModeVisu.EnleveSpeciaux ();
		break;
		case DecorsDessus.type:
			spanType.innerHTML = "Décor superposé";
			imgPinceau.src = DecorsDessus.getUrl(Pinceau.element);
			Etage.ModeVisu.EnleveSpeciaux ();
		break;
		case Murs.type:
			spanType.innerHTML = "Mur";
			imgPinceau.src = Murs.getUrl(Pinceau.element);
			Etage.ModeVisu.EnleveSpeciaux ();
		break;
		case Speciaux.type:
			spanType.innerHTML = Speciaux.getNom(Pinceau.element);
			imgPinceau.src = Speciaux.getUrl(Pinceau.element);
			Etage.ModeVisu.AfficheSpeciaux ();
		break;
	}
}

Pinceau.appliquer = function () {
	for (var i = 0; i < Etage.CasesSelectionnees.length; i++) {
		var idx = Etage.CasesSelectionnees[i];
		if (Pinceau.action == "annule")
			Pinceau.annuleCase(idx);
		else
			Pinceau.appliqueCase(idx);
	}
}

// Applique les modifications à une case
Pinceau.appliqueCase = function (idx) {
	var ancienneCase = Etage.Cases[idx];
	var nvlleCase;

	var idxModif = (ancienneCase.modifiee) ? Etage.TrouveCaseModifieeParId(ancienneCase.id) : -1;
	if (idxModif > -1) {	// Case déjà modifiée
		var caseModif = Etage.CasesModifiees[idxModif];
		nvlleCase = { idx: idx, id: ancienneCase.id, mur: caseModif.mur, decor: caseModif.decor, decor_dessus: caseModif.decor_dessus, fond: caseModif.fond, passage: caseModif.passage, pvp: caseModif.pvp, creusable: caseModif.creusable, tangible: caseModif.tangible, entree_arene: caseModif.entree_arene };
		Etage.CasesModifiees.splice(idxModif, 1);
	}
	else			// Case pas encore modifiée
		nvlleCase = { idx: idx, id: ancienneCase.id, mur: ancienneCase.mur, decor: ancienneCase.decor, decor_dessus: ancienneCase.decor_dessus, fond: ancienneCase.fond, passage: ancienneCase.passage, pvp: ancienneCase.pvp, creusable: ancienneCase.creusable, tangible: ancienneCase.tangible, entree_arene: ancienneCase.entree_arene };

	// récupération et mise à jour des données
	switch (Pinceau.type) {
		case Fonds.type:
			Etage.changeCase (Fonds, idx, Pinceau.element);
			nvlleCase.fond = Pinceau.element;
		break;
		case Decors.type:
			Etage.changeCase (Decors, idx, Pinceau.element);
			nvlleCase.decor = Pinceau.element;
		break;
		case DecorsDessus.type:
			Etage.changeCase (DecorsDessus, idx, Pinceau.element);
			nvlleCase.decor_dessus = Pinceau.element;
		break;
		case Murs.type:
			Etage.changeCase (Murs, idx, Pinceau.element);
			nvlleCase.mur = Pinceau.element;
		break;
		case Speciaux.type:
			Etage.changeCase (Speciaux, idx, Pinceau.element);
			var pinceauSpecial = Speciaux.donnees[Speciaux.getIdxFromId(Pinceau.element)];
			switch (pinceauSpecial.type) {
				case 'passage': nvlleCase.passage = pinceauSpecial.valeur; break;
				case 'pvp': nvlleCase.pvp = pinceauSpecial.valeur; break;
				case 'creusable': nvlleCase.creusable = pinceauSpecial.valeur; break;
				case 'tangible': nvlleCase.tangible = pinceauSpecial.valeur; break;
				case 'entree_arene': nvlleCase.entree_arene = pinceauSpecial.valeur; break;
			}
		break;
	}

	Etage.CasesModifiees.push(nvlleCase);
	ancienneCase.modifiee = true;
};

// Annule les modifications apportées à une case
Pinceau.annuleCase = function (idx) {
	var caseDebut = Etage.Cases[idx];

	// on regarde si des modifications ont été apportées à la case
	// si oui, on la réinitialise. Si non, rien à faire.
	var idxModif = (caseDebut.modifiee) ? Etage.TrouveCaseModifieeParId(caseDebut.id) : -1;
	if (idxModif > -1) {	// Case déjà modifiée
		var caseModif = Etage.CasesModifiees[idxModif];

		// Remise des éléments
		Etage.changeCase (Fonds, idx, caseDebut.fond);
		Etage.changeCase (Decors, idx, caseDebut.decor);
		Etage.changeCase (Murs, idx, caseDebut.mur);
		Etage.changeCase (DecorsDessus, idx, caseDebut.decor_dessus);
		Etage.changeCase (Speciaux, idx, (caseDebut.passage) ? 'passageOK' : 'passageNOK');
		Etage.changeCase (Speciaux, idx, (caseDebut.pvp) ? 'pvpOK' : 'pvpNOK');
		Etage.changeCase (Speciaux, idx, (caseDebut.creusable) ? 'creusableOK' : 'creusableNOK');
		Etage.changeCase (Speciaux, idx, (caseDebut.tangible) ? 'tangibleOK' : 'tangibleNOK');
		Etage.changeCase (Speciaux, idx, (caseDebut.entree_arene) ? 'areneOK' : 'areneNOK');

		// On supprime la case de la liste des modifs
		Etage.CasesModifiees.splice(idxModif, 1);
		caseDebut.modifiee = false;
	}
};

Pinceau.dessineListe = function (objet, parent) {
	var imagesParLigne = 5;
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

	for (var i = 0; i < objet.donnees.length; i++) {
		var debutLigne = (i % imagesParLigne == 0);
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

Pinceau.dessineRadar = function () {
	var divConteneur = document.getElementById("visuPinceau");
	for (var j = -Pinceau.maxY; j <= Pinceau.maxY; j++) {
		for (var i = -Pinceau.maxX; i <= Pinceau.maxX; i++) {
			var div = document.createElement("div");
			div.id = "radar-" + i + "-" + j;
			
			div.className = "radarVierge ";
			if (i == 0 && j == 0) div.className += "radarSelectionne ";
			if (i == -Pinceau.maxX) div.className += "radarDebutLigne radarGauche ";
			if (i == Pinceau.maxX) div.className += "radarDroite ";
			if (j == -Pinceau.maxY) div.className += "radarHaut ";
			if (j == Pinceau.maxY) div.className += "radarBas ";
			
			div.x = Math.abs(i);
			div.y = Math.abs(j);
			var txtI = (div.x == Pinceau.maxX) ? "Tout" : div.x;
			var txtJ = (div.y == Pinceau.maxY) ? "Tout" : div.y;
			div.title = "Rayon du pinceau : x = " + txtI + " | y = " + txtJ;
			
			if (i == 0 && j !== 0) div.innerHTML = (div.y == Pinceau.maxY) ? "&infin;" : div.y;
			else if (j == 0) div.innerHTML = (div.x == Pinceau.maxX) ? "&infin;" : div.x;
			
			div.onclick = function () {
				Pinceau.selectionne(this.x, this.y);
			};
			div.onmouseover = function () {
				Pinceau.survole(this.x, this.y);
			};
			divConteneur.appendChild(div);
		}
	}
};
	
Pinceau.selectionne = function (x, y) {
	for (var i = -Pinceau.maxX; i <= Pinceau.maxX; i++) {
		for (var j = -Pinceau.maxY; j <= Pinceau.maxY; j++) {
			var objet = document.getElementById("radar-" + i + "-" + j);
			if (Math.abs(i) <= x && Math.abs(j) <= y) {
				ManipCss.enleveClasse (objet, "radarSurvole");
				ManipCss.ajouteClasse (objet, "radarSelectionne");
			} else {
				ManipCss.enleveClasse (objet, "radarSurvole");
				ManipCss.enleveClasse (objet, "radarSelectionne");
			}
		}
	}
	Pinceau.rayonX = (x == Pinceau.maxX) ? 1000 : x;
	Pinceau.rayonY = (y == Pinceau.maxY) ? 1000 : y;
};

Pinceau.survole = function (x, y) {
	for (var i = -Pinceau.maxX; i <= Pinceau.maxX; i++) {
		for (var j = -Pinceau.maxY; j <= Pinceau.maxY; j++) {
			var objet = document.getElementById("radar-" + i + "-" + j);
			if (Math.abs(i) <= x && Math.abs(j) <= y) {
				ManipCss.ajouteClasse (objet, "radarSurvole");
			} else {
				ManipCss.enleveClasse (objet, "radarSurvole");
			}
		}
	}
};
