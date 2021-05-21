
--
-- Name: cree_monstre_auto(integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION cree_monstre_auto(integer) RETURNS text
    LANGUAGE plpgsql
    AS $$/*****************************************************/
/* fonction cree_monstre_auto : cree les monstres    */
/*  en automatique en fonction de la répart donnée   */
/*                                                   */
/*****************************************************/
declare
	code_retour text;
	l_etage record;
	nb_monstre numeric;
	nb_monture numeric;
	nb_joueur numeric;
	repart_actu numeric;
	repart_normale numeric;
	nb_manque integer;
	nb_ideal integer;
	v_cree integer;
	v_etage_ref integer;
	type_monstre integer;
	cpt integer;
begin
	code_retour := '';
	for l_etage in select * from rep_mon_joueur order by rjmon_etage loop
		nb_manque := 0;
		select into nb_monstre, nb_monture sum(case when gmon_monture='N' THEN 1 ELSE 0 END),sum(case when gmon_monture!='N' THEN 1 ELSE 0 END)
			from perso,positions,perso_position,monstre_generique
			where perso_type_perso = 2
			and perso_actif = 'O'
			and ppos_perso_cod = perso_cod
			and ppos_pos_cod = pos_cod
			and gmon_cod = perso_gmon_cod
			and pos_etage = l_etage.rjmon_etage
			and perso_gmon_cod != 544
			and perso_race_cod not in (53,54);
		select into nb_joueur COALESCE(sum(case when pos_etage in (0, -1) AND perso_tangible='N' then 0 else 1 end),0)
			from perso,positions,perso_position
			where perso_type_perso = 1
			and perso_actif = 'O'
			and ppos_perso_cod = perso_cod
			and ppos_pos_cod = pos_cod
			and pos_etage = l_etage.rjmon_etage;
		if nb_joueur < 10 then
			select into v_etage_ref etage_reference 
				from etage where etage_numero = l_etage.rjmon_etage;
			if v_etage_ref = l_etage.rjmon_etage then
				-- Etage principal: Il s''autoréférence
				nb_joueur := 10;
			else
				-- Etage de référence différent: C'est une antre
				nb_joueur := 20;
			end if;
		end if;

		/* Traitement des monstres (or monture) */
		repart_actu := nb_monstre / nb_joueur;
		repart_normale := l_etage.rjmon_repart;
		if repart_actu < repart_normale then
			nb_ideal := round(nb_joueur * repart_normale);
			nb_manque := nb_ideal - nb_monstre;
			if nb_manque > getparm_n(85) then
				nb_manque := getparm_n(85);
			end if;
			if nb_manque > 0 then
				cpt := 0;
				while(cpt < nb_manque) loop
					type_monstre := choix_monstre_etage(l_etage.rjmon_etage, 0);
					if l_etage.rjmon_type = 'P' then
						v_cree := cree_monstre(type_monstre,l_etage.rjmon_etage);
					end if;
					if l_etage.rjmon_type = 'H' then
						v_cree := cree_monstre_hasard(type_monstre,l_etage.rjmon_etage);
					end if;
					cpt := cpt + 1;
				end loop;

			end if;
		end if;

    /* Traitement des montures (or monstre) */
		repart_actu := nb_monture / nb_joueur;
		repart_normale := l_etage.rjmon_monture;
		if repart_actu < repart_normale then
			nb_ideal := round(nb_joueur * repart_normale);
			nb_manque := nb_ideal - nb_monstre;
			if nb_manque > getparm_n(85) then
				nb_manque := getparm_n(85);
			end if;
			if nb_manque > 0 then
				cpt := 0;
				while(cpt < nb_manque) loop
					type_monstre := choix_monstre_etage(l_etage.rjmon_etage, 1);
          -- forcement sur portail pour les montures
          v_cree := cree_monstre(type_monstre,l_etage.rjmon_etage);
					cpt := cpt + 1;
				end loop;

			end if;
		end if;


		code_retour := code_retour||'Etage : '||trim(to_char(l_etage.rjmon_etage,'9999'))||' - ';
		code_retour := code_retour||trim(to_char(repart_actu,'99999990D99'))||' - '||trim(to_char(repart_normale,'999990D99'));
		code_retour := code_retour||' - '||trim(to_char(nb_manque,'9999'))||'
';
		
	
	end loop;
	return code_retour;
end;$$;


ALTER FUNCTION public.cree_monstre_auto(integer) OWNER TO delain;

--
-- Name: FUNCTION cree_monstre_auto(integer); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION cree_monstre_auto(integer) IS 'Crée les monstres nécessaires au renouvèlement des espèces.';