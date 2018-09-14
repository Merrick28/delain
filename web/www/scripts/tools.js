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


//------------------------------------------------------------------------------------------------------------------
//--- Outil d'aide à la selection d'un perso, lieu, monstre generique, etc....
function getTableCod_update() { // fonction de mise à jour de la liste (voir jeu_test\ajax_request.php)
    // recupe des paramètres
    var params= {} ;
    if ( $( "#spop-tablecod-perso-pnj" ).length ) params.perso_pnj = $( "#spop-tablecod-perso-pnj" ).prop( "checked" ) ? true : false ;
    if ( $( "#spop-tablecod-perso-monstre" ).length ) params.perso_monstre = $( "#spop-tablecod-perso-monstre" ).prop( "checked" ) ? true : false ;
    if ( $( "#spop-tablecod-perso-fam" ).length ) params.perso_fam = $( "#spop-tablecod-perso-fam" ).prop( "checked" ) ? true : false ;

    //executer le service asynchrone
    runAsync({request: "get_table_cod", data:{recherche:$("#spop-tablecod-cherche").val(), table:$("#spop-tablecod-table").val(), params:params}}, function(d){
        // Ici on récupère le nombre d'entrée et la liste de nom recherché (max 10) => affichage dans le popup
        if (d.resultat == -1) {
            $("#spop-serchlist").html("<b>Erreur:</b> "+d.message);
        } else if (!d.data || !d.data.count) {
            $("#spop-serchlist").html("<b>Aucun élément ne correspond à la recherche.</b>");
        } else {
            var data = d.data.data ;
            var content = "";
            for (i in data) {
                content += '<a id="spop-tablecod-select-'+i+'" data-spop-cod="'+data[i].cod+'"  data-spop-nom="'+data[i].nom+'" href="#">'+data[i].nom+'</a> ('+data[i].cod+')<br>';
            }
            if (data.length<d.data.count) content+='<br><i style="font-size:7pt;">Il y a encore '+(d.data.count-i)+' autres éléments.</i>';
            $("#spop-serchlist").html(content);
        }
    });
}

function getTableCod(divname, table, titre, params={})
{
    var options = "" ;
    if (table=="perso"){
        options += 'avec les monstres: <input type="checkbox" id="spop-tablecod-perso-monstre" onClick="getTableCod_update();">';
        options += 'avec les fam.: <input type="checkbox" id="spop-tablecod-perso-fam" onClick="getTableCod_update();">';
        options += 'Limiter aux PNJ: <input type="checkbox" id="spop-tablecod-perso-pnj" onClick="getTableCod_update();">';
    }

    $("#" + divname+"_cod").parent().prepend('<div id="spop-tablecod" class="spop-overlay">' +
                        '<input type="hidden" id="spop-tablecod-table" value="'+table+'">' +
                        '<div style="width:100%; background-color:#800000;color:white;font-weight:bold;text-align:center;padding:3px 0 3px 0;">'+titre+'</div>' +
                        '<br><div id="spop-serchlist" style="width: 350px; height: 160px;">Faites une recherche.</div>' +
                        '<br><i>Rechercher:</i><input id="spop-tablecod-cherche" style="margin:4px;" type="text" value=""><br>' +
                         options+
                        '<br><center><input id="spop-tablecod-cancel" type="submit" class="test" value="Annuler"></div></center></div>');

    $("#spop-tablecod-cherche").focus();
    $("#spop-tablecod-cherche").keyup(getTableCod_update);


    $(document).click(function (event) {
        if (event.target.id == "spop-tablecod-cancel")
        {
            $(document).unbind("click");
            $('#spop-tablecod').remove();
        }
        else if ( event.target.id.substr(0, 21) == "spop-tablecod-select-")
        {
            var element = $("#"+event.target.id);
            $("#" + divname+"_cod").val (element.attr("data-spop-cod") );
            $("#" + divname+"_nom").text (element.attr("data-spop-nom") );
            $(document).unbind("click");
            $('#spop-tablecod').remove();
        }
    });

}