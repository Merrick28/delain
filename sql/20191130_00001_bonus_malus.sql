ALTER TABLE public.bonus
   ALTER COLUMN bonus_tbonus_libc TYPE character varying(4);

ALTER TABLE public.bonus_type
   ADD COLUMN tbonus_cumulable character varying(1) DEFAULT 'N' NOT NULL;
COMMENT ON COLUMN public.bonus_type.tbonus_cumulable
  IS 'O/N';

ALTER TABLE public.bonus_type
   ADD COLUMN tbonus_degressivite integer;
COMMENT ON COLUMN public.bonus_type.tbonus_degressivite
  IS 'Pour les bonus/malus standards, il s''agit d''un coefficient de dégressivité par defaut. Pour les bonus de carac, c''est la limite de la carac en %';

UPDATE bonus_type SET tbonus_degressivite='25', tbonus_cumulable='O' WHERE tbonus_libc in ('FOR', 'CON', 'INT', 'DEX');


ALTER TABLE public.bonus
   ADD COLUMN bonus_mode character varying(1) DEFAULT 'S' NOT NULL;
COMMENT ON COLUMN public.bonus.bonus_mode
  IS 'Mode de fonctionnement du bonus => S: Standard, C: Cumulatif, E: Equipement';

ALTER TABLE public.bonus
   ADD COLUMN bonus_degressivite integer;
COMMENT ON COLUMN public.bonus.bonus_degressivite
  IS 'Pour les bonus/malus standards, il s''agit d''un coefficient de dégressivité.';

ALTER TABLE public.bonus
   ADD COLUMN bonus_obj_cod integer;
COMMENT ON COLUMN public.bonus.bonus_obj_cod
  IS 'Code de l''objet qui donne le bonus d''équipement';

ALTER TABLE public.bonus
   ADD COLUMN bonus_objbm_cod integer;
COMMENT ON COLUMN public.bonus.bonus_objbm_cod
  IS 'Code définissant le bonus d''équipement, le lien est nécéssaire modifier les bonus en cours en cas de modification du bonus source';

CREATE SEQUENCE public.seq_corig_cod
  INCREMENT 1
  MINVALUE 1
  START 1
  CACHE 1;
ALTER TABLE public.seq_corig_cod
  OWNER TO delain;
GRANT SELECT, UPDATE ON SEQUENCE public.seq_corig_cod TO delain;

ALTER TABLE public.carac_orig
   ADD COLUMN corig_cod  integer NOT NULL DEFAULT nextval(('seq_corig_cod'::text)::regclass); -- New PK


ALTER TABLE public.carac_orig DROP CONSTRAINT pk_corig;

ALTER TABLE public.carac_orig ADD CONSTRAINT pk_corig PRIMARY KEY (corig_cod);


ALTER TABLE public.carac_orig
   ADD COLUMN corig_mode character varying(1) DEFAULT 'S' NOT NULL;
COMMENT ON COLUMN public.carac_orig.corig_mode
  IS 'Mode de fonctionnement du bonus => S: Standard, C: Cumulatif, E: Equipement';


ALTER TABLE public.carac_orig
   ADD COLUMN corig_valeur numeric DEFAULT 0 NOT NULL;
COMMENT ON COLUMN public.carac_orig.corig_valeur
  IS 'C'' est la valeur de carac qui a été ajouté à la carac de base, cette valeur doit être retiré de la carac de base si le B/M disparait. Anciennement on utilisait corig_carac_valeur_orig, mais son utilisation ne permetttait pas la possibilité de plusieurs B/M';


UPDATE  public.carac_orig SET corig_valeur = perso_for - corig_carac_valeur_orig FROM perso WHERE perso.perso_cod = corig_perso_cod and corig_type_carac='FOR' ;
UPDATE  public.carac_orig SET corig_valeur = perso_int - corig_carac_valeur_orig FROM perso WHERE perso.perso_cod = corig_perso_cod and corig_type_carac='INT' ;
UPDATE  public.carac_orig SET corig_valeur = perso_dex - corig_carac_valeur_orig FROM perso WHERE perso.perso_cod = corig_perso_cod and corig_type_carac='DEX' ;
UPDATE  public.carac_orig SET corig_valeur = perso_con - corig_carac_valeur_orig FROM perso WHERE perso.perso_cod = corig_perso_cod and corig_type_carac='CON' ;

ALTER TABLE public.carac_orig
   ADD COLUMN corig_obj_cod integer DEFAULT NULL;
COMMENT ON COLUMN public.carac_orig.corig_obj_cod
  IS 'code objet de l''équipement qui donne ce bonus/malus';

ALTER TABLE public.carac_orig
   ADD COLUMN corig_objbm_cod integer DEFAULT NULL;
COMMENT ON COLUMN public.carac_orig.corig_objbm_cod
  IS 'Code définissant le bonus d''équipement, le lien est nécéssaire modifier les bonus en cours en cas de modification du bonus source';


ALTER TABLE public.defi_bmcaracs
   ADD COLUMN dbmc_mode character varying(1) DEFAULT 'S' NOT NULL;

COMMENT ON COLUMN public.objets_sorts.objsort_parent_cod IS 'C''est un lien vers le objsort_cod d''un ensorcellement d''objet générique qui est le pere de celui-ci, cette liaison est nécéssaire à cause du nombre d''utilisation du sort pour chque objet.';

CREATE SEQUENCE public.seq_objbm_cod
  INCREMENT 1
  MINVALUE 1
  START 1
  CACHE 1;
ALTER TABLE public.seq_objbm_cod
  OWNER TO delain;
GRANT SELECT, UPDATE ON SEQUENCE public.seq_objbm_cod TO delain;

CREATE TABLE public.objets_bm
(
  objbm_cod integer NOT NULL DEFAULT nextval('seq_objbm_cod'::regclass),
  objbm_gobj_cod integer, -- Le code de l'objet générique sur lequel est rattaché le bonus/malus
  objbm_obj_cod integer, -- Le code de l'obet sur lequel est rattaché le bonus/malus  (le bonus/malus peut être rattaché à un objet spécifique)
  objbm_tbonus_cod integer NOT NULL, -- Code du type de bonus/malus utilisé
  objbm_nom character varying(50), -- Le nom du bonus/malus (si null le nom sera celui du bonus/malus réel)
  objbm_bonus_valeur numeric, -- la valeur du bonus/malus
  CONSTRAINT objets_bm_pkey PRIMARY KEY (objbm_cod)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE public.objets_bm
  OWNER TO delain;
COMMENT ON TABLE public.objets_bm
  IS 'Permet d’associer un bonus/malus à un objet spécifique ou a un générique';
COMMENT ON COLUMN public.objets_bm.objbm_gobj_cod IS 'Le code de l''objet générique sur lequel est rattaché le bonus/malus';
COMMENT ON COLUMN public.objets_bm.objbm_obj_cod IS 'Le code de l''obet sur lequel est rattaché le bonus/malus  (le bonus/malus peut être rattaché à un objet spécifique)';
COMMENT ON COLUMN public.objets_bm.objbm_tbonus_cod IS 'Code du type de bonus/malus utilisé';
COMMENT ON COLUMN public.objets_bm.objbm_nom IS 'Le nom du bonus/malus (si null le nom sera celui du bonus/malus réel)';
COMMENT ON COLUMN public.objets_bm.objbm_bonus_valeur IS 'la valeur du bonus/malus';

