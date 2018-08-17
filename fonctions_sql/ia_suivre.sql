-- Function: public.ia_suivre(integer)

-- DROP FUNCTION public.ia_suivre(integer);

CREATE OR REPLACE FUNCTION public.ia_suivre(integer)
  RETURNS text AS
$BODY$/*****************************************************/
/* fonction ia_suivre                                */
/*    reçoit en arguments :                          */
/* $1 = perso_cod du monstre                         */
/*    retourne en sortie en entier non lu            */
/*    les évènements importants seront mis dans la   */
/*    table des evenemts admins                      */
/* Cette fonction effectue les actions automatiques  */
/*  des monstres pour éviter que le MJ qui a autre   */
/*  à faire jouer tout à la mimine....               */
/*****************************************************/
/* 12/08/2005 : création                             */
/*****************************************************/

declare
	-------------------------------------------------------
	-- variables E/S
	-------------------------------------------------------
	code_retour text;				-- code sortie
	-------------------------------------------------------
	-- variables de renseignements du monstre
	-------------------------------------------------------
	v_monstre alias for $1;  -- perso_cod du monstre
	v_niveau integer;        -- niveau du monstre
	v_exp numeric;           -- xp du monstre
	v_pa integer;            -- pa du monstre
	v_cible integer;         -- cible
	actif varchar(2);        -- actif ?
	doit_jouer integer;      -- 0 pour non, 1 pour oui
	v_dlt timestamp;         -- dlt du monstre
	v_temps_tour integer;    -- temps du tour
	i_temps_tour interval;   -- temps du tour en intervalle
	temp_niveau integer;     -- random pour passage niveau
	-------------------------------------------------------
	-- variables temporaires ou de calcul
	-------------------------------------------------------
	temp integer;            -- fourre tout
	temp_txt text;           -- texte temporaire
	-------------------------------------------------------
	-- variables pour cible
	-------------------------------------------------------
	pos_dest integer;        -- destination

begin
	doit_jouer := 0;
	code_retour := 'IA suivre<br>Monstre ' || trim(to_char(v_monstre, '999999999999')) || '<br>';
	/***********************************/
	/* Etape 1 : on récupère les infos */
	/* du monstre                      */
	/***********************************/
	temp_txt := calcul_dlt2(v_monstre);
	select into v_niveau,
		v_exp,
		v_pa,
		v_cible,
		actif,
		v_dlt,
		v_temps_tour
			limite_niveau(v_monstre),
			perso_px,
			perso_pa,
			perso_cible,
			perso_actif,
			perso_dlt,
			perso_temps_tour
	from perso
	where perso_cod = v_monstre;

	if actif != 'O' then
		return 'inactif !';
	end if;
	i_temps_tour := trim(to_char(v_temps_tour,'999999999999')) || ' minutes';
	if v_dlt + i_temps_tour - '10 minutes'::interval >= now() then
		doit_jouer := 1;
	end if;
	temp := lancer_des(1,100);
	if temp > 50 AND doit_jouer = 0 then
		code_retour := code_retour || 'Perso non joué.';
		return code_retour;
	end if;

	/***********************************/
	/* Etape 2 : on regarde si passage */
	/*  de niveau                      */
	/***********************************/
	-- on lance la procédure de passage de niveau
	if (v_exp >= v_niveau and v_pa >= getparm_n(8)) then
		temp_niveau := lancer_des(1, 6);
		temp_txt := f_passe_niveau(v_monstre, temp_niveau);
		select into v_pa perso_pa from perso where perso_cod = v_monstre;
		code_retour := code_retour || 'Passage niveau.<br>';
	end if;

	/**********************************************/
	/* Etape 3 : on execute l’instruction suivre  */
	/**********************************************/
	select into v_cible pia_parametre from perso_ia where pia_perso_cod = v_monstre;
	select into pos_dest ppos_pos_cod from perso_position where ppos_perso_cod = v_cible;
	code_retour := code_retour || ia_deplacement(v_monstre, pos_dest);

	/*************************************************/
	/* Etape 4 : tout semble fini                    */
	/*************************************************/
	return code_retour;
end;$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION public.ia_suivre(integer)
  OWNER TO delain;
COMMENT ON FUNCTION public.ia_suivre(integer) IS 'Fonction d’IA pour suivre une cible';
