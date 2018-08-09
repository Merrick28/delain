CREATE OR REPLACE FUNCTION public.deplace_antre_araignee(integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$declare
	personnage alias for $1;
	v_pos alias for $2;
	code_retour text;
begin
	update perso_position
		set ppos_pos_cod = v_pos
		where ppos_perso_cod = personnage;
	code_retour := 'Vous êtes brusquement transporté vers un nouveau lieu, sans pouvoir rien y faire.<br>
	 Le sol, ou plutôt la chose grisâtre sur laquelle vous posez le pied est collante... et instable, comme si toute la structure sur laquelle vous 	marchez était en mouvement, ce qui tend déjà à vous donner la nausée et à ralentir fortement votre marche. Le silence est pesant, inquiétant... Les sons, au lieu de se répercuter sur les murs comme dans le reste des souterrains, sont absorbés par les centaines de fils de soie qui forment autour de vous une paroi fragile et impénétrable à la fois, tel un tunnel capitonné... ou une prison insoupçonnée ?<br>
Par endroit, là où la toile laisse une ouverture, tel un passage pour l''habitant, on peut apercevoir le vide béant et menaçant, qui fait craindre la moindre rupture de la fine cloison filandreuse. Le plus impressionnant reste peut-être de pouvoir deviner, loin, très loin en contrebas, les décors et même les aventuriers qui évoluent au masque de mort... ';
	return code_retour;
end;
	$function$

