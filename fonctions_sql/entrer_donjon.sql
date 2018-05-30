
--
-- Name: entrer_donjon(integer, integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION entrer_donjon(integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
    AS $_$/* fonction entrer_donjon                                    */
/*                                                           */
/*   on passe en paramètres :                                */
/*   $1 = perso_cod                                          */
/*   $2 = etage_numero                                       */
/*   $3 = pos_cod                                            */
/*************************************************************/
/* Créé le 10/02/2015                                        */
/*	   par Kahlann                                       */
/*************************************************************/

/*************************************************************/
/* Modifié le XX/XX/XXXX par                                 */
/*************************************************************/

declare

	v_perso alias for $1;
	v_etage alias for $2;
	v_pos_depart alias for $3;
	v_nombre_entree integer;
	des_entree integer;
	v_pa integer;
	v_new_pos integer;
	v_tangible text;
        v_type_perso integer;
        v_level_max integer;
        v_level_perso integer;
        v_fam_actif integer;
	v_familier integer;
        texte_evt text;
        texte_fuite text;

        imp_actif integer; -- 1 = Impalpable ON
        imp_nbtour integer; -- Nombre de tour impalpable

begin

-- Paramètres pour l animation
imp_actif := 0;
imp_nbtour := 100;


/**************************************************/
/* on vérifie les pa et l impalpabilité           */
/**************************************************/

select into v_pa, v_tangible, v_type_perso perso_pa, perso_tangible, perso_type_perso from perso
where perso_cod = v_perso;

if v_pa < 4 then
	return '1;Vous n''avez pas assez de PA pour rentrer dans le donjon.';
end if;

if v_tangible = 'N' then
	return '1;Vous ne pouvez pas entrer dans le donjon en étant impalpable.';
end if;

if v_type_perso = 3 then
        return '1;Un familier ne peut pas se déplacer seul.';
end if;

-- on vérifie que le perso peut entrer dans le donjon en fonction de son niveau
select into v_level_max carene_level_max from carac_arene
where carene_etage_numero = v_etage;

if v_level_max > 0 then
   select into v_level_perso perso_niveau from perso
   where perso_cod = v_perso;

   if v_level_perso > v_level_max then
        return '1;Vous ne pouvez pas entrer dans le donjon car votre niveau est trop élevé.';
   end if;

end if;

texte_fuite := '';
if exists (select 1 from lock_combat where lock_cible = v_perso) then
    texte_fuite := fuite(v_perso);
    if split_part(texte_fuite, '#', 1) = '1' then
        -- Échec.
        return '1;' || split_part(texte_fuite, '#', 2);
    else
        texte_fuite := split_part(texte_fuite, '#', 2);
    end if;
end if;

/**************************************************/
/* On calcule la position d entrée dans le donjon */
/**************************************************/

select into v_new_pos pos_cod from positions
where pos_etage = v_etage and pos_entree_arene = 'O';



-- déplacement du joueur
update perso_position set ppos_pos_cod = v_new_pos where ppos_perso_cod = v_perso;

/***************************************************/
/* Gestion du familier                             */
/***************************************************/
-- récupération de l'étage d'arrivée
select into v_etage pos_etage from positions
where pos_cod = v_new_pos;
-- récupération du paramètre étage familier (actif ou non)
select into v_fam_actif etage_familier_actif from etage
where etage_cod = v_etage;

-- si familier inactif alors on le passe intangible
if v_fam_actif = 0 then
     select into v_familier max(pfam_familier_cod) from perso_familier INNER JOIN perso ON perso_cod=pfam_familier_cod WHERE perso_actif='O'
     and pfam_perso_cod = v_perso;

     if found and v_familier != getparm_n(111) then
          update perso set perso_tangible = 'N', perso_nb_tour_intangible = 9999
	  where perso_cod = v_familier;
     end if;
end if;

/**************************************************/
/* on insère les données du perso dans perso_arene*/
/**************************************************/

insert into perso_arene (parene_perso_cod, parene_etage_numero, parene_pos_cod, parene_date_entree, pdonjon_pos_cod_sortie)
values (v_perso, v_etage, v_new_pos, now(), v_pos_depart);

texte_evt := 'Entrée en donjon';

insert into ligne_evt (levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible)
 values (nextval('seq_levt_cod'), 86, now(), 1 , v_perso, texte_evt, 'O', 'O', null, null);

update perso set perso_pa = v_pa - 4 where perso_cod = v_perso;

return '1;Entrée en donjon effectuee';

end;$_$;


ALTER FUNCTION public.entrer_donjon(integer, integer, integer) OWNER TO delain;

--
-- Name: FUNCTION entrer_donjon(integer, integer, integer); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION entrer_donjon(integer, integer, integer) IS 'Gère l entrée dans un donjon';
