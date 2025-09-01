
--
-- Name: rafle_objets(integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION public.rafle_objets(integer) RETURNS text
    LANGUAGE plpgsql
    AS $_$/*****************************************************************/
/* function rafle_objets : rafle des objets posée au sol et met  */
/*          dans l’inventaire du perso                           */
/* On passe en paramètres                                        */
/*    $1 = perso_cod                                             */
/* Le code sortie est un entier                                  */
/*    0 = tout s est bien passé                                  */
/*    1 = le perso et l’arme ne sont pas sur la même position    */
/*    2 = le perso n a pas assez de pa                           */
/*    3 = perso non trouvé                                       */
/*    4 = obet non trouvée                                       */
/*    5 = position perso non trouvée                             */
/*    6 = position arme non trouvée                              */
/*****************************************************************/
/* Créé le 11/03/2003                                            */
/* Liste des modifications :                                     */
/*  18/12/2003 : Retour en texte pour intégrer les mimiques      */
/*  26/06/2012 : Ajout du nom de l’objet dans les événements     */
/*  26/06/2012 : Rationalisation des requêtes                    */
/*****************************************************************/
declare
code_retour text;
	personnage alias for $1;
   num_objet integer;
	pos_perso positions.pos_cod%type;
	nom_objet objets.obj_nom_generique%type;
	texte_evt text;
	tobjet type_objet.tobj_libelle%type;
   objet_position_cod objet_position.pobj_cod%type;
	gobj integer;
	code_monstre integer;
	code_ia text;
	cout_pa integer;
	v_poids_max integer;
	v_poids_actu numeric;
	v_poids_objet numeric;
	v_temp integer;
    ligne record;                -- Une ligne d’enregistrements
    nb_objet integer;		 -- nb d'objet a ramasser
begin

    code_retour := '';

    /**************************************/
	/* Etape 1                            */
	/* vérifier raflage est possible      */
	/**************************************/
    select perso_enc_max, ppos_pos_cod, get_poids(perso_cod) into v_poids_max, pos_perso, v_poids_actu
    from perso join perso_position on ppos_perso_cod=perso_cod
    where perso_cod=personnage
      and perso_tangible = 'O'
      and perso_pa >= 6
      and	coalesce(f_to_numeric( ((perso_misc_param->>'kill_perte_objet')::jsonb)->>'kill_pos_cod'),0)=ppos_pos_cod
      and	coalesce( ((perso_misc_param->>'kill_perte_objet')::jsonb)->>'kill_date' , '1970-01-01 00:00:00')::timestamp > NOW() - '20 DAYS'::interval ;
    if not found then
        code_retour := '<p>Anomalie : Il n''est pas ou plus possible de rafler ici !</p>';
        return code_retour;
    end if;


	/**************************************/
	/* Etape 2                            */
	/* Vérification objet                 */
	/**************************************/

	-- Interdit de ramasser pendant un défi
	if exists(select 1 from defi where defi_statut = 1 and personnage in (defi_lanceur_cod, defi_cible_cod)
		UNION ALL select 1 from defi
			inner join perso_familier on pfam_perso_cod in (defi_lanceur_cod, defi_cible_cod)
			where defi_statut = 1 and pfam_familier_cod = personnage)
	then
		code_retour := '<p>Anomalie : il est interdit de rafler des objets pendant un défi !</p>';
        return code_retour;
    end if;

     -- on enlève les pa au perso (on paye avant de ramasser)
     update perso set perso_pa = perso_pa - 6 where perso_cod = personnage;

	/**************************************/
	/* Etape 3                            */
	/* boucle sur les objets à rafler     */
	/**************************************/

    nb_objet := f_lit_des_roliste('20D4+15');

    -- 859: glyphe on ne ramasse pas
    -- 84/85: mimique on en ramasse pas (ne pouvait pas être dans l'inventaire)
    -- on ne rammasse que les objets identifiés => finalement dev pas possible
    -- les objst ne reste identifiés que qq DLT, quand on ramasse réelement , il n'y a plus rien d'ideitifié
    for ligne in (select obj_cod, obj_nom, obj_gobj_cod, obj_poids, pobj_cod, tobj_libelle
                    from objets
                      inner join objet_position on pobj_obj_cod = obj_cod
                      inner join objet_generique on gobj_cod = obj_gobj_cod
                      inner join type_objet on tobj_cod=gobj_tobj_cod
                      -- left join perso_identifie_objet on pio_perso_cod = personnage and pio_obj_cod = obj_cod
                      where pobj_pos_cod=pos_perso  and obj_gobj_cod not in (859, 84, 85) -- and pio_cod is null
                      order by random() limit nb_objet )
      loop

            num_objet := ligne.obj_cod ;
            nom_objet := ligne.obj_nom ;
            gobj := ligne.obj_gobj_cod ;
            v_poids_objet := ligne.obj_poids ;
            objet_position_cod := ligne.pobj_cod ;
            tobjet := ligne.tobj_libelle ;

            -- on regarde le poids
            if ((v_poids_actu + v_poids_objet) > (v_poids_max * 3))	then
                v_poids_max := v_poids_max * 3;
                code_retour := code_retour || '<p>Vous ne pouvez ramasser un objet qui vous fait dépasser '||trim(to_char(v_poids_max,'99999999'))||' d’encombrement.</p>';
                return code_retour;
            end if;
            v_poids_actu := v_poids_actu + v_poids_objet ; -- mise à jour du poids car on va ramasser cet objet

            /********************************/
            /* Etape 6                      */
            /* on valide les changements    */
            /********************************/
            -- 6.1 : on supprime le objet_position
            delete from objet_position where pobj_cod = objet_position_cod;

            -- 6.3 : on rajoute l’objet dans l’inventaire du perso
            insert into perso_objets (perobj_cod, perobj_perso_cod, perobj_obj_cod, perobj_identifie, perobj_equipe)
                    values (nextval('seq_perobj_cod'), personnage, num_objet, 'O', 'N');

            -- 6.5 : on rajoute un événement
            texte_evt := '[perso_cod1] a ramassé un objet « ' || nom_objet || ' » (' || tobjet || ' ' || to_char(num_objet, '99999999999') || ')';
            insert into ligne_evt (levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible)
                    values (nextval('seq_levt_cod'), 3, now(), 1, personnage, texte_evt, 'O', 'O');

            code_retour := code_retour || '<p>L’objet « ' || nom_objet || ' » a été ramassé. Il est maintenant dans votre inventaire.</p>';

    end loop;

    return code_retour;
end;$_$;


ALTER FUNCTION public.rafle_objets(integer) OWNER TO delain;

--
-- Name: FUNCTION rafle_objets(integer); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION public.rafle_objets(integer) IS 'Gère le raflage d’objet par un perso.';