CREATE OR REPLACE FUNCTION public.trouve_objet(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* Fonction trouve_objet : Localise un objet dans les souterrains*/
/* On passe en paramètres :                                      */
/*    1 : le objet_cod                                           */
/* On a en sortie une chaine html utilisable directement         */
/*****************************************************************/
/* Liste des modifications :                                     */
/*    28/4/9 Bleda première mise en ligne                        */
/*****************************************************************/
declare
	v_objet alias for $1;
	v_etage integer;
	v_pnom text;
	v_pcod integer;
	v_x integer;
	v_y integer;
	v_magasin text;
	code_retour text;
begin
	code_retour := '';
	-- Au sol ?
	select into v_etage, v_x, v_y pos_etage, pos_x, pos_y from positions, objet_position where pos_cod = pobj_pos_cod and pobj_obj_cod = v_objet;
	if found then
		return ' en position ' || v_x::text || '/' || v_y::text || ' étage ' || v_etage::text;
	end if;
	-- En inventaire ?
	select into v_pcod perobj_perso_cod from perso_objets where perobj_obj_cod = v_objet;
	if found then
		select into v_pnom, v_etage, v_x, v_y 
			perso_nom, pos_etage, pos_x, pos_y 
		from perso 
			left join perso_position on ppos_perso_cod=perso_cod 
			left join positions on pos_cod=ppos_pos_cod 
		where perso_cod=v_pcod;

		if found then
			return ' dans l''inventaire de ' || v_pnom::text || ' (' || v_pcod::text || '), étage ' || v_etage::text || ' x:' || v_x::text || ' y:' || v_y::text;
		end if;
	end if;
	-- En magasin ?
	select into v_magasin lieu_nom from lieu, stock_magasin where lieu_cod = mstock_lieu_cod and mstock_obj_cod = v_objet;
	if found then
		return 'Dans l''échoppe : ' || v_magasin;
	end if;

	return 'Objet non trouvé (Ni au sol, ni en inventaire, ni en magasin)';
end;	$function$

