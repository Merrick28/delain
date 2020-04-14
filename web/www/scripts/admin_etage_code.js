// L’objet étage qui contient ses caractéristiques générales
// ainsi que les listes de cases.
var Etage = { };
Etage.Cases = new Array();
Etage.CasesSelectionnees = new Array();
Etage.CasesModifiees = new Array();

Etage.ModeVisu = { };
Etage.ModeVisu.Joli = 'joli';
Etage.ModeVisu.Murs = 'murs';
Etage.ModeVisu.Courant = Etage.ModeVisu.Joli;
Etage.ModeVisu.AfficheSpecial = false;

Etage.ModeVisu.AfficheSpeciaux = function () {
	var typeSpecial = Speciaux.donnees[Speciaux.getIdxFromId(Pinceau.element)].type;
	if (Etage.ModeVisu.AfficheSpecial == typeSpecial) return true;
	var joli = (Etage.ModeVisu.Courant == Etage.ModeVisu.Joli);

	var cssTrue = Speciaux.getClass(Speciaux.getIdFromValeur (typeSpecial, true));
	var cssFalse = Speciaux.getClass(Speciaux.getIdFromValeur (typeSpecial, false));

	Etage.ModeVisu.EnleveSpeciaux ();
	switch (typeSpecial) {
		case 'passage':
			for (var i = 0; i < Etage.Cases.length; i++) {
				var donneesCourantes = Etage.TrouveCaseActuelle(i);
				var classe = (donneesCourantes.passage) ? cssTrue : cssFalse;
				ManipCss.ajouteClasse (Etage.Cases[i].divSpecial, classe);
				if (joli) ManipCss.ajouteClasse (Etage.Cases[i].divSpecial, 'pinceauOnOffJoli');
			}
		break;
		case 'pvp':
			for (var i = 0; i < Etage.Cases.length; i++) {
				var donneesCourantes = Etage.TrouveCaseActuelle(i);
				var classe = (donneesCourantes.pvp) ? cssTrue : cssFalse;
				ManipCss.ajouteClasse (Etage.Cases[i].divSpecial, classe);
				if (joli) ManipCss.ajouteClasse (Etage.Cases[i].divSpecial, 'pinceauOnOffJoli');
			}
		break;
		case 'creusable':
			for (var i = 0; i < Etage.Cases.length; i++) {
				var donneesCourantes = Etage.TrouveCaseActuelle(i);
				var classe = (donneesCourantes.creusable) ? cssTrue : cssFalse;
				ManipCss.ajouteClasse (Etage.Cases[i].divSpecial, classe);
				if (joli) ManipCss.ajouteClasse (Etage.Cases[i].divSpecial, 'pinceauOnOffJoli');
			}
		break;
		case 'tangible':
			for (var i = 0; i < Etage.Cases.length; i++) {
				var donneesCourantes = Etage.TrouveCaseActuelle(i);
				var classe = (donneesCourantes.tangible) ? cssTrue : cssFalse;
				ManipCss.ajouteClasse (Etage.Cases[i].divSpecial, classe);
				if (joli) ManipCss.ajouteClasse (Etage.Cases[i].divSpecial, 'pinceauOnOffJoli');
			}
		break;
		case 'entree_arene':
			for (var i = 0; i < Etage.Cases.length; i++) {
				var donneesCourantes = Etage.TrouveCaseActuelle(i);
				var classe = (donneesCourantes.entree_arene) ? cssTrue : cssFalse;
				ManipCss.ajouteClasse (Etage.Cases[i].divSpecial, classe);
				if (joli) ManipCss.ajouteClasse (Etage.Cases[i].divSpecial, 'pinceauOnOffJoli');
			}
		break;
	}
	Etage.ModeVisu.AfficheSpecial = typeSpecial;
};

Etage.ModeVisu.EnleveSpeciaux = function () {
	if (!Etage.ModeVisu.AfficheSpecial) return true;
	for (var i = 0; i < Etage.Cases.length; i++) {
		ManipCss.enleveClasse (Etage.Cases[i].divSpecial, 'pinceauOn');
		ManipCss.enleveClasse (Etage.Cases[i].divSpecial, 'pinceauOff');
		if (Etage.ModeVisu.Courant == Etage.ModeVisu.Joli)
			ManipCss.enleveClasse (Etage.Cases[i].divSpecial, 'pinceauOnOffJoli');
	}
	Etage.ModeVisu.AfficheSpecial = false;
};

Etage.ModeVisu.Change = function (nouveauMode) {
	//if (nouveauMode == Etage.ModeVisu.Courant)
	//	return true;

	switch (nouveauMode) {
		case Etage.ModeVisu.Murs:
			for (var i = 0; i < Etage.Cases.length; i++) {
				var donneesCourantes = Etage.TrouveCaseActuelle(i);
				if (donneesCourantes.mur == 0)
					ManipCss.ajouteClasse (Etage.Cases[i].divSpecial, 'pasMurSimple');
				else
					ManipCss.ajouteClasse (Etage.Cases[i].divSpecial, 'murSimple');
				if (Etage.ModeVisu.AfficheSpecial)
					ManipCss.enleveClasse (Etage.Cases[i].divSpecial, 'pinceauOnOffJoli');
			}
		break;
		case Etage.ModeVisu.Joli:
			for (var i = 0; i < Etage.Cases.length; i++) {
				var donneesCourantes = Etage.TrouveCaseActuelle(i);
				if (donneesCourantes.mur == 0)
					ManipCss.enleveClasse (Etage.Cases[i].divSpecial, 'pasMurSimple');
				else
					ManipCss.enleveClasse (Etage.Cases[i].divSpecial, 'murSimple');
				if (Etage.ModeVisu.AfficheSpecial)
					ManipCss.ajouteClasse (Etage.Cases[i].divSpecial, 'pinceauOnOffJoli');
			}
		break;
	}
	Etage.ModeVisu.Courant = nouveauMode;
};

// Calcule l’Id d’une case en fonction de ses coordonnées
// (Attention, ne fonctionne qu’en étage rectangulaire complet)
Etage.getCaseIdxByXY = function (x, y) {
	var tailleX = Etage.maxX - Etage.minX + 1;
	return (x - Etage.minX) + (Etage.maxY - y) * tailleX;
};

// Trouve si la case demandée a été modifiée, et renvoie son index dans le
// tableau des cases modifiées.
Etage.TrouveCaseModifieeParId = function (id) {
	for (var i = 0; i < Etage.CasesModifiees.length; i++) {
		if (Etage.CasesModifiees[i].id == id)
			return i;
	}
	return -1;
};

// Trouve la case avec ses données les plus récentes. Attention, pour les cases modifiées, cela exclut les propriétés "div".
Etage.TrouveCaseActuelle = function (idx) {
	var resultat = Etage.Cases[idx];
	var idxModif = (resultat.modifiee) ? Etage.TrouveCaseModifieeParId(resultat.id) : -1;
	if (idxModif > -1)
		resultat = Etage.CasesModifiees[idxModif];
	return resultat;
};

// Récupère toutes les cases voisines de la case ciblée
// en fonction du pinceau
Etage.getCasesVoisines = function (idx) {
	var resultat = new Array();
	var centre = Etage.Cases[idx];

	var xDebut = Math.max(centre.x - Pinceau.rayonX, Etage.minX);
	var xFin = Math.min(centre.x + Pinceau.rayonX, Etage.maxX);
	var yDebut = Math.min(centre.y + Pinceau.rayonY, Etage.maxY);
	var yFin = Math.max(centre.y - Pinceau.rayonY, Etage.minY);
	for (var ix = xDebut; ix <= xFin; ix++) {
		for (var iy = yDebut; iy >= yFin; iy--) {
			var caseIdx = Etage.getCaseIdxByXY(ix, iy);
			var pavageOK = (Pinceau.action != "pavage" || (Math.abs(centre.x + centre.y - ix - iy) % 2 == 0));
			var murOK = (Pinceau.action != "murs" || Etage.Cases[caseIdx].mur > 0);
			var solOK = (Pinceau.action != "sols" || Etage.Cases[caseIdx].mur == 0);
			if (pavageOK && murOK && solOK)
				resultat.push(caseIdx);
		}
	}
	return resultat;
};

// Dessine l’étage.
Etage.Dessine = function() {
	console.log('debut dessine etage');
	var lignesVisibles = 50;
	var colonnesVisibles = 50;

	var imagesParLigne = Etage.maxX - Etage.minX + 1;
	var nombreDeLignes = Etage.maxY - Etage.minY + 1;
	var largeurPX = (imagesParLigne * 28);
	var hauteurPX = (nombreDeLignes * 28);

	var viewPortHauteur = lignesVisibles * 28;
	var viewPortLargeur = colonnesVisibles * 28;
	if (nombreDeLignes > lignesVisibles)	// Barre de défilement
		viewPortLargeur += 20;

	var divViewPort = document.createElement("div");
	divViewPort.className = "bordiv";
	divViewPort.style.overflow = "auto";
	divViewPort.style.maxWidth = viewPortLargeur.toString() + "px";
	divViewPort.style.maxHeight = viewPortHauteur.toString() + "px";
	if (nombreDeLignes > lignesVisibles)	// Barre de défilement
		divViewPort.style.width = viewPortLargeur.toString() + "px";

	var divConteneur = document.createElement("div");
	divConteneur.style.width = largeurPX.toString() + "px";
	divConteneur.style.heigth = hauteurPX.toString() + "px";
	divConteneur.onmouseout = function () { Etage.deselectionne(); };

	for (var i = 0; i < Etage.Cases.length; i++) {
		var debutLigne = (i % imagesParLigne == 0);
		var idObj = Etage.Cases[i].id;

		Etage.Cases[i].divFond = Etage.getCoucheSuperposee (Fonds, Etage.Cases[i].id, Etage.Cases[i].fond);
		if (debutLigne) Etage.Cases[i].divFond.style.clear = "left";

		Etage.Cases[i].divDecor = Etage.getCoucheSuperposee (Decors, Etage.Cases[i].id, Etage.Cases[i].decor);
		Etage.Cases[i].divMur = Etage.getCoucheSuperposee (Murs, Etage.Cases[i].id, Etage.Cases[i].mur);
		Etage.Cases[i].divDecorDessus = Etage.getCoucheSuperposee (DecorsDessus, Etage.Cases[i].id, Etage.Cases[i].decor_dessus);
		Etage.Cases[i].divSpecial = Etage.getCoucheAction (i);

		divConteneur.appendChild(Etage.Cases[i].divFond);
		var divEnCours = Etage.Cases[i].divFond;
		if (Etage.Cases[i].divDecor) {
			divEnCours.appendChild(Etage.Cases[i].divDecor);
			divEnCours = Etage.Cases[i].divDecor;
		}
		if (Etage.Cases[i].divMur) {
			divEnCours.appendChild(Etage.Cases[i].divMur);
			divEnCours = Etage.Cases[i].divMur;
		}
		if (Etage.Cases[i].divDecorDessus) {
			divEnCours.appendChild(Etage.Cases[i].divDecorDessus);
			divEnCours = Etage.Cases[i].divDecorDessus;
		}
		divEnCours.appendChild(Etage.Cases[i].divSpecial);
	}
	divViewPort.appendChild(divConteneur);
	document.getElementById("vueEtage").appendChild(divViewPort);
	console.log("fin etage dessine");
};

// Insère une div quelque part entre DivFond et DivAction, qui sont toujours présentes, dans l’ordre Fond > Décor > Mur > DécorDessus > Action.
Etage.insereCouche = function (div, idx) {
	// Recherche de la div où insérer la couche
	var divParente = false;
	switch (div.type) {
		case DecorsDessus.type:
			divParente = (!divParente) ? Etage.Cases[idx].divMur : divParente;
		case Murs.type:
			divParente = (!divParente) ? Etage.Cases[idx].divDecor : divParente;
		case Decors.type:
			divParente = (!divParente) ? Etage.Cases[idx].divFond : divParente;
		break;
	}
	if (divParente) {
		div.appendChild(divParente.childNodes[0])
		divParente.appendChild(div);
	}
	else {
	}
};

Etage.getValeurFromType = function (type, idx, sousType) {
	var resultat = false;
	var laCase = Etage.TrouveCaseActuelle (idx);
	switch (type) {
		case Fonds.type: resultat = laCase.fond; break;
		case Murs.type: resultat = laCase.mur; break;
		case Decors.type: resultat = laCase.decor; break;
		case DecorsDessus.type: resultat = laCase.decor_dessus; break;
		case Speciaux.type: 
			switch (sousType) {
				case 'passage': resultat = Speciaux.getIdFromValeur (sousType, laCase.passage); break;
				case 'pvp': resultat = Speciaux.getIdFromValeur (sousType, laCase.pvp); break;
				case 'creusable': resultat = Speciaux.getIdFromValeur (sousType, laCase.creusable); break;
				case 'tangible': resultat = Speciaux.getIdFromValeur (sousType, laCase.tangible); break;
				case 'entree_arene': resultat = Speciaux.getIdFromValeur (sousType, laCase.entree_arene); break;
			}
		break;
	}
	return resultat;
};

Etage.setValeurFromType = function (type, idx, valeur) {
	switch (type) {
		case Fonds.type: Etage.Cases[idx].fond = valeur; break;
		case Murs.type: Etage.Cases[idx].mur = valeur; break;
		case Decors.type: Etage.Cases[idx].decor = valeur; break;
		case DecorsDessus.type: Etage.Cases[idx].decor_dessus = valeur; break;
	}
};

Etage.getDivFromType = function (type, idx) {
	var resultat = false;
	switch (type) {
		case Fonds.type: resultat = Etage.Cases[idx].divFond; break;
		case Murs.type: resultat = Etage.Cases[idx].divMur; break;
		case Decors.type: resultat = Etage.Cases[idx].divDecor; break;
		case DecorsDessus.type: resultat = Etage.Cases[idx].divDecorDessus; break;
		case Speciaux.type: resultat = Etage.Cases[idx].divSpecial; break;
	}
	return resultat;
};

Etage.setDivFromType = function (type, idx, div) {
	switch (type) {
		case Fonds.type: Etage.Cases[idx].divFond = div; break;
		case Murs.type: Etage.Cases[idx].divMur = div; break;
		case Decors.type: Etage.Cases[idx].divDecor = div; break;
		case DecorsDessus.type: Etage.Cases[idx].divDecorDessus = div; break;
	}
};

Etage.enleveCouche = function (divAEnlever) {
	var divParente = divAEnlever.parentNode;
	var divEnfante = divAEnlever.childNodes[0];
	divParente.replaceChild(divEnfante, divAEnlever);
};

Etage.getCoucheSuperposee = function(typeObjet, id, valeur) {
	if (typeObjet.enlevable && valeur == 0) return false;
	var div = document.createElement("div");
	div.id = typeObjet.getId(id);
	div.type = typeObjet.type;
	div.className = "caseVue " + ((typeObjet.type == Fonds.type) ? "caseFond " : "") + typeObjet.getClass(valeur);
	return div;
};

Etage.changeCase = function (objet, idx, nvlleValeur) {
	var divCourante = Etage.getDivFromType (objet.type, idx);
	var sousType = objet.getSousType (nvlleValeur);
	var valeurCourante = Etage.getValeurFromType (objet.type, idx, sousType);

	// Cas 1 : la div doit être simplement modifiée.
	if (divCourante && (nvlleValeur != 0 || !objet.enlevable)) {
		var styleDecorCourant = objet.getClass(valeurCourante);
		var styleNouveauDecor = objet.getClass(nvlleValeur);
		ManipCss.remplaceClasse(divCourante, styleDecorCourant, styleNouveauDecor);
	}

	// Cas 2 : la div doit être supprimée.
	if (divCourante && nvlleValeur == 0 && objet.enlevable) {
		Etage.enleveCouche (divCourante);
		Etage.setDivFromType (objet.type, idx, false);
	}

	// Cas 3 : la div doit être rajoutée.
	if (!divCourante && nvlleValeur != 0) {
		var nouvelleDiv = Etage.getCoucheSuperposee (objet, Etage.Cases[idx].id, nvlleValeur);
		Etage.setDivFromType (objet.type, idx, nouvelleDiv);
		Etage.insereCouche (nouvelleDiv, idx);
	}
};

Etage.getCoucheAction = function(i) {
	var div = document.createElement("div");
	div.id = 'actions_' + Etage.Cases[i].id;
	div.className = "caseVue";
	div.title = "x = " + Etage.Cases[i].x + " | y = " + Etage.Cases[i].y;
	div.index = i;

	div.onclick = function () {
		Pinceau.appliquer();
	};
	div.onmouseover = function () {
		Etage.deselectionne();
		Etage.selectionne(this);
	};
	return div;
};

// Sérialise les modifications apportées à l’étage
Etage.ecrireModifs = function () {
	var input = document.plateau.modifs;
	var valeur = "";
	for (var i = 0; i < Etage.CasesModifiees.length; i++) {
		var c = Etage.CasesModifiees[i];
		var modif_mur = Etage.Cases[c.idx].mur != c.mur;
		var modif_dec = Etage.Cases[c.idx].decor != c.decor;
		var modif_des = Etage.Cases[c.idx].decor_dessus != c.decor_dessus;
		var modif_fon = Etage.Cases[c.idx].fond != c.fond;
		var modif_psg = Etage.Cases[c.idx].passage != c.passage;
		var modif_pvp = Etage.Cases[c.idx].pvp != c.pvp;
		var modif_pio = Etage.Cases[c.idx].creusable != c.creusable;
		var modif_tan = Etage.Cases[c.idx].tangible != c.tangible;
		var modif_arn = Etage.Cases[c.idx].entree_arene != c.entree_arene;

		if (modif_mur || modif_dec || modif_fon || modif_des || modif_psg || modif_pvp || modif_pio || modif_tan || modif_arn)
		{
			valeur += c.id + "|";
			if (modif_mur) valeur += "m=" + c.mur + ",";
			if (modif_dec) valeur += "d=" + c.decor + ",";
			if (modif_des) valeur += "s=" + c.decor_dessus + ",";
			if (modif_fon) valeur += "f=" + c.fond + ",";
			if (modif_psg) valeur += "p=" + ((c.passage) ? "1" : "0") + ",";
			if (modif_pvp) valeur += "v=" + ((c.pvp) ? "1" : "0") + ",";
			if (modif_pio) valeur += "c=" + ((c.pio) ? "1" : "0") + ",";
			if (modif_tan) valeur += "t=" + ((c.tan) ? "1" : "0") + ",";
			if (modif_arn) valeur += "a=" + ((c.entree_arene) ? "1" : "0") + ",";
			valeur += ";";
		}
	}
	input.value = valeur;
}

// Sélectionne et surligne les cases survolées par le pinceau
Etage.selectionne = function (div) {
	var voisines = Etage.getCasesVoisines(div.index);
	for (var i = 0; i < voisines.length; i++) {
		var uneCase = Etage.Cases[voisines[i]];
		ManipCss.ajouteClasse(uneCase.divSpecial, "etageSurligne");
		Etage.CasesSelectionnees.push(voisines[i]);
	}
}

// Désélectionne et désurligne toutes les cases
Etage.deselectionne = function() {
	for (var i = 0; i < Etage.CasesSelectionnees.length; i++) {
		var uneCase = Etage.Cases[Etage.CasesSelectionnees[i]];
		ManipCss.enleveClasse(uneCase.divSpecial, "etageSurligne");
	}
	Etage.CasesSelectionnees = new Array();
}
