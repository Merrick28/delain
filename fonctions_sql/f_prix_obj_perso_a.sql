CREATE OR REPLACE FUNCTION public.f_prix_obj_perso_a(integer, integer, integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/**********************************************************/
/* fonction f_prix_obj_perso_a : donne le prix d’un objet */
/*  pour un perso X dans une échoppe Y à l’achat          */
/* on passe en paramètres :                               */
/*  $1 = perso_cod du perso                               */
/*  $2 = lieu_cod du magasin                              */
/*  $3 = obj_cod de l’objet                               */
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
begin
	v_prix := f_prix_objet(v_lieu_cod,v_obj_cod);

-- 
-- MODIFICATEUR DE PERSO
--
	v_prix := floor(v_prix * (mod_vente(personnage,v_lieu_cod))); 

-- 
-- MODIFICATEUR DE GUILDE ==> Supprimé ! Blade 27/12/2009
--
/*	if ((select lieu_tlieu_cod from lieu where lieu_cod = v_lieu_cod) = 21) then
		select into v_modif_guilde
			guilde_modif_noir
			from guilde,guilde_perso
			where pguilde_perso_cod = personnage
			and pguilde_valide = 'O'
			and pguilde_guilde_cod = guilde_cod;
		if found then
			v_prix := round(v_prix*(100+v_modif_guilde)*0.01*1);
		else
			v_prix := v_prix * 1;
		end if;	
	else
		if ((select lieu_tlieu_cod from lieu where lieu_cod = v_lieu_cod) != 14) then		
			select into v_modif_guilde
				guilde_modif
				from guilde,guilde_perso
				where pguilde_perso_cod = personnage
				and pguilde_valide = 'O'
				and pguilde_guilde_cod = guilde_cod;
			if found then
				v_prix := round(v_prix*(100+v_modif_guilde)*0.01);
			end if;	
		end if;
	end if;*/
	return v_prix;
end;
$function$

