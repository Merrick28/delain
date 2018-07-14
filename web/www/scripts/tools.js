/**
 * ============================ divers outils en Js/jquery ============================
 *
 * Créé le 10/4/2018 par Marlyza
 *
 *      - Fonction ajax pour facilité les echange front/back (sans recharment de page)
 *      - Fonction pour poster une url avec des données
 *      - Fonction liées à l'ajout/suppression de favoris dans la barre de menu à gauche.
 *      - Fonction pour gestion barre switch et gestion du menu (dropdowns)
 */


function runAsync(service, callback, context) { // service = {request: request, ws:ws, type_data:type_data, data:data, response_type:response_type}
    // préparation des parametres
    if (!context) context=null;
    var request = service.request;						// Parametre mandatory !
    var ws = "/jeu_test/ajax_request.php";				// type de webservice par defaut
    var response_type = "json";							// type de données attendues par défaut

    // Gestion des paramètres d'entrée
    if (service.ws) ws=service.ws;				                    // WS spécifique
    if (service.response_type) response_type=service.response_type;	// type de données en reponse spécifique

    if (!service.data)
    {   // préparation sans données additionnelles
        var ajax_data={request:request};
        var method = "GET";
    }
    else
    {	// sinon ajouter les data au bout de la request et poster!
        var ajax_data=$.extend( {request:request}, service.data );
        var method = "POST";
    }

    if (service.type_data=='json')
    {	// Poster des données au format json
        ajax_data=JSON.stringify(ajax_data);
        method = "POST";
    }

    return $.ajax({
        url: ws,		// appel du ws
        root: 'data',
        dataType: response_type,
        method: method,
        async: true,
        data: ajax_data,
        cache:false,
        success: function (data) {
            if (callback) {
                callback(data, context); 						// on appele de la callback avec le context
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            console.log("runAsync: Ajax Error Status=> " + textStatus);
            console.log("runAsync: Ajax Error=> " + errorThrown);
        }
    });
}

function post(path, params, values)
{

    var form = document.createElement("form");
    form.setAttribute("method", "post");
    form.setAttribute("action", path);

    for(var key in params)
    {
            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("type", "hidden");
            hiddenField.setAttribute("name", params[key]);
            hiddenField.setAttribute("value", values[key]);
            form.appendChild(hiddenField);
    }

    document.body.appendChild(form);
    form.submit();
}

//------------------------------------------------------------------------------------------------------------------
//--- gestion des favoris
function popRequestStatus(r, context)
{
    if (r.resultat==0)
    {
        $.nok({ message: r.data.message, type: "success", stay: 3, sticky: false });
        // si le resultat est conforme on switch les icones, et on met a jour le menu de gauche
        if (context.action=="add")
        {
            $("#fav-add-"+context.type+"-"+context.misc_cod).css("display","none");
            $("#fav-del-"+context.type+"-"+context.misc_cod).css("display","block");
            $("#barre-favoris").css("display","block");  // au cas où c'est le premier element
            $("#barre-favoris").append('<div id="fav-link-' + r.data.pfav_cod + '"><img src="/images/favoris.png" alt=""> <a href="' + r.data.link + '">' + r.data.nom + '</a></div>');
        }
        else
        {
            $("#fav-add-"+context.type+"-"+context.misc_cod).css("display","block");
            $("#fav-del-"+context.type+"-"+context.misc_cod).css("display","none");
            $("#fav-link-"+r.data.pfav_cod).remove();
        }
    }
    else
    {
        $.nok({ message: r.message, type: "info", stay: 5, sticky: false });
    }
}

function addSortFavoris(type, sort_cod)
{
    var nom = $.trim($("#fav-add-sort-" + sort_cod).parent().next().text());
    nom = nom.substring(0, nom.indexOf(" ("));

    $("#fav-add-sort-" + sort_cod).parent().prepend('<div id="spop-sort" class="spop-overlay"><i>Nom du favoris:</i><input id="spop-sort-nom" style="margin:4px;" type="text" value="' + nom + '"><br><center><input id="spop-sort-valid" type="submit" class="test" value="Ajouter !">&nbsp;&nbsp;<input id="spop-sort-cancel" type="submit" class="test" value="Annuler"></div></center></div>');

    $(document).click(function (event) {
        if ((event.target.id == "spop-sort-cancel") || (event.target.closest("div").id != "spop-sort"))
        {
            $(document).unbind("click");
            $('#spop-sort').remove();
        }
        else if (event.target.id == "spop-sort-valid")
        {
            var nom = $("#spop-sort-nom").val();
            runAsync({request: "add_favoris", data:{nom:nom, type:"sort"+type, misc_cod:sort_cod}}, popRequestStatus, {action:"add", type:"sort", misc_cod:sort_cod})
            $(document).unbind("click");
            $('#spop-sort').remove();
            event.stopPropagation();
        }
        else
        {
            event.stopPropagation();
        }
    });

 }


function delSortFavoris(type, sort_cod)
{
    runAsync({request: "del_favoris", data:{type:"sort"+type, misc_cod:sort_cod}}, popRequestStatus, {action:"del", type:"sort", misc_cod:sort_cod})
}

//------------------------------------------------------------------------------------------------------------------
//--- gestion du switch de perso rapide
function switch_perso(perso)
{
    if (!perso || perso=="" || perso==0) return;       // pour les bouton vide
    post("/switch_rapide.php", ["url","perso"], [window.location.href, perso]);
}

//------------------------------------------------------------------------------------------------------------------
//--- gestion du menu  on/off (en version mobile)
function switch_menu(e)
{
  if (e.target.nodeName!="DIV") return;   // ne pas switcher si clic sur un lien.

  if ($("#dropdown-menu").css("display")=="none")
  {
      $("#dropdown-menu").css("display","block");
      $("#dropdown-button").css("display","none");
      $("#colonne0-icons").css("display","none");
      $("#colonne1").css({"position": "absolute", "top" : "12px",  "left" : "12px" });
      $("#colonne2").css({"margin-left": "185px" });
  }
  else
  {
      $("#dropdown-menu").css("display","none");
      $("#dropdown-button").css("display","block");
      $("#colonne0-icons").css("display","block");
      $("#colonne1").css({"position": "relative", "top" : "0px",  "left" : "0px" });
      $("#colonne2").css({"margin-left": "0px" });
  }
}
