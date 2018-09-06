-- Function: public.choix_cible_aleatoire(integer, integer)

-- DROP FUNCTION public.choix_cible_aleatoire(integer, integer);

CREATE OR REPLACE FUNCTION public.choix_cible_aleatoire(
    integer,
    integer)
  RETURNS integer AS
$BODY$/**********************************************************/
/* fonction choix_cible_aleatoire : pour un aventurier    */
/*   choisir une cible dans sa vue                        */
/* on passe en paramètres :                               */
/*   $1 = perso_cod du joueur                             */
/*   $2 = type de cible 0=tout type                       */
/*			1 pour perso/fam                  */
/*			2 pour monstre                    */
/**********************************************************/
/* on a en sortie le perso_cod de la cible                */
/**********************************************************/
/* créé le 06/09/2018 - Marlyza                           */
/**********************************************************/
declare
	code_retour integer;
	v_perso alias for $1;
	v_type_perso alias for $2;
--
    v_compt_cod integer;
    v_pos_etage integer;
    v_pos_x integer;
    v_pos_y integer;
    v_portee integer;
begin
	-- principes de base :
	-- un perso peut être ammené à choisir aléatoirement une cible
	-- s'il est sous effet de la désorientation, et voir plus tard pour les besoins d'une quête.

	code_retour :=0; -- si on trouve rien

	select into
	    v_compt_cod,
	    v_pos_etage,
	    v_pos_x,
	    v_pos_y,
	    v_portee

	    pcompt_compt_cod,
	    pos_etage,
	    pos_x,
	    pos_y,
	    portee_attaque(perso_cod)
	  from perso, perso_position, positions, perso_compte
	  where perso_cod = v_perso
		and ppos_perso_cod = perso_cod
		and ppos_pos_cod = pos_cod
		and pcompt_perso_cod = perso_cod;

        if found then
		select into code_retour perso_cod
		from perso,positions,perso_position
		where pos_x between (v_pos_x-v_portee) and (v_pos_x+v_portee)
		and pos_y between (v_pos_y-v_portee) and (v_pos_y+v_portee)
		and pos_cod = ppos_pos_cod
		and pos_etage = v_pos_etage
		and ppos_perso_cod = perso_cod
		and perso_cod != v_perso
		and perso_actif = 'O'
		and perso_tangible = 'O'
		and ((v_type_perso=0)
			OR
		    (v_type_perso=1 and perso_type_perso in (1,3))
		   	OR
		    (v_type_perso=2 and perso_type_perso=2)
		    )
		and not exists
			(select 1 from lieu,lieu_position where lpos_pos_cod = ppos_pos_cod and lpos_lieu_cod = lieu_cod and lieu_refuge = 'O')
		and not exists
			(select 1 from perso_compte where pcompt_compt_cod = v_compt_cod and pcompt_perso_cod = perso_cod)
		and perso_cod not in (
				(select pfam_familier_cod from perso_compte join perso_familier on pfam_perso_cod=pcompt_perso_cod join perso on perso_cod=pfam_familier_cod  where pcompt_compt_cod = v_compt_cod and perso_actif='O')
			union
				(select pcompt_perso_cod from perso_compte,compte_sitting where pcompt_compt_cod = csit_compte_sitteur and csit_compte_sitteur = v_compt_cod and csit_dfin > now() and csit_ddeb < now())
			union
				(select pcompt_perso_cod from perso_compte,compte_sitting where pcompt_compt_cod = csit_compte_sitte and csit_compte_sitteur = v_compt_cod and csit_dfin > now() and csit_ddeb < now())
			union
				(select pfam_familier_cod from perso_compte,compte_sitting,perso_familier where pcompt_compt_cod = csit_compte_sitte and csit_compte_sitteur = v_compt_cod and csit_dfin > now() and csit_ddeb < now() and pfam_perso_cod = pcompt_perso_cod)
			union
				(select pfam_familier_cod from perso_compte,compte_sitting,perso_familier where pcompt_compt_cod = csit_compte_sitteur and csit_compte_sitteur = v_compt_cod and csit_dfin > now() and csit_ddeb < now() and pfam_perso_cod = pcompt_perso_cod))
		order by random() limit 1 ;
		        if not found then
		          code_retour := 0;
		        end if;
        end if;

	return code_retour;
end;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION public.choix_cible_aleatoire(integer, integer)
  OWNER TO delain;
GRANT EXECUTE ON FUNCTION public.choix_cible_aleatoire(integer, integer) TO webdelain;
