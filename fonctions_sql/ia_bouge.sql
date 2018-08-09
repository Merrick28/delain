CREATE OR REPLACE FUNCTION public.ia_bouge(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************/
/* fonction ia_pacifique                             */
/*    reçoit en arguments :                          */
/* $1 = perso_cod du monstre                         */
/*    retourne en sortie en entier non lu            */
/*    les évènements importants seront mis dans la   */
/*    table des evenemts admins                      */
/* Cette fonction effectue les actions automatiques  */
/*  des monstres pour éviter que le MJ qui a autre   */
/*  à faire jouer tout à la mimine....               */
/*****************************************************/
/* 25/01/2004 : changement pour adaptation à la      */
/*  nouvelle fonction attaque(int4,int4)             */
/* 20/02/2004 : ajout des sorts aggressifs           */
/* 05/07/2004 : IA non obligatoirement jouée         */
/*****************************************************/
declare
-------------------------------------------------------
-- variables E/S
-------------------------------------------------------
	code_retour text;				-- code sortie
-------------------------------------------------------
-- variables de renseignements du monstre
-------------------------------------------------------
	v_monstre alias for $1;		-- perso_cod du monstre
	v_niveau integer;				-- niveau du monstre
	v_exp numeric;					-- xp du monstre
	v_pa integer;					-- pa du monstre
	v_vue integer;					-- distance de vue
	v_cible integer;				-- cible
	temp_cible integer;			-- cible temporaire
	v_etage integer;				-- etage	
	pos_actuelle integer;		-- position
	v_x integer;					-- X
	v_y integer;					-- Y
	actif varchar(2);				-- actif ?
	v_int integer;					-- intelligence du monstre
	v_pv integer;					-- pv du monstre
	v_pv_max integer;				-- pv_max du monstre
	doit_jouer integer;			-- 0 pour non, 1 pour oui
	v_dlt timestamp;				-- dlt du monstre
	v_temps_tour integer;		-- temps du tour
	i_temps_tour interval;		-- temps du tour en intervalle
	temp_niveau integer;			-- random pour passage niveau
-------------------------------------------------------
-- variables temporaires ou de calcul
-------------------------------------------------------
	temp integer;					-- fourre tout
	temp_txt text;					-- texte temporaire
	compt_loop integer;			-- comptage de boucle pour sortie
	dep_aleatoire integer;		-- variable de calcul de dep aleatoire
-------------------------------------------------------
-- variables pour cible
-------------------------------------------------------
	pos_dest integer;				-- destination

begin
	doit_jouer := 0;
	code_retour := 'IA bouge<br>Perso '||trim(to_char(v_monstre,'999999999999'))||'<br>';
/***********************************/
/* Etape 1 : on récupère les infos */
/* du monstre                      */
/***********************************/
	temp_txt := calcul_dlt2(v_monstre);
	select into 	v_niveau,
						v_exp,
						v_pa,
						v_vue,
						pos_actuelle,
						v_cible,
						v_etage,
						actif,
						v_x,
						v_y,
						v_int,
						v_pv,
						v_pv_max,
						v_dlt,
						v_temps_tour
					limite_niveau(v_monstre),
					perso_px,
					perso_pa,
					distance_vue(v_monstre),
					ppos_pos_cod,
					perso_cible,
					pos_etage,
					perso_actif,
					pos_x,
					pos_y,
					perso_int,
					perso_pv,
					perso_pv_max,
					perso_dlt,
					perso_temps_tour
		from perso,perso_position,positions
		where perso_cod = v_monstre
		and ppos_perso_cod = v_monstre
		and ppos_pos_cod = pos_cod;
	if actif != 'O' then
		return 'inactif !';
	end if;
	i_temps_tour := trim(to_char(v_temps_tour,'99999999999'))||' minutes';
	if v_dlt + i_temps_tour - '10 minutes'::interval >= now() then
		doit_jouer := 1;
	end if;
	temp := lancer_des(1,100);
	if temp > 50 then
		if doit_jouer = 0 then
			code_retour := code_retour||'Perso non joué.';
			
			return code_retour;
		end if;
	end if;
	
			compt_loop := 0;
			while (v_pa >= getparm_n(9)) loop
				compt_loop := compt_loop + 1;
				exit when compt_loop >= 5;
				dep_aleatoire := f_deplace_aleatoire(v_monstre,pos_actuelle);
				select into pos_actuelle ppos_pos_cod
					from perso_position
					where ppos_perso_cod = v_monstre;
				select into v_pa perso_pa from perso
					where perso_cod = v_monstre;
			end loop;
		code_retour := code_retour||'déplacement aléatoire.<br>';
		return code_retour;
	return code_retour;
end;$function$

