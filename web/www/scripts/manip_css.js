var ManipCss = {};

// Ajoute une classe CSS à un élément donné
ManipCss.ajouteClasse = function (objet, classe) {
	var lesClasses = objet.className.split(" ");
	if (lesClasses.indexOf(classe) == -1) {
		lesClasses.push(classe);
		objet.className = lesClasses.join(" ");
	}
}

// Enlève une classe CSS d’un élément donné
ManipCss.enleveClasse = function (objet, classe) {
	var lesClasses = objet.className.split(" ");
	var indexClasse = lesClasses.indexOf(classe);
	var changement = false;
	while (indexClasse > -1) {
		changement = true;
		lesClasses.splice(indexClasse, 1);
		indexClasse = lesClasses.indexOf(classe);
	}
	if (changement)
		objet.className = lesClasses.join(" ");
}

// Remplace une classe CSS d’un élément donné par une autre (et ajoute la nouvelle si l’ancienne n’existait pas)
ManipCss.remplaceClasse = function (objet, ancienneClasse, nouvelleClasse) {
	ManipCss.enleveClasse(objet, ancienneClasse);
	ManipCss.ajouteClasse(objet, nouvelleClasse);
}