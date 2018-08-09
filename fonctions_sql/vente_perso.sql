CREATE OR REPLACE FUNCTION public.vente_perso(integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/***********************************************************/
/* fonction vente_perso : vend des objets de quête aux     */
/*   PNJ                                                   */
/* on passe en paramètres :                                */
/*   $1 = perso_cod vendeur                                */
/*   $2 = le gobj_cod de l'objet vendu                     */
/* on a en retour une chaine html exploitable directement  */
/*   dans la page d'action                                 */
/***********************************************************/
/* créée le 04/03/2004                                     */
/***********************************************************/
declare
-------------------------------------------------------------
-- variables de retour
-------------------------------------------------------------
	code_retour text;					-- texte de retour formaté
-------------------------------------------------------------
-- variables concernant le vendeur
-------------------------------------------------------------
	personnage alias for $1;
	pa integer;							-- nombre de PA du vendeur
	pos_perso integer;				-- position du vendeur
	nb_quete_perso integer;			-- nombre de fois où le vendeur a fait cette quête
-------------------------------------------------------------
-- variables concernant l'objet vendu
-------------------------------------------------------------
	num_gobj alias for $2;			-- gobj_cod de l'objet à vendre
	nom_obj text;						-- nom de l'objet vendu
-------------------------------------------------------------
-- variables de contrôle
-------------------------------------------------------------
	temp integer;						-- fourre tout
	l_objet record;					-- record pour effacement de l'inventaire
	px_gagne integer;					-- px de base gagnés par le vendeur
	or_gagne integer;					-- brouzoufs gagnés par le vendeur
	num_quete integer;				-- numéro de la quête associée
begin
-------------------------------------------------------------
-- Etape 1 : contrôles
-------------------------------------------------------------
-- 1.1 : nombre de PA
	select into pa
		perso_pa
		from perso
		where perso_cod = personnage;
	if not found then
		code_retour := '<p>Erreur ! Vendeur non trouvé !';
		return code_retour;
	end if;
	if pa < getparm_n(36) then
		code_retour := 'Erreur ! Vous n''avez pas assez de PA pour effectuer cette action !';
		return code_retour;
	end if;
-- 1.5 existance du type d'objets
	select into nom_obj gobj_nom
		from objet_generique
		where gobj_cod = num_gobj;
	if not found then
		code_retour := '<p>Erreur ! Type d''objet non trouvé !';
		return code_retour;
	end if;
-- 1.4 quantité des objets à vendre
	select into temp
		count(perobj_cod) from perso_objets,objets
		where perobj_perso_cod = personnage
		and perobj_obj_cod = obj_cod
		and obj_gobj_cod = num_gobj;
	if temp < 10 then
		code_retour := '<p>Erreur ! Vous n''avez pas assez de '||nom_obj||' dans vote inventaire.<br>';
		code_retour := code_retour||'Le minimum pour une vente est de 10 unités.';
		return code_retour;
	end if;
-------------------------------------------------------------
-- Etape 2 : contrôles OK, on passe à la vente
-------------------------------------------------------------
-- 2.1 : on enlève les PA du vendeur
	update perso
		set perso_pa = perso_pa - getparm_n(36)
		where perso_cod = personnage;
-- 2.2 : on enlève ses objets de l'inventaire
	for l_objet in
		select perobj_obj_cod,perobj_cod
		from perso_objets,objets
		where perobj_perso_cod = personnage
		and perobj_obj_cod = obj_cod
		and obj_gobj_cod = num_gobj
		limit 10 loop
		delete from perso_objets
			where perobj_cod  = l_objet.perobj_cod;
		delete from perso_identifie_objet
			where pio_obj_cod = l_objet.perobj_obj_cod;
		delete from objets
			where obj_cod = l_objet.perobj_obj_cod;
	end loop;
-- 2.3 : on rajoute les brouzoufs
	if num_gobj = 91 then
		px_gagne := 10;
		or_gagne := 80;
		num_quete := 2;
	code_retour := '<i>"Le rat, c''est la vermine par excellence. J''vous l''dis moi, c''est maladie et compagnie ces bêtes là. Y''a des sacrés pièces en plus par ici, j''préfères n''en voir que l''arrière, croyez moi ! Enfin, ce que j''en dis moi ! C''est du bon boulot en tout qu''rat ! Uh uh, une bonne vieille feinte de ratier ça !".</i> L''individu s''éloigne alors en vous saluant, réprimant avec peine un fou-rire inexplicable. ';

	end if;
	if num_gobj = 92 then
		px_gagne := 12;
		or_gagne := 100;
		num_quete := 3;

code_retour := '<i>En plus, j''ai une sainte horreur des serpents, ça rampe et c''est gluant. J''suis bien content que vous en ayez fait passer plus d''un de vie à trépas ! Sinon, vous faites quoi des peaux ? Parce que j''ai un cousin qui a sa petite affaire de maroquinerie."</i> ricane le ratier en vous quittant non sans vous saluer cordialement.';

	end if;
	if num_gobj = 94 then
		px_gagne := 20;
		or_gagne := 350;
		num_quete := 4;
code_retour := '<i>"J''ai entendu dire que les Orlanthis faisaient des vêtements avec la soie d''araignée !? Z''avez déjà entendu quelque chose d''aussi absurde vous ? Toujours à vouloir se mettre en avant ceux-là. Notez bien, j''pourrais m''faire un sacré pactole en leur revendant tout ça. Mais non, rêvons pas, va bien falloir que j''aille bruler tout ça, faut que ça blinque : des souterrains propres pour des aventuriers épanouis comme on dit chez les ratiers"</i> lance l''homme en reprenant la route après vous avoir remercié avec gratitude. ';
	end if;
	update perso set perso_po = perso_po + or_gagne
		where perso_cod = personnage;
-- 2.4 XP gagné : pour cela on regarde si la quête a déjà été effectuée
	select into nb_quete_perso
		pquete_nombre
		from quete_perso
		where pquete_quete_cod = num_quete
		and pquete_perso_cod = personnage;
	if not found then
		nb_quete_perso = 0;
		insert into quete_perso
			(pquete_perso_cod,pquete_quete_cod,pquete_nombre)
			values
			(personnage,num_quete,0);
	end if;
	nb_quete_perso := nb_quete_perso + 1;
	px_gagne := round(px_gagne/nb_quete_perso);
	update perso set perso_px = perso_px + px_gagne where perso_cod = personnage;
-- 2.5 on rajoute le compteur de quêtes
	update quete_perso
		set pquete_nombre = nb_quete_perso
		where pquete_perso_cod = personnage
		and pquete_quete_cod = num_quete;
-- 2.6 on génère un code retour

	code_retour := code_retour||'<br><br>Félicitations, vous gagnez <b>'||trim(to_char(or_gagne,'99999'))||'</b> brouzoufs et <b>'||trim(to_char(px_gagne,'99999'));
	code_retour := code_retour||'</b> points d''expérience pour cette vente.<br>';
	return code_retour;
end;$function$

