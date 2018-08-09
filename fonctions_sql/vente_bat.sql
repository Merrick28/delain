CREATE OR REPLACE FUNCTION public.vente_bat(integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/***********************************************************/
/* fonction vente_bat : vent des objets de quête aux       */
/*   batiments administratifs                              */
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
-- 1.2 position du perso (il faut qu'il soit sur un batiment administratif)
	select into pos_perso ppos_pos_cod
		from perso_position
		where ppos_perso_cod = personnage;
	if not found then
		code_retour := '<p>Erreur ! Position vendeur non trouvée !';
		return code_retour;
	end if;
	select into temp lieu_cod
		from lieu,lieu_position
		where lpos_pos_cod = pos_perso
		and lpos_lieu_cod = lieu_cod
		and lieu_tlieu_cod = 9;
	if not found then
		code_retour := '<p>Erreur ! Vous n''êtes pas sur un batiment administratif !';
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
		code_retour := '<p>Erreur ! Vous n''avez pas assez de '||nom_obj||' dans votre inventaire.<br>';
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
		or_gagne := 100;
		num_quete := 2;
	end if;
	if num_gobj = 92 then
		px_gagne := 12;
		or_gagne := 120;
		num_quete := 3;
	end if;
	if num_gobj = 94 then
		px_gagne := 20;
		or_gagne := 400;
		num_quete := 4;
	end if;
	if num_gobj = 833 then
		px_gagne := 12;
		or_gagne := 100;
		num_quete := 20;
	end if;
        if num_gobj = 849 then
		px_gagne := 20;
		or_gagne := 1000;
		num_quete := 21;
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
        -- ajout du titre pour la récolte des citrouilles noires
        if nb_quete_perso = 1 and num_gobj = 849 then
            insert into perso_titre (ptitre_perso_cod, ptitre_titre, ptitre_date) values
            (personnage,'Défenseur lors de la Nuit de Malkiar',now());
        end if;

-- 2.5 on rajoute le compteur de quêtes
	update quete_perso
		set pquete_nombre = nb_quete_perso
		where pquete_perso_cod = personnage
		and pquete_quete_cod = num_quete;
-- 2.6 on génère un code retour
	code_retour := '<p><i>Après un temps qui vous semble interminable, la préposée au guichet a fini de remplir les formulaires nécessaires. Vous signez en bas de page.<br>';
	code_retour := code_retour||'</i><br>Félicitations, vous gagnez <b>'||trim(to_char(or_gagne,'99999'))||'</b> brouzoufs et <b>'||trim(to_char(px_gagne,'99999'));
	code_retour := code_retour||'</b> points d''expérience pour cette vente.<br>';
	return code_retour;
end;$function$

