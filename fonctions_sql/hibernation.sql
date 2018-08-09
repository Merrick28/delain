CREATE OR REPLACE FUNCTION public.hibernation(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/**********************************************************/
/* fonction hibernation : met un compte en hibernation    */
/* on passe en paramètre le numéro du compte              */
/* on a en retour une chaine                              */
/**********************************************************/
declare
	code_retour text;
	v_compte alias for $1;
	v_defi record;        -- Le défi en cours pour chaque perso du compte
begin
-- le compte
	update compte set compt_hibernation = 'O',compt_dfin_hiber = now() + '4 days'::interval, compt_ddeb_hiber = null
		where compt_cod = v_compte;
-- les persos
	update perso set perso_actif = 'H' where perso_actif = 'O' 
		and perso_type_perso = 1 
		and perso_actif = 'O' 
		and perso_cod in 
		(select pcompt_perso_cod from perso_compte 
		where pcompt_compt_cod = v_compte) ;
-- les familiers
	update perso set perso_actif = 'H' where perso_actif = 'O' 
		and perso_type_perso = 3 
		and perso_actif = 'O' 
		and perso_cod in 
		(select pfam_familier_cod from perso_compte, perso_familier
		where pcompt_compt_cod = v_compte
		and pcompt_perso_cod = pfam_perso_cod) ;
-- les locks
	delete from lock_combat where lock_cible in
		(select pcompt_perso_cod from perso_compte 
		where pcompt_compt_cod = v_compte) ;
	delete from lock_combat where lock_attaquant in
		(select pcompt_perso_cod from perso_compte 
		where pcompt_compt_cod = v_compte) ;
	delete from lock_combat where lock_cible in
		(select pfam_familier_cod from perso_compte, perso_familier
		where pcompt_compt_cod = v_compte
		and pcompt_perso_cod = pfam_perso_cod) ;
	delete from lock_combat where lock_attaquant in
		(select pfam_familier_cod from perso_compte, perso_familier
		where pcompt_compt_cod = v_compte
		and pcompt_perso_cod = pfam_perso_cod) ;

-- les défis
	for v_defi in
		select defi_cod, pcc.pcompt_compt_cod as compte_l, pcl.pcompt_compt_cod as compte_c
		from defi
		inner join perso_compte pcc on pcc.pcompt_perso_cod = defi_cible_cod
		inner join perso_compte pcl on pcl.pcompt_perso_cod = defi_lanceur_cod
		where defi_statut IN (0, 1) and v_compte IN (pcc.pcompt_compt_cod, pcl.pcompt_compt_cod)
	loop
--modif Teruo		if v_defi.defi_lanceur_cod = v_compte then -- Le lanceur
                if v_defi.compte_l = v_compte then -- Le lanceur
			perform defi_abandonner(v_defi.defi_cod, 'L');
		else                              -- La cible
			perform defi_abandonner(v_defi.defi_cod, 'C');
		end if;
	end loop;

-- code_retour 
	code_retour := 'OK';
	return code_retour;
end;$function$

