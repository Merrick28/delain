--
-- Name: change_cible_attaque(integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function change_cible_attaque(integer, integer) RETURNS integer
    LANGUAGE plpgsql
    AS $_$/*******************************************************/
/* change_cible_attaque                                */
/*  Détermine si le monstre doit changer de cible      */
/*  suite à une attaque de perso                       */
/* Paramètres                                          */
/*  $1 = perso_cod du monstre                          */
/*  $2 = perso_cod de l'attaquant                      */
/*******************************************************/
/* Créé le 23/05/2005                                  */
/*******************************************************/
declare
	v_monstre alias for $1;
	v_perso alias for $2;
	v_des integer;
	v_cible integer;
	v_ia integer;
begin
	-- détermination de l'IA actuelle
	select into v_ia
		ia_type
		from type_ia,perso_ia
		where pia_perso_cod = v_monstre
		and pia_ia_type = ia_type;
	if not found then
		select into v_ia
			ia_type
			from type_ia,perso,monstre_generique
			where perso_cod = v_monstre
			and perso_gmon_cod = gmon_cod
			and gmon_type_ia = ia_type;
		if not found then
			v_ia := 1;
		end if;
	end if;
	-- changement de cible
	select into v_cible
		perso_cible
		from perso
		where perso_cod = v_monstre;
	if v_cible is null then
		-- pas de cible actuelle, on prend l'attaquant
		update perso set perso_cible = v_perso where perso_cod = v_monstre;
	else
		if v_ia = 4 then
			if lancer_des(1,100) < 60 then
				update perso set perso_cible = v_perso where perso_cod = v_monstre;
			end if;
		else
			if lancer_des(1,100) < 25 then
				update perso set perso_cible = v_perso where perso_cod = v_monstre;
			end if;
		end if;
	end if;
	return 0;
end;$_$;


ALTER FUNCTION public.change_cible_attaque(integer, integer) OWNER TO delain;