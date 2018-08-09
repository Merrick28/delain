CREATE OR REPLACE FUNCTION public.f_prix_objet(integer, integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/*************************************************************/
/* fonction prix_objet : donne le prix par défaut d'un objet */
/*  dans un magasin                                          */
/* on passe en paramètres :                                  */
/*  $1 = lieu_cod du magasin                                 */
/*  $2 = obj_cod de l'objet                                  */
/* on a un entier en retour                                  */
/*************************************************************/
declare
	code_retour integer;					-- prix par défaut
	num_magasin alias for $1;			-- lieu_cod du magasin
	num_objet alias for $2;				-- gobj_cod de l'objet
	temp integer;							-- fourre tout
	marge integer;							-- marge du magasin
	v_prix_obj integer;
	v_prix_gobj integer;
begin
	select into temp mtar_prix
		from magasin_tarif
		where mtar_lieu_cod = num_magasin
		and mtar_gobj_cod = (select obj_gobj_cod from objets where obj_cod = num_objet);
	if found then
		code_retour := temp;
	else
		select into code_retour gobj_valeur
			from objet_generique,objets
			where obj_cod = num_objet
			and obj_gobj_cod = gobj_cod;
	end if;
	select into v_prix_obj,v_prix_gobj obj_valeur,gobj_valeur
		from objets,objet_generique
		where obj_cod = num_objet
		and obj_gobj_cod = gobj_cod;
	code_retour := code_retour - v_prix_gobj + v_prix_obj;
-- on rajoute la marge du magasin
	select into marge lieu_marge from lieu
		where lieu_cod = num_magasin;
	code_retour := ceil(code_retour + ((marge * code_retour)/100));
	return code_retour;
end;$function$

