
--
-- Name: tue_perso_rappel_familier(integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION tue_perso_rappel_familier(integer, integer) RETURNS text
    LANGUAGE plpgsql
    AS $_$/*************************************************************/
/* fonction tue_perso_rappel_familier                        */
/*   accomplit les actions de rattachement d'un familier     */
/*   à la mort de son maitre                                 */
/*   on passe en paramètres :                                */
/*   $1 = perso_cod perso (maitre)                           */
/*   $2 = perso_cod familier                                 */
/* on a en sortie une chaine séparée par ;                   */
/*   1 = text perte du familier                              */
/*************************************************************/
/* Créé le 02/05/2018  par Marlyza                           */
/* Modif xxxxxxx xx/xx/xxxx :                                */
/*************************************************************/
declare
	code_retour text;
	v_cible alias for $1;
	v_familier alias for $2;
-- Messages
	texte_evt text;             -- pour les events
	v_mes integer;              -- Numéro du message de coterie
	v_corps text;               -- Contenu du message
	v_titre text;
	v_imp_F integer;             -- durée impalpabilité d'un familier dans l'étage
  v_imp_P integer;             -- durée impalpabilité d'un personnage dans l'étage
  v_taux_xp integer;           -- taux de perte d'xps de l'étage
begin
  code_retour := 'Suite a votre décès votre familier vous rejoint, mais il est sonné par le choc.';

  -- recuperation des caratéristiques dépendant de l'etage
 select into v_imp_F, v_imp_P, v_taux_xp
   etage_duree_imp_f, etage_duree_imp_p, etage_perte_xp
   from perso
   inner join perso_position on ppos_perso_cod = perso_cod
   inner join positions on pos_cod = ppos_pos_cod
   inner join etage on etage_numero = pos_etage
   where perso_cod = v_cible ;

 	/**************************************************/
	/* le familier laisse tomber des objets au sol    */
	/* (obligatioire sinon si le maitre se sent       */
	/* en danger, il pourrait donner à son familier   */
	/* pour sauver son matos)                         */
	/**************************************************/
  v_corps := 'Suite a votre décès votre familier vous rejoint, mais dans la précipitation il a laissé tomber au sol : ';

  -- Gestion de la perte des objets
  v_corps := v_corps || tue_perso_perd_objets(v_familier, 0);

  v_mes := nextval('seq_msg_cod');
  v_titre := 'Perte d’équipement de votre familier';
  v_titre := substring(v_titre from 1 for 50);
  insert into messages (msg_cod, msg_date2, msg_date, msg_titre, msg_corps)
  values (v_mes, now(), now(), v_titre, v_corps);

  insert into messages_exp (emsg_msg_cod, emsg_perso_cod, emsg_archive)
  values (v_mes, v_familier, 'N');

  insert into messages_dest (dmsg_msg_cod, dmsg_perso_cod, dmsg_lu, dmsg_archive)
  values (v_mes, v_familier, 'N', 'N');

 	/**************************************************/
	/* le familier devient intangible                 */
	/**************************************************/
	update perso	set perso_tangible = 'N', perso_nb_tour_intangible = v_imp_F where perso_cod = v_familier;

  texte_evt := '[attaquant] a retrouvé son maitre [cible].';
  insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible)
    values(nextval('seq_levt_cod'), 54, now(), 1, v_cible, texte_evt, 'O', 'O', v_familier, v_cible);

  texte_evt := '[cible] a retrouvé son maitre [attaquant].';
  insert into ligne_evt(levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible)
    values(nextval('seq_levt_cod'), 54, now(), 1, v_familier, texte_evt, 'O', 'O', v_cible, v_familier);

  return code_retour;
end;$_$;


ALTER FUNCTION public.tue_perso_rappel_familier(integer, integer) OWNER TO delain;

--
-- Name: FUNCTION tue_perso_rappel_familier(integer, integer); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION tue_perso_rappel_familier(integer, integer) IS 'Fonction gérant le rattachement du familier à la mort de son maitre.';
