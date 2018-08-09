CREATE OR REPLACE FUNCTION public.nettoie(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************/
/* fonction nettoie : nettoie les souterrains        */
/* on passe en params 0                              */
/* on a en retour un texte explicatif                */
/*****************************************************/
declare
	code_retour text;
	ligne record;
	ligne_objet record;
	temps interval;
	cpt integer;
begin
	cpt := 0;
	code_retour := '';
-- on efface les objets en trop
	for ligne in select tobj_cod,tobj_nettoyage,tobj_libelle
		from type_objet
		where tobj_nettoyage is not null loop
		temps := ligne.tobj_nettoyage::text||' days';
		for ligne_objet in select pobj_obj_cod
			from objet_position,objets,objet_generique
			where pobj_obj_cod = obj_cod	
			and obj_gobj_cod = gobj_cod
			and gobj_tobj_cod = ligne.tobj_cod 
			and pobj_dlache < now() - temps loop
			delete from objet_position
				where pobj_obj_cod = ligne_objet.pobj_obj_cod;
			delete from perso_identifie_objet
				where pio_obj_cod = ligne_objet.pobj_obj_cod;
			delete from objets
				where obj_cod = ligne_objet.pobj_obj_cod;
			cpt := cpt + 1;
		end loop;		
		code_retour := code_retour||ligne.tobj_libelle||' : '||trim(to_char(cpt,'9999999'))||' nettoyés.<br>';	
		cpt := 0;
	end loop;

	return code_retour;
end;$function$

