-- Function: public.enregistre_avancee_donjon(integer)

-- DROP FUNCTION public.enregistre_avancee_donjon(integer);

CREATE OR REPLACE FUNCTION public.enregistre_avancee_donjon(integer)
  RETURNS text AS
$BODY$/* fonction enregistre_avancee_donjon                                    */
/*                                                           */
/*   on passe en paramètres :                                */
/*   $1 = perso_cod                                          */
/*************************************************************/
/* Créé le 12/02/2015                                        */
/*	   par Kahlann                                       */
/*************************************************************/

/*************************************************************/
/* Modifié le XX/XX/XXXX par                                 */
/*************************************************************/

declare
	
	v_perso alias for $1;
	v_position integer;
        texte_evt text;

begin

/**************************************************/
/* On récupère la position du personnage          */
/**************************************************/

select into v_position ppos_pos_cod from perso_position
where  ppos_perso_cod = v_perso;

/**************************************************/
/* on met à jour les données du perso dans perso_arene*/
/**************************************************/
texte_evt := 'Enregistrement du point de passage en donjon';

insert into ligne_evt (levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible)
 values (nextval('seq_levt_cod'), 86, now(), 1 , v_perso, texte_evt, 'O', 'O', null, null);

update perso_arene set parene_pos_cod = v_position where parene_perso_cod = v_perso;

return '1;Enregistrement du point de passage en donjon effectué.';

end;$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION public.enregistre_avancee_donjon(integer)
  OWNER TO delain;
COMMENT ON FUNCTION public.enregistre_avancee_donjon(integer) IS 'Enregistre la position du joueur comme point de retour en cas de mort en donjon';
