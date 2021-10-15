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
//--- sauvegarde des notes de QA
function popSaveQANotesStatus(r, context)
{
    if (r.resultat==0)
    {
        $.nok({message: "Vos notes personnelles ont été sauvegardées!!", type: "success", stay: 3, sticky: false});
    } else {
        $.nok({ message: r.message, type: "info", stay: 5, sticky: false });
    }

}

function addQANotes(id)
{
    var notes = "";
    var texte = $("#"+id).clone() ;
    texte.find("form").remove();
    texte.find("div").each(  function (index) {
        var n = $(this).clone() ;
        n.find("p").contents().unwrap();
        notes+=n.html();
    } );

    runAsync({request: "add-qa-notes", data:{notes:notes }}, popSaveQANotesStatus, {})

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
            $("#barre-favoris").append('<div id="fav-link-' + r.data.pfav_cod + '"><img onclick="javascript:delSortFavoris('+context.type.substr(-1)+','+context.misc_cod+');" src="/images/favoris.png" alt=""> <a href="' + r.data.link + '">' + r.data.nom + '</a></div>');
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

function addSortFavoris(type, misc_cod)
{
    var fav = "#fav-add-sort-" + type + "-" + misc_cod ;
    var nom = $.trim($(fav).parent().next().text());
    nom = nom.substring(0, nom.indexOf(" ("));

    $(fav).parent().prepend('<div id="spop-sort" class="spop-overlay"><em>Nom du favoris:</em><input id="spop-sort-nom" style="margin:4px;" type="text" value="' + nom + '"><br><center><input id="spop-sort-valid" type="submit" class="test" value="Ajouter !">&nbsp;&nbsp;<input id="spop-sort-cancel" type="submit" class="test" value="Annuler"></div></center></div>');

    $(document).click(function (event) {
        if ((event.target.id == "spop-sort-cancel") || (event.target.closest("div").id != "spop-sort"))
        {
            $(document).unbind("click");
            $('#spop-sort').remove();
        }
        else if (event.target.id == "spop-sort-valid")
        {
            var nom = $("#spop-sort-nom").val();
            runAsync({request: "add_favoris", data:{nom:nom, type:"sort"+type, misc_cod:misc_cod}}, popRequestStatus, {action:"add", type:"sort-"+type, misc_cod:misc_cod})
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


function delSortFavoris(type, misc_cod)
{
    runAsync({request: "del_favoris", data:{type:"sort"+type, misc_cod:misc_cod}}, popRequestStatus, {action:"del", type:"sort-"+type, misc_cod:misc_cod})
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
    var params = {} ;
    if ( $( "#spop-tablecod-perso-perso" ).length ) params.perso_perso = false; else params.perso_perso = true ;
    if ( $( "#spop-tablecod-perso-pnj" ).length ) params.perso_pnj = $( "#spop-tablecod-perso-pnj" ).prop( "checked" ) ? true : false ;
    if ( $( "#spop-tablecod-perso-monstre" ).length ) params.perso_monstre = $( "#spop-tablecod-perso-monstre" ).prop( "checked" ) ? true : false ;
    if ( $( "#spop-tablecod-perso-fam" ).length ) params.perso_fam = $( "#spop-tablecod-perso-fam" ).prop( "checked" ) ? true : false ;
    if ( $( "#spop-tablecod-meca-etage_cod" ).length ) params.etage_cod = $( "#spop-tablecod-meca-etage_cod" ).val() ;
    if ( $( "#spop-tablecod-etape-aquete_cod" ).length ) params.aquete_cod = $( "#spop-tablecod-etape-aquete_cod" ).val() ;
    if ( $( "#spop-tablecod-etape-aqetape_cod" ).length ) params.aqetape_cod = $( "#spop-tablecod-etape-aqetape_cod" ).val() ;
    if ( $( "#spop-tablecod-element-aquete_cod" ).length ) params.aquete_cod = $( "#spop-tablecod-element-aquete_cod" ).val() ;
    if ( $( "#spop-tablecod-element-aqetape_cod" ).length ) params.aqetape_cod = $( "#spop-tablecod-element-aqetape_cod" ).val() ;
    if ( $( "#spop-tablecod-element-aqelem_type" ).length ) params.aqelem_type = $( "#spop-tablecod-element-aqelem_type" ).val() ;
    if ( $( "#spop-tablecod-position-lieu" ).length ) params.position_lieu = $( "#spop-tablecod-position-lieu" ).prop( "checked" ) ? true : false ;
    if ( $( "#spop-tablecod-position-etage" ).length ) params.position_etage = $( "#spop-tablecod-position-etage" ).val() ;
    if ( $( "#spop-tablecod-position-x" ).length ) params.position_x = $( "#spop-tablecod-position-x" ).val() ;
    if ( $( "#spop-tablecod-position-y" ).length ) params.position_y = $( "#spop-tablecod-position-y" ).val() ;
    if ( $( "#spop-tablecod-objet-generique-sort" ).length ) params.objet_generique_sort = $( "#spop-tablecod-objet-generique-sort" ).prop( "checked" ) ? true : false ;
    if ( $( "#spop-tablecod-objet-generique-sort-bm" ).length ) params.objet_generique_sort_bm = $( "#spop-tablecod-objet-generique-sort-bm" ).prop( "checked" ) ? true : false ;
    if ( $( "#spop-tablecod-objet-generique-bm" ).length ) params.objet_generique_bm = $( "#spop-tablecod-objet-generique-bm" ).prop( "checked" ) ? true : false ;
    if ( $( "#spop-tablecod-objet-generique-equipe" ).length ) params.objet_generique_equipe = $( "#spop-tablecod-objet-generique-equipe" ).prop( "checked" ) ? true : false ;

    //executer le service asynchrone
    runAsync({request: "get_table_cod", data:{recherche:$("#spop-tablecod-cherche").val(), table:$("#spop-tablecod-table").val(), params:params}}, function(d){
        // Ici on récupère le nombre d'entrée et la liste de nom recherché (max 10) => affichage dans le popup
        if (d.resultat == -1)
        {
            $("#spop-serchlist").html("<strong>Erreur:</strong> "+d.message);
        }
        else if (!d.data || !d.data.count)
        {
            $("#spop-serchlist").html("<strong>Aucun élément ne correspond à la recherche.</strong>");
        }
        else
        {
            var data = d.data.data ;
            var content = "";
            for (i in data) {
                content += '<a id="spop-tablecod-select-'+i+'" data-spop-cod="'+data[i].cod+'"  data-spop-nom="'+data[i].nom+'" data-spop-num1="'+(data[i].num1 ? data[i].num1 : '' )+'" href="#">'+data[i].nom+'</a> ('+data[i].cod+')'+(data[i].info ? ' <i style="font-size: 9px;">'+data[i].info+'</em>' : '' )+'<br>';
            }
            if (data.length<d.data.count) {
                content+='<br><em style="font-size:7pt;">Il y a encore '+(d.data.count-i)+' autres éléments.</em>';
                if ($.inArray(d.data.table, ["element", "etape", "position", "perso"])<0) {
                    content+='&nbsp; <a href="/jeu_test/admin_table_list.php?table='+d.data.table+'" target="_blank"><i style="font-size:7pt;">Voir la table complète</em></a>';
                }
            }
            $("#spop-serchlist").html(content);
        }
    });
}

function getTableCod(divname, table, titre, params)
{
    //id des elements
    var divname_cod = $.isArray(divname) ? divname[0] : divname+"_cod" ;
    var divname_nom = $.isArray(divname) ? divname[1] : divname+"_nom" ;
    var divname_num_1 = $.isArray(divname) ? (divname.length>2 ? divname[2] : "") : divname+"_num_1" ;

    var options = "" ;
    if (table=="perso"){
        if (params=="monstre")
        {
            options += '<input id="spop-tablecod-perso-perso" type="hidden" value="false">';
        }
        else
        {
            options += 'avec les monstres: <input type="checkbox" id="spop-tablecod-perso-monstre" onClick="getTableCod_update();">';
            options += 'avec les fam.: <input type="checkbox" id="spop-tablecod-perso-fam" onClick="getTableCod_update();">';
            options += 'Limiter aux PNJ: <input type="checkbox" id="spop-tablecod-perso-pnj" onClick="getTableCod_update();">';
        }
    } else if (table=="position")
    {
        options += 'Etage: <input type="text" size=3 id="spop-tablecod-position-etage" value="'+params[0]+'" onChange="getTableCod_update();"> ';
        options += 'X = <input type="text" size=3 id="spop-tablecod-position-x" value="'+params[1]+'" onChange="getTableCod_update();"> ';
        options += 'Y = <input type="text" size=3 id="spop-tablecod-position-y" value="'+params[2]+'" onChange="getTableCod_update();"> ';
        options += 'Limiter aux lieux: <input type="checkbox" id="spop-tablecod-position-lieu" onClick="getTableCod_update();">'
        options += '<br><i style="font-size: 9px">Nota: Si ni l\'étage, la position X ou Y ne sont pas définis, la recherche est limitées aux lieux</i>';
    } else if (table=="etape")
    {
        if (params)
        {
            options += '<input id="spop-tablecod-etape-aquete_cod" type="hidden" value="'+params[0]+'">';
            options += '<input id="spop-tablecod-etape-aqetape_cod" type="hidden" value="'+params[1]+'"><u><i>Etapes spéciales</u></i>:<br>';
            options += '<a style="margin-left:20px;" id="spop-tablecod-select--1"  data-spop-cod="0"  data-spop-nom="Etape suivante" href="#">Etape suivante</a> (0)<br>';
            options += '<a style="margin-left:20px;" id="spop-tablecod-select--2"  data-spop-cod="-1"  data-spop-nom="Quitter/Abandonner" href="#">Quitter/Abandonner</a> (-1)<br>';
            options += '<a style="margin-left:20px;" id="spop-tablecod-select--3"  data-spop-cod="-2"  data-spop-nom="Terminer avec succès" href="#">Terminer avec succès</a> (-2)<br>';
            options += '<a style="margin-left:20px;" id="spop-tablecod-select--4"  data-spop-cod="-3"  data-spop-nom="Echec de la quête" href="#">Echec de la quête</a> (-3)<br>';
        }
        else
        {
            options += '<input id="spop-tablecod-etape-aquete_cod" type="hidden" value="0">';
            options += '<input id="spop-tablecod-etape-aqetape_cod" type="hidden" value="0">';
        }
    } else if (table=="element")
    {
        options += '<input id="spop-tablecod-element-aquete_cod" type="hidden" value="'+params[0]+'">';
        options += '<input id="spop-tablecod-element-aqetape_cod" type="hidden" value="'+params[1]+'">';
        options += '<input id="spop-tablecod-element-aqelem_type" type="hidden" value="'+params[2]+'">';
    } else if (table=="meca")
    {
        options += '<input id="spop-tablecod-meca-etage_cod" type="hidden" value="'+params[0]+'">';
   } else if (table=="objet_generique")
   {
        options += 'Limiter: Attach. Sorts: <input type="checkbox" id="spop-tablecod-objet-generique-sort" onChange="getTableCod_update();"> ';
        options += 'Sorts BM: <input type="checkbox" id="spop-tablecod-objet-generique-sort-bm" onChange="getTableCod_update();"> ';
        options += ' Bonus/malus : <input type="checkbox" id="spop-tablecod-objet-generique-bm" onChange="getTableCod_update();"> ';
        options += ' Cond. Equip.: <input type="checkbox" id="spop-tablecod-objet-generique-equipe" onChange="getTableCod_update();"> ';
   }

    $("#" + divname_cod).parent().prepend('<div id="spop-tablecod" class="spop-overlay">' +
                        '<input type="hidden" id="spop-tablecod-table" value="'+table+'">' +
                        '<div style="width:100%; background-color:#800000;color:white;font-weight:bold;text-align:center;padding:3px 0 3px 0;">'+titre+'</div>' +
                        '<br><div id="spop-serchlist" style="width:450px; height:180px; overflow:hidden; white-space:nowrap;">Faites une recherche.</div>' +
                        '<br><em>Rechercher:</em><input id="spop-tablecod-cherche" style="margin:4px;" type="text" value=""><br>' +
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
            $("#" + divname_cod).val (element.attr("data-spop-cod") ).trigger('change');
            $("#" + divname_nom).text (element.attr("data-spop-nom") );
            if (divname_num_1 != "") $("#" + divname_num_1).val (element.attr("data-spop-num1") );
            $(document).unbind("click");
            $('#spop-tablecod').remove();
        }
    });

    getTableCod_update(); // Faire un premier chargement (pour avoir au moins 10 lignes

}

function setNomByTableCod(divname, table, cod) { // fonction de mise à jour d'un champ nom quand on connait le cod
    //executer le service asynchrone
    $("#"+divname).text("");
    runAsync({request: "get_table_nom", data:{table:table, cod:cod}}, function(d){
        if ((d.resultat == 0)&&(d.data)&&(d.data.nom))
        {
            $("#"+divname).text(d.data.nom);
        }
    });
}

function  addQueteAutoParamRow(elem, M)
{
    if ((elem.parent().find("tr[id^='row-']").length<M) || (M == 0))
    {

        var row = elem[0].id ;
        var s = row.split('-') ;
        var r = (1+1*s[2]);
        var new_row = s[0]+'-'+s[1]+'-'+r+'-';
        var new_elem = '<tr id="'+new_row+'" style="display: block;">'+elem.html().replace(new RegExp(row,'g'), new_row)+'</tr>';
        $(new_elem).insertAfter(elem);

        //Maintenant que l'élément est inséré, on raz les valeurs parasites qui ont été dupliquées de la précédente entrée
        $('*[id^="'+new_row+'"]').each(function( index ) {
            if ($( this ).attr("data-entry"))
            {
                if ($( this ).attr("data-entry") == "val")
                {
                    $( this ).val("");
                }
                else if ($( this ).attr("data-entry") == "text")
                {
                    $( this ).text("");
                }
            }
        });
    }
    else
    {
        alert('Il ne doit pas y avoir plus de '+M+' valeur(s) pour ce paramètre!')
    }
}

function  delQueteAutoParamRow(elem, n)
{
    var min = (n>0 ? n : 1) ;
    if ( elem.parent().find("tr[id^='row-']").length > min  )
    {
        elem.remove();
    }
    else
    {
        alert('Il doit rester au moins '+min+' valeur(s) pour ce paramètre!')
    }
}

function  switchQueteAutoParamRow(param, type)
{
    var t= $("#alt-type-"+param).val();
    if (t=="element")
    {
        // switcher vers perso/obj, lieu/, etc...
        $('tr[id^="alt-'+param+'"]').css("display","none");
        $('tr[id^="row-'+param+'-"]').css("display","block");
        $('tr[id^="add-row-'+param+'-"]').css("display","block");
        $("#alt-type-"+param).val(type);
    }
    else
    {
        // switcher vers element.
        $('tr[id^="alt-'+param+'"]').css("display","block");
        $('tr[id^="row-'+param+'-"]').css("display","none");
        $('tr[id^="add-row-'+param+'-"]').css("display","none");
        $("#alt-type-"+param).val("element");
    }
}

function  setQuetePositionCod(rowid)
{
    var pos_x = 1*$('#'+rowid+'aqelem_param_num_1').val();
    var pos_y = 1*$('#'+rowid+'aqelem_param_num_2').val();
    var pos_etage = 1*$('#'+rowid+'aqelem_param_num_3').val();
    runAsync({request: "get_table_info", data:{info:"pos_cod", pos_x:pos_x, pos_y:pos_y, pos_etage:pos_etage}}, function(d){
        if ((d.resultat == 0)&&(d.data)&&(d.data.pos_cod))
        {
            $('#'+rowid+'aqelem_misc_cod').val(d.data.pos_cod);
        }
    });

}

function copyToClipboard(element) {
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val($(element).text()).select();
    document.execCommand("copy");
    $temp.remove();
}
