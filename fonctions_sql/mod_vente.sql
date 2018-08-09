CREATE OR REPLACE FUNCTION public.mod_vente(integer, integer)
 RETURNS numeric
 LANGUAGE plpgsql
AS $function$/****************************************************************/
/* fonction mod_vente : donne un modificateur de vente pour les */
/*  magasins. Calculé en fonction de l'alignement et du nombre  */
/*  de transactions passées par un perso dans un magasin donné. */
/* On passe en paramètres :                                     */
/*  $1 = perso_cod                                              */
/*  $2 = lieu_cod                                               */
/* on a en retour un numeric correspondant au bonus de prix :   */
/*  ex : 0.95 signifie qu'on ne paierai que 95% du prix d'achat */
/*  et que le magasin achetera à 105% du prix de vente.         */
/****************************************************************/
/* créé le 08/03/2004                                           */
/* 03/04/2009: Suppression de l'influence du karma sur les prix */
/****************************************************************/
declare
------------------------------------------------------------------
-- variables de retour
------------------------------------------------------------------
	code_retour numeric;					-- multiplicateur
------------------------------------------------------------------
-- variables du perso
------------------------------------------------------------------	
	personnage alias for $1;			-- perso_cod
	karma_perso numeric;				-- karma du perso
	nb_visite_magasin numeric;			-- nombre de transactions dans magasin
------------------------------------------------------------------
-- variables du magasin
------------------------------------------------------------------
	magasin alias for $2;				-- lieu_cod du magasin
	align_magasin integer;				-- aligenement du magasin1
	v_neutre integer;						-- neutralité du magasin
------------------------------------------------------------------
-- variables de calcul
------------------------------------------------------------------
	temp_numeric numeric;
begin
	code_retour := 1;
------------------------------------------------------------------
-- Etape 1 : on calcule en fonction du la réput
------------------------------------------------------------------	
--	select into align_magasin,v_neutre lieu_alignement,lieu_neutre
--		from lieu
--		where lieu_cod = magasin;
--	select into karma_perso perso_kharma
--		from perso
--		where perso_cod = personnage;
--	if v_neutre = 0 then
--		if (@(align_magasin)) > 100 then
--			if (@(align_magasin)) > 200 then
--				if ((align_magasin*karma_perso) < 0) then
--					code_retour := code_retour + 0.15;
--				else
--					code_retour := code_retour - 0.15;
--				end if;
--			else
--				if ((align_magasin*karma_perso) > 0) then
--					code_retour := code_retour - 0.15;
--				end if;
--			end if;
--		end if;
--	end if;
------------------------------------------------------------------
-- Etape 2 : en fonction du nombre de visites
------------------------------------------------------------------	
	select into nb_visite_magasin pvmag_nombre
		from perso_visite_magasin
		where pvmag_perso_cod = personnage
		and pvmag_lieu_cod = magasin;
	if not found then
		nb_visite_magasin := 0;
	end if;
	temp_numeric := nb_visite_magasin::numeric/100;
	code_retour := code_retour - temp_numeric;
	if code_retour > 1.15 then
		code_retour := 1.15;
	end if;
	if code_retour < 0.85 then
		code_retour := 0.85;
	end if;
	return code_retour;
end;$function$

