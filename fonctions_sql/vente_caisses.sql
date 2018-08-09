CREATE OR REPLACE FUNCTION public.vente_caisses(integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/***********************************************************/
/* fonction vente_caisses : quête des caisses volées        */
/* on passe en paramètres :                                */
/*   $1 = perso_cod vendeur                                */
/*   $2 = le gobj_cod de l'objet vendu                     */
/* on a en retour une chaine html exploitable directement  */
/*   dans la page d'action                                 */
/***********************************************************/
/* créée le 07/06/2006                                     */
/***********************************************************/
declare
-------------------------------------------------------------
-- variables de retour
-------------------------------------------------------------
	code_retour text;		-- texte de retour formaté
	potion integer;
-------------------------------------------------------------
-- variables concernant le vendeur
-------------------------------------------------------------
	personnage alias for $1;
	pts_prestige integer;		--points de prestige du perso
-------------------------------------------------------------
-- variables concernant l'objet vendu
-------------------------------------------------------------
	num_gobj alias for $2;		-- gobj_cod de l'objet à vendre
	nom_obj text;			-- nom de l'objet vendu
-------------------------------------------------------------
-- variables de contrôle
-------------------------------------------------------------
	temp integer;			-- fourre tout
	l_objet record;			-- record pour effacement de l'inventaire
	num integer;
	
begin

-- Verif quantité des objets à vendre
	select into temp
		count(perobj_cod) from perso_objets,objets
		where perobj_perso_cod = personnage
		and perobj_obj_cod = obj_cod
		and obj_gobj_cod = num_gobj;
	if temp < 1 then
		code_retour := '<p>Erreur ! Vous ne possédez pas de '||nom_obj||' dans votre inventaire.<br>';
		return code_retour;
	end if;
-- on insère un point de prestige suite à cette action
	select into temp perso_prestige 
		from perso 
		where perso_cod = personnage;
	pts_prestige := temp + 1 ;
	update perso set perso_prestige = pts_prestige where perso_cod = personnage;
-- on enlève les objets de l'inventaire
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
select into potion gobj_cod
			from objet_generique
			where gobj_tobj_cod = 21
			and gobj_cod not in (412,561)
	                order by random();
perform cree_objet_perso(potion,personnage);
select into potion gobj_cod
			from objet_generique
			where gobj_tobj_cod = 21
			and gobj_cod not in (412,561)
	                order by random();
perform cree_objet_perso(potion,personnage);
select into potion gobj_cod
			from objet_generique
			where gobj_tobj_cod = 21
			and gobj_cod not in (412,561)
	                order by random();
perform cree_objet_perso(potion,personnage);
	return code_retour;
	end loop;
end;$function$

