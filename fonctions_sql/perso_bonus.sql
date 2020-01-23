-- Function: public.perso_bonus(integer)

-- DROP FUNCTION public.perso_bonus(integer);

CREATE OR REPLACE FUNCTION public.perso_bonus(integer)
  RETURNS text AS
$BODY$/***********************************/
/* perso_bonus                     */
/* $1 = perso_cod                  */
/* 23/01/2020 - Sauf Equipement    */
/***********************************/
declare
	code_retour text;
	v_perso alias for $1;
	temp integer;
	temp_car integer;
	bonus_signe text;
	ligne record;
begin
	select into temp count(*)
		from bonus,bonus_type
		where bonus_perso_cod = v_perso
		and bonus_tbonus_libc = tbonus_libc;

	select into temp_car count(*)
		from carac_orig inner join perso on perso_cod = corig_perso_cod
		where perso_cod = v_perso;

	if temp+temp_car = 0 then
		return 'Aucun bonus/malus magique';
	end if;
	code_retour := '';
	for ligne in
		select tonbus_libelle,bonus_valeur,bonus_nb_tours
		from bonus,bonus_type
		where bonus_perso_cod = v_perso
		and bonus_tbonus_libc = tbonus_libc
		and bonus_mode != 'E' loop
			if (ligne.bonus_valeur > 0) then
				bonus_signe := '+';
			else
				bonus_signe := '';
			end if;
			code_retour := code_retour||'<br>'||ligne.tonbus_libelle||'('||bonus_signe||trim(to_char(ligne.bonus_valeur,'99999999'))||' / '||trim(to_char(ligne.bonus_nb_tours,'999999'))||' tours).';
	end loop;

	/* Marlyza - 11/06/2018 - ajout des bonus de carac */
	for ligne in
		select corig_type_carac,
			case corig_type_carac
				when 'FOR' then perso_for - corig_carac_valeur_orig
				when 'CON' then perso_con - corig_carac_valeur_orig
				when 'INT' then perso_int - corig_carac_valeur_orig
				when 'DEX' then perso_dex - corig_carac_valeur_orig
			end as bonus_carac,
			case when coalesce(corig_nb_tours, 0)=0 then ' => '||to_char(corig_dfin,'dd/mm/yyyy hh24:mi:ss')
			else ' / '|| trim(to_char(coalesce(corig_nb_tours, 0),'999999')) || ' tours' end as corig_delai
			from carac_orig
			inner join perso on perso_cod = corig_perso_cod
			where perso_cod = v_perso and corig_mode !='E' loop
			if (ligne.bonus_carac > 0) then
				bonus_signe := '+';
			else
				bonus_signe := '';
			end if;
			code_retour := code_retour||'<br>'||ligne.corig_type_carac||'('||bonus_signe||trim(to_char(ligne.bonus_carac,'99999999'))||ligne.corig_delai ||').';
	end loop;
	return substr(code_retour,5);
end;
	$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION public.perso_bonus(integer)
  OWNER TO delain;
