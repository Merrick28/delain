function add_lieu(pos)
{
	var td = $("#pos-"+pos);
	td.removeAttr( "onclick" );		// desactiver le click sur cette celulle
	
	var image = $( "#pos-"+pos+" img" )[0];
	image.src = image.src.substring(0, image.src.indexOf("automap_"))+ "automap_1_3.gif" ;		// remplacer la case vide par notre lieux
	
	var posx=td.attr("data-posx");
	var posy=td.attr("data-posy");
	
	// Gestion des données à sauvegarder
	var list_position = $("#list-position");	
	list_position.append( $('<input type="hidden" name="positions[]" value="'+posx+','+posy+'">') );

	// Gestion de l'affichage
	var list_lieux = $("#list-lieux");	
	list_lieux.append( "X="+posx+" Y="+posy+"<br>");
	
	$("#count-lieu").text($("#count-lieu").text()*1+1);

}