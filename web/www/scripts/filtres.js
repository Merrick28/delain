/**
 * ============================ divers outils en Js/jquery ============================
 *
 * Créé le 18/3/2019 par Marlyza
 *
 *      - Fonction pour facilité lle filtrage de table
 */

function filtre_table_search(table)
{
    var search = $("#"+table+"-filtre-perso").val().toLowerCase();
    var col = $("#"+table+"-col").val();
    var type = $("input[name="+table+"-filtre-type]:checked").val() ;

    $("#"+table+" tr").each(function( index ) {
        if ($('#row-'+index).length>0)
        {
            var data_type = $('#row-'+index).attr('data-type');
            var data_partisans = $('#row-'+index).attr('data-partisans');
            var data_chevauche = $('#row-'+index).attr('data-chevauche');
            if (!data_chevauche) data_chevauche = '0' ;
            var data_monture = $('#row-'+index).attr('data-monture');
            if (!data_monture) data_monture = 'N' ;

            if (   ((search=='') || ($('#row-'+index+' td:nth-child('+col+')').text().toLowerCase().includes(search)))
                && (    (type==-1)
                     || ((type==0) && (data_partisans=='O'))
                     || ((type==1) && (type==data_type) && (data_partisans=='N'))
                     || ((type==2) && (type==data_type) && (data_monture=='N'))
                     || ((type==-2) && (data_monture=='O') && (data_chevauche=='0'))
                     || ((type==3) && (type==data_type))
                     || ((type==3) && (data_chevauche!='0'))
                   )
            )
            {
                $('#row-'+index).show();
            }
            else
            {
                $('#row-'+index).hide();
                if ($('#detail-'+index).length>0) $('#detail-'+index).css("display","none");
            }
        }
    });
}

function toggle_details(event, elem)
{
    if (($("#"+elem).length<=0) || (event.target.tagName=='A')) return;

    if ($("#"+elem).css("display") == "none")
        $("#"+elem).css("display", "");
    else
        $("#"+elem).css("display","none");
}