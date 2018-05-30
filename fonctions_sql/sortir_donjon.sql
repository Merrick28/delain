--
-- Name: sortir_donjon(integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION sortir_donjon(integer) RETURNS text
    LANGUAGE plpgsql
    AS $_$/*************************************************************/
/* fonction sortir_donjon                                    */
/*                                                           */
/*   on passe en paramètres :                                */
/*   $1 = perso_cod                                          */
/*************************************************************/
/* Créé le 11/02/2015                                        */
/*	   par Kahlann                                       */
/*************************************************************/

/*************************************************************/
/* Modifié le 17/12/2010 par Maverick                        */
/*************************************************************/

declare

	v_perso alias for $1;
	v_pa integer;
	v_pos_retour integer;
	v_type_perso integer;
        v_familier integer;

begin


-- on vérifie les pa

select into v_pa, v_type_perso perso_pa, perso_type_perso from perso
where perso_cod = v_perso;

if v_pa < 4 then
	return '1;Vous n''avez pas assez de PA pour sortir de l''arène.';
end if;

if v_type_perso = 3 then
        return '1;Un familier ne peut pas se déplacer seul';
end if;

--on récupère les données du perso dans perso_arene

select into v_pos_retour pdonjon_pos_cod_sortie from perso_arene
where parene_perso_cod = v_perso;

--on effectue le déplacement retour

update perso_position
set ppos_pos_cod = v_pos_retour
where ppos_perso_cod = v_perso;

-- on efface la trace du perso dans l'arène

delete from perso_arene
where parene_perso_cod = v_perso;


-- on enlève les PA
update perso
set perso_pa = perso_pa - 4
where perso_cod = v_perso;

-- on remet l eventuel familier palpable

select into v_familier max(pfam_familier_cod) from perso_familier
where pfam_perso_cod = v_perso;

if found then
     	update perso set perso_tangible = 'O', perso_nb_tour_intangible = 0
	where perso_cod = v_familier;
end if;

-- on ajoute un event

	insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
		values(nextval('seq_levt_cod'),87,now(),1,v_perso,E'Sortie de donjon','O','O',null,null);

return '0;Vous sortez vivant de ce donjon';

end;$_$;


ALTER FUNCTION public.sortir_donjon(integer) OWNER TO delain;

--
-- Name: FUNCTION sortir_donjon(integer); Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON FUNCTION sortir_donjon(integer) IS 'Sortie d''un donjon';
