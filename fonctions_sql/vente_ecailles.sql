CREATE OR REPLACE FUNCTION public.vente_ecailles(integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/***********************************************************/
/* fonction vente_ecailles : quête des dispensaire         */
/* on passe en paramètres :                                */
/*   $1 = perso_cod vendeur                                */
/*   $2 = le gobj_cod de l'objet vendu                     */
/* on a en retour une chaine html exploitable directement  */
/*   dans la page d'action                                 */
/***********************************************************/
/* créée le 04/06/2006                                     */
/***********************************************************/
declare
-------------------------------------------------------------
-- variables de retour
-------------------------------------------------------------
	code_retour text;		-- texte de retour formaté
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
	
begin

-- Verif quantité des objets à vendre
	select into temp
		count(perobj_cod) from perso_objets,objets
		where perobj_perso_cod = personnage
		and perobj_obj_cod = obj_cod
		and obj_gobj_cod = num_gobj;
	if temp < 10 then
		code_retour := '<p>Erreur ! Vous n''avez pas assez de '||nom_obj||' dans votre inventaire.<br>';
		code_retour := code_retour||'Le guérisseur n''accepte de vous reprendre les écailles que si vous en avez au moins 10.';
		return code_retour;
	end if;
-- on insère un point de prestige suite à cette action
	update perso set perso_prestige = pts_prestige +1,perso_px = perso_px + 20,perso_po = perso_po + 1000 where perso_cod = personnage;
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
	end loop;
	return code_retour;
end;$function$

