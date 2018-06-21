
// Indique si le cadre du bas de la vue doit être étendu. Par défaut, non.
var etendreVue = 0;

// Fonction appelée à l'ouverture de la vue, déterminant s'il y a lieu d'étendre le cadre du bas ou non.
function tailleCadre()
{
    // lecture du cookie pour savoir si on retaille le cadre du bas ou non
    var lesCookies=document.cookie.split(";");
    for (var i = 0; i < lesCookies.length; i++)
    {
        var nom = lesCookies[i].substr(0, lesCookies[i].indexOf("=")).replace(/^\s+|\s+$/g,"");
        if (nom == "etendrevue")
        {
            etendreVue = parseInt(lesCookies[i].substr(lesCookies[i].indexOf("=") + 1));
        }
    }

    if (etendreVue == 0)
        reduireCadre();
}

// Permet de permutter entre l'état étendu ou l'état réduit du cadre du bas.
function modifierCadre()
{
    etendreVue = 1 - etendreVue;
    if (etendreVue == 0)
        reduireCadre();
    else
        aggrandirCadre();
    //return false;
}

// Augmente le cadre du bas
function aggrandirCadre()
{
    document.getElementById("vue_bas").style.maxHeight = "";

    // Mise en place d’un cookie pour se souvenir de ce choix
    var maintenant = new Date();
    var danslongtemps = maintenant.getTime() + (5 * 365 * 24 * 60 * 60 * 1000); // 5 ans
    var echeance = new Date();
    echeance.setTime(danslongtemps);
    document.cookie = "etendrevue=1; expires=" + echeance.toGMTString();
}

// réduit le cadre du bas
function reduireCadre()
{
    var hauteurCadreGauche = document.getElementById("vue_gauche").offsetHeight;
    var hauteurCadreDroit = document.getElementById("vue_droite").offsetHeight;
    var hauteurVueHaut = Math.max(hauteurCadreGauche, hauteurCadreDroit);
    var hauteurEcran;
    if (document.all)
        hauteurEcran = document.body.clientHeight;
    else
        hauteurEcran = window.innerHeight;

    var resteEcran = hauteurEcran - hauteurVueHaut - 100;
    var hauteurVueBas = Math.max(resteEcran, 200);
    document.getElementById("vue_bas").style.maxHeight = "" + (hauteurVueBas) + "px";

    // Mise en place d’un cookie pour se souvenir de ce choix
    var maintenant = new Date();
    var danslongtemps = maintenant.getTime() + (5 * 365 * 24 * 60 * 60 * 1000); // 5 ans
    var echeance = new Date();
    echeance.setTime(danslongtemps);
    document.cookie = "etendrevue=0; expires=" + echeance.toGMTString();
}
