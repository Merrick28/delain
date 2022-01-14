--
-- Name: compter_perso_amelioration(integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE or replace FUNCTION compter_perso_amelioration(integer) RETURNS integer
    LANGUAGE plpgsql
    AS $_$/************************************/
/* $1 = personnage                  */
/* retour = niveau du perso         */
/* anos =                           */
/*  -1 = temps non correct          */
/************************************/
declare
	personnage alias for $1;
	res integer;
	temp integer;
	v_race integer;
begin
	res := 1;
	select into temp niveau from temps_niv
	where temps = (select perso_temps_tour from perso where perso_cod = personnage);
	if not found then
		return -1;
	end if;
	res := res + temp;

	select into temp
		(perso_for - coalesce(perso_for_init, 0)) +
		(perso_dex - coalesce(perso_dex_init, 0)) +
		(perso_int - coalesce(perso_int_init, 0)) +
		(perso_con - coalesce(perso_con_init, 0)) +
		coalesce(perso_amelioration_vue, 0) +
		perso_des_regen +
		CASE perso_race_cod
			WHEN 33 THEN 0
			ELSE -1
		END +
		coalesce(perso_amelioration_degats, 0) +
		coalesce(perso_amelioration_armure, 0) +
		coalesce(perso_amel_deg_dex, 0) +
		coalesce(perso_nb_amel_repar, 0) +
		coalesce(perso_nb_receptacle, 0)+
		(select count(pcomp_cod) from perso_competences where pcomp_perso_cod = perso_cod and pcomp_pcomp_cod IN (25,63,66,72,75)) +
		2 * (select count(pcomp_cod) from perso_competences where pcomp_perso_cod = perso_cod and pcomp_pcomp_cod IN (61,64,67,73,76)) +
		3 * (select count(pcomp_cod) from perso_competences where pcomp_perso_cod = perso_cod and pcomp_pcomp_cod IN (62,65,68,74,77)) +
		CASE perso_race_cod
			WHEN 2 THEN -1
			ELSE 0
		END
	from perso
	where perso_cod = personnage;

	res := res + temp;

	-- sorts
	select into temp, v_race coalesce(perso_amelioration_nb_sort, 0), perso_race_cod
	from perso
	where perso_cod = personnage;
	if v_race in (1, 3) then
		temp := temp / 4;
	end if;

	res := res + temp;
	return res;
end;$_$;


ALTER FUNCTION public.compter_perso_amelioration(integer) OWNER TO delain;

--
-- Name: FUNCTION compter_perso_amelioration(integer); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION compter_perso_amelioration(integer) IS 'Permet de comptabiliser le nombre de niveaux pris par un personnage';