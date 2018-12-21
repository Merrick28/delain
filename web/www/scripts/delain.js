// Indique si le cadre du bas de la vue doit être étendu. Par défaut, non.
var etendreVue = 0;

// Fonction appelée à l'ouverture de la vue, déterminant s'il y a lieu d'étendre le cadre du bas ou non.
function tailleCadre() {
    // lecture du cookie pour savoir si on retaille le cadre du bas ou non
    var lesCookies = document.cookie.split(";");
    for (var i = 0; i < lesCookies.length; i++) {
        var nom = lesCookies[i].substr(0, lesCookies[i].indexOf("=")).replace(/^\s+|\s+$/g, "");
        if (nom == "etendrevue") {
            etendreVue = parseInt(lesCookies[i].substr(lesCookies[i].indexOf("=") + 1));
        }
    }

    if (etendreVue == 0)
        reduireCadre();
}

// Permet de permutter entre l'état étendu ou l'état réduit du cadre du bas.
function modifierCadre() {
    etendreVue = 1 - etendreVue;
    if (etendreVue == 0)
        reduireCadre();
    else
        aggrandirCadre();
    //return false;
}

// Augmente le cadre du bas
function aggrandirCadre() {
    document.getElementById("vue_bas").style.maxHeight = "";

    // Mise en place d’un cookie pour se souvenir de ce choix
    var maintenant = new Date();
    var danslongtemps = maintenant.getTime() + (5 * 365 * 24 * 60 * 60 * 1000); // 5 ans
    var echeance = new Date();
    echeance.setTime(danslongtemps);
    document.cookie = "etendrevue=1; expires=" + echeance.toGMTString();
}

// réduit le cadre du bas
function reduireCadre() {
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

/* Change styles, en utilisant javascript */
/* Pour que cela fonctionne, il faut que l'objet cliquable
 ait la class change_class_on_click, et les attributs remplus
 par exemple
 <input type="checkbox" class="change_class_on_select" data-class-dest="<id>" data-class-onclick="Styleonclick">
 */
$(document).on('change', '.change_class_on_select', function () {
    var newstyle = $(this).attr("data-class-onclick");
    $(".allliste").removeClass(newstyle);
    if ($(this).is(':checked')) {
        var dest = $(this).attr("data-class-dest");
        $("#" + dest).addClass(newstyle);
    }
});

$(".change_class_on_hover").hover(
    function () {
        var newstyle = $(this).attr("data-class-onhover");
        var dest = $(this).attr("data-class-dest");
        $("#" + dest).addClass(newstyle);
    },
    function () {
        var newstyle = $(this).attr("data-class-onhover");
        var dest = $(this).attr("data-class-dest");
        $("#" + dest).removeClass(newstyle);
    }
);

$(".double_change_class_on_hover").hover(
    function () {
        var newstyle1 = $(this).attr("data-class-onhover1");
        var newstyle2 = $(this).attr("data-class-onhover2");
        var dest1 = $(this).attr("data-class-dest1");
        var dest2 = $(this).attr("data-class-dest2");
        $("#" + dest1).addClass(newstyle1);
        $("#" + dest2).addClass(newstyle2);
    },
    function () {
        var newstyle1 = $(this).attr("data-class-onhover1");
        var newstyle2 = $(this).attr("data-class-onhover2");
        var dest1 = $(this).attr("data-class-dest1");
        var dest2 = $(this).attr("data-class-dest2");
        $("#" + dest1).removeClass(newstyle1);
        $("#" + dest2).removeClass(newstyle2);
    }
);
