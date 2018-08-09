CREATE OR REPLACE FUNCTION public.f_prix_obj_perso_v(integer, integer, integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/**********************************************************/
/* fonction f_prix_obj_perso_v : donne le prix d'un objet */
/*  pour un perso X dans une échoppe Y à la vente         */
/* on passe en paramètres :                               */
/*  $1 = perso_cod du perso                               */
/*  $2 = lieu_cod du magasin                              */
/*  $3 = obj_cod de l'objet                               */
/* on a en retour un entier correspondant au prix         */
/**********************************************************/
declare
	code_retour integer;
	personnage alias for $1;
	v_lieu_cod alias for $2;
	v_obj_cod alias for $3;
	v_bonus integer;
	v_prix integer;
	v_modif_guilde integer;
	v_etat numeric;
	type_mag integer;
begin
	v_prix := f_prix_objet(v_lieu_cod,v_obj_cod);
--
-- BONUS
--
	select into v_bonus
		obon_prix
		from bonus_objets,objets
		where obj_cod = v_obj_cod
		and obj_obon_cod = obon_cod;
	if found then
		v_prix := v_prix + v_bonus;
	end if;
-- 
-- MODIFICATEUR DE PERSO
--
	v_prix := floor(v_prix / (mod_vente(personnage,v_lieu_cod))); 
--
-- ETAT
-- 
	select into v_etat
		obj_etat from objets where obj_cod = v_obj_cod;
	v_prix := round(v_prix*v_etat/100);
	select into type_mag
		lieu_tlieu_cod
		from lieu
		where lieu_cod = v_lieu_cod;
	if type_mag = 11 then
		v_prix := round(v_prix*getparm_n(46)*0.01);
	end if;
if type_mag = 21 then
		v_prix := round(v_prix*getparm_n(46)*0.01);
	end if;
	if type_mag = 14 then
		v_prix := round(v_prix*getparm_n(47)*0.01);
	end if;
	return v_prix;
end;
$function$

