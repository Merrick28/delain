function toutCocher(formulaire, nom) {
    for (i = 0; i < formulaire.elements.length; ++ i)
    {
        if ((formulaire.elements[i].name.substring(0, nom.length) == nom) && (!formulaire.elements[i].disabled)) {
            formulaire.elements[i].checked = !formulaire.elements[i].checked;
        }
    }
}

function toutCocher_limite(formulaire, nom, limite) {
    var nombre_coches = 0;
    for (i = 0; i < formulaire.elements.length; ++ i)
    {
        if ((formulaire.elements[i].name.substring(0, nom.length) == nom) && (!formulaire.elements[i].disabled)){
            formulaire.elements[i].checked = !formulaire.elements[i].checked;
            if (formulaire.elements[i].checked)
                nombre_coches++;
        }
    }

    if (nombre_coches > limite)
        alert("Attention ! Vous avez coché plus de " + limite + " éléments. Du fait de limitations techniques, seuls les " + limite + " premiers seront pris en compte." );
}

function toutCompter_limite(formulaire, nom, limite) {
    var nombre_coches = 0;
    for (i = 0; i < formulaire.elements.length; ++ i)
    {
        if ((formulaire.elements[i].name.substring(0, nom.length) == nom && formulaire.elements[i].checked)  && (!formulaire.elements[i].disabled)) {
            nombre_coches++;
        }
    }

    if (nombre_coches > limite)
        alert("Attention ! Vous avez coché plus de " + limite + " éléments. Du fait de limitations techniques, seuls les " + limite + " premiers seront pris en compte." );
    
    return true;
}