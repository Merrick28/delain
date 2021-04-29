ALTER TABLE public.terrain
   ADD COLUMN ter_msg_inaccessible character varying(256);
COMMENT ON COLUMN public.terrain.ter_msg_inaccessible
  IS 'Message pour le joueur lors qu''il se retouve sur un terrain normalement inacessible (en général à la mort de sa monture).';


update terrain set ter_msg_inaccessible='[cible] s’enfonce dans la boue profonde qui le submerge et l’asphyxie.' where ter_nom='boue';
update terrain set ter_msg_inaccessible='[cible] s’ecorche à sang dans ses cailloux saillants.' where ter_nom='caillou';
update terrain set ter_msg_inaccessible='[cible] tombe en chute libre et perd connaissance.' where ter_nom='vent';
update terrain set ter_msg_inaccessible='Le froid engourdi les membres gelés de [cible].' where ter_nom='glace';
update terrain set ter_msg_inaccessible='Des plantes dangereuses se cachent dans l’herbe, en marchant dessus [cible] se retouve empoisonné.' where ter_nom='herbe';
update terrain set ter_msg_inaccessible='La forêt abrite de nombreux animaux parasites qui s’attaquent à [cible].' where ter_nom='foret';
update terrain set ter_msg_inaccessible='[cible] s’ecorche sur cette clotûre particulièrement dangeureuse.'  where ter_nom='clôture';
update terrain set ter_msg_inaccessible='L’eau est profonde, [cible] perd pied et s’y noit.' where ter_nom='eau';
update terrain set ter_msg_inaccessible='Les émanations qui s’échappent du marrais empoisonnent [cible].' where ter_nom='marais';
update terrain set ter_msg_inaccessible='[cible] meure de soif dans ce désert.' where ter_nom='désert';
update terrain set ter_nom='obscur', ter_desc='obscur' where ter_nom='obscur ';
update terrain set ter_msg_inaccessible='L’obscurité flétrie l’ame de [cible].' where ter_nom='obscur';
update terrain set ter_msg_inaccessible='[cible] s’écorche sur cette barrière particulièrement dangeureuse.' where ter_nom='barrière';
update terrain set ter_msg_inaccessible='Le feu inflige de lourdes brûlure à [cible].' where ter_nom='feu';
update terrain set ter_msg_inaccessible='Le terrain cache de nombreux pièges mortels, [cible] tombe dans l’un d’eux.' where ter_nom='terre';

insert into terrain (ter_cod, ter_nom, ter_desc, ter_msg_inaccessible) values
  (0, '~~~ sans terrain ~~~', 'Sans terrain', 'La zone cache de nombreux pièges mortels, [cible] tombe dans l’un d’eux.'),
  (-1, '~~~ tous les autres ~~~', 'Tous les autres terrains non définis', 'La zone cache de nombreux pièges mortels, [cible] tombe dans l’un d’eux.');


update public.type_ia set ia_nom='Monture docile', ia_fonction='ia_monture([perso], 0)' where ia_type=17 ;
update public.type_ia set ia_nom='Monture à ordre', ia_fonction='ia_monture([perso], 1)' where ia_type=18 ;
update public.type_ia set ia_nom='Monture mixte', ia_fonction='ia_monture([perso], 2)' where ia_type=19 ;
delete from public.type_ia where ia_type in (20, 21) ;

-- Compétence #104
INSERT INTO public.competences(  comp_typc_cod, comp_libelle, comp_modificateur, comp_connu) VALUES ( 2, 'Equitation', 0, 'N');

INSERT INTO perso_competences( pcomp_perso_cod, pcomp_pcomp_cod, pcomp_modificateur)
	SELECT perso_cod as pcomp_perso_cod, 104 as pcomp_pcomp_cod, 30 as pcomp_modificateur  FROM  perso WHERE perso_monture IS NOT null ;


insert into  type_evt (tevt_libelle, tevt_texte) VALUES
  ('Equitation', '[attaquant] monte sur sa monture [cible].'),
  ('Equitation', '[attaquant] est descendu de sa monture [cible].'),
  ('Equitation', '[attaquant] donne un ordre à sa monture [cible].') ,
  ('Equitation', '[attaquant] a été éjecté de sa monture [cible].') ,
  ('Equitation', '[cible] a été désarçonner de sa monture par [attaquant].') ;

INSERT INTO bonus_type( tbonus_libc, tonbus_libelle, tbonus_gentil_positif, tbonus_nettoyable, tbonus_cumulable, tbonus_degressivite, tbonus_description, tbonus_compteur)
    VALUES( 'EQI', 'Equitation', true,   'O', 'O', 50, 'Equitation: Chaque point positif d’Equitation augmente la compétence équitation d’autant.',  'N');

INSERT INTO  lieu_type(tlieu_libelle, tlieu_url) VALUES ('Portail à montures', 'portail_demoniaque.php');

ALTER TABLE rep_mon_joueur ADD COLUMN rjmon_monture numeric NOT NULL DEFAULT 0 ;

DROP FUNCTION choix_monstre_etage(integer) ;

CREATE TABLE perso_nb_action
(
  pnbact_perso_cod integer NOT NULL,
  pnbact_action character varying(24) NOT NULL,
  pnbact_nombre integer NOT NULL,
  pnbact_date_derniere_action timestamp with time zone,
  CONSTRAINT fk_pnbact_perso_cod FOREIGN KEY (pnbact_perso_cod) REFERENCES public.perso (perso_cod) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT uniq_pnbact_perso_cod_action UNIQUE (pnbact_perso_cod, pnbact_action)
);

ALTER TABLE public.perso_nb_action OWNER TO delain;



--
-- Name: type_vue7; Type: TYPE; Schema: public; Owner: delain
--
CREATE TYPE public.type_vue7 AS (
	tvue_num integer,
	t_pos_cod integer,
	t_x integer,
	t_y integer,
	t_nb_perso integer,
	t_nb_monstre integer,
	t_type_aff integer,
	t_nb_obj integer,
	t_or integer,
	t_dist integer,
	t_type_mur integer,
	t_type_case integer,
	t_type_bat integer,
	t_decor integer,
	t_traj integer,
	t_decor_dessus integer,
	t_nb_lock integer,
	t_pos_ter_cod integer,
);


ALTER TYPE public.type_vue7 OWNER TO delain;

--
-- Name: TYPE type_vue2; Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON TYPE public.type_vue7 IS 'Type pour faciliter le traitement JS des données de la vue';