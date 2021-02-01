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
/* Modifié le 13/02/2018 par Marlyza (c) LAG                 */
/*************************************************************/

declare
	
	v_perso alias for $1;
	v_position integer;
	v_registre_position integer;  -- ancienne inscription eventuelle
  texte_evt text;

begin

/**************************************************/
/* On purge les vieilles inscriptions             */
/**************************************************/
delete from perso_registre where preg_date_inscription<=now()-'1 month'::interval;

/**************************************************/
/* On récupère la position du personnage          */
/**************************************************/
select into v_position, v_registre_position ppos_pos_cod, preg_pos_cod from perso_position
left join perso_registre on preg_perso_cod=ppos_perso_cod
where  ppos_perso_cod = v_perso;

/**************************************************/
/* on met à jour les données du perso dans perso_registre*/
/**************************************************/
if v_registre_position is not null then
  -- le perso s'était déja inscrit, on positionne une nouvelle inscription
    texte_evt := '[perso_cod1] a réinscrit son nom dans les registres des souterrains, au cas où.';
else
  -- première inscription
  texte_evt := '[perso_cod1] a inscrit son nom dans les registres des souterrains.';
end if;

insert into ligne_evt (levt_cod, levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible)
 values (nextval('seq_levt_cod'), 86, now(), 1 , v_perso, texte_evt, 'O', 'O', null, null);

if v_registre_position is not null then
  -- le perso s'était déja inscrit, on positionne une nouvelle inscription
  update perso_registre set preg_pos_cod = v_position, preg_date_inscription=now() where preg_perso_cod = v_perso;
else
  -- première inscription
  insert into perso_registre (preg_perso_cod, preg_pos_cod, preg_date_inscription) values(v_perso,v_position, now()) ;
end if;

return '1;Enregistrement du point de passage en donjon effectué.';

end;$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION public.enregistre_avancee_donjon(integer)
  OWNER TO delain;
COMMENT ON FUNCTION public.enregistre_avancee_donjon(integer) IS 'Enregistre la position du joueur comme point de retour du batiment admin en cas de mort en arene';
