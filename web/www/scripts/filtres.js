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
            var data_type = $( this ).attr('data-type');
            var data_partisans = $( this ).attr('data-partisans');
            console.log( index + ": " + $( this ).text() );
            console.log( type+' / '+data_type+' * '+data_partisans);

            if (   ((search=='') || ($('#row-'+index+' td:nth-child('+col+')').text().toLowerCase().includes(search)))
                && ((type==-1) || ((type==0) && (data_partisans=='O')) || ((type==1) && (type==data_type) && (data_partisans=='N')) || ((type==2) && (type==data_type)) || ((type==3) && (type==data_type)))
            )
            {
                $('#row-'+index).show();
            }
            else
            {
                $('#row-'+index).hide();
            }
        }
    });
}