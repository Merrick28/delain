Validation = {
	Types: {},			// Les types de validations prédéfinies
	Champs: [],			// Les champs enregistrés comme à valider
	OK: true,			// Le dernier résultat de validation globale
	CSSInvalide: "invalide",	// Classe CSS à appliquer en cas d’invalidité
};

Validation.Types = {
	Numerique: /^[\+\-]?\d+[\.,]?\d*$/,
	Entier: /^[\+\-]?\d+$/,
	Roliste: /^[\+\-]?\d+([dD]\d+(\s*[\+\-]\s*\d+)?)?$/,
	EntierOuVide: /^[\+\-]?\d+$|^$/,
};

// Ajoute à la liste des éléments à valider le champ dont l’id est donné
Validation.Ajoute = function (id, patron, type) {
	Validation.Champs.push({id: id, patron: patron, type:type});
};

// Enlève de la liste le champ dont l’id est donné
Validation.Enleve = function (id, type) {
	for (var i = 0; i < Validation.Champs.length; i++) {
		if (Validation.Champs[i].id == id && Validation.Champs[i].type == type) {
			Validation.Champs.splice(i, 1);
			i--;
		}
	}
};

Validation.TrouveDonnees = function (id) {
	for (var i = 0; i < Validation.Champs.length; i++) {
		if (Validation.Champs[i].id == id) {
			return Validation.Champs[i];
		}
	}
};

// Valide un champ dont l’id est donné.
Validation.ValideParId = function (id) {
	var donnees = Validation.TrouveDonnees(id);
	var champ = document.getElementById(id);
	var resultat = false;
	if (champ) {
		var valeur = champ.value.trim();
		var patron = donnees.patron;
		resultat = patron.test(valeur);
		if (!resultat) {
			Validation.OK = false;
			ManipCss.ajouteClasse (champ, Validation.CSSInvalide);
		}
		else
			ManipCss.enleveClasse (champ, Validation.CSSInvalide);
	}
	return resultat;
};

// Valide l’ensemble des champs enregistrés.
Validation.Valide = function () {
	Validation.OK = true;
	for (var i = 0; i < Validation.Champs.length; i++) {
		var champ = document.getElementById(Validation.Champs[i].id);
		if (champ) {
			var valeur = champ.value.trim();
			var patron = Validation.Champs[i].patron;
			if (!patron.test(valeur)) {
				Validation.OK = false;
				ManipCss.ajouteClasse (champ, Validation.CSSInvalide);
			}
			else
				ManipCss.enleveClasse (champ, Validation.CSSInvalide);
		}
	}
	return Validation.OK;
};
