CREATE OR REPLACE FUNCTION public.f_prix_gobj(integer, integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/*************************************************************/
/* fonction prix_gobj : donne le prix par défaut d'un gobj   */
/*  dans un magasin                                          */
/* on passe en paramètres :                                  */
/*  $1 = lieu_cod du magasin                                 */
/*  $2 = gobj_cod                                            */
/* on a un entier en retour                                  */
/*************************************************************/
declare
	code_retour integer;					-- prix par défaut
	num_magasin alias for $1;			-- lieu_cod du magasin
	num_objet alias for $2;				-- gobj_cod de l'objet
	temp integer;							-- fourre tout
	marge integer;							-- marge du magasin
begin
	select into temp mtar_prix
		from magasin_tarif
		where mtar_lieu_cod = num_magasin
		and mtar_gobj_cod = num_objet;
	if found then
		code_retour := temp;
	else
		select into code_retour gobj_valeur
			from objet_generique
			where gobj_cod = num_objet;
	end if;
	return code_retour;
end;$function$

