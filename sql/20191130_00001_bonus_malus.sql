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

UPDATE bonus_type SET tbonus_degressipvite='25', tbonus_cumulable='O' WHERE tbonus_libc in ('FOR', 'CON', 'INT', 'DEX');


ALTER TABLE public.bonus
   ADD COLUMN bonus_mode character varying(1) DEFAULT 'S' NOT NULL;
COMMENT ON COLUMN public.bonus.bonus_mode
  IS 'Mode de fonctionnement du bonus => S: Standard, C: Cumulatif, E: Equipement';

ALTER TABLE public.bonus
   ADD COLUMN bonus_degressivite integer;
COMMENT ON COLUMN public.bonus.bonus_degressivite
  IS 'Pour les bonus/malus standards, il s''agit d''un coefficient de dégressivité.';

CREATE SEQUENCE public.seq_corig_cod
  INCREMENT 1
  MINVALUE 1
  START 1
  CACHE 1;
ALTER TABLE public.seq_corig_cod
  OWNER TO webdelain;
GRANT SELECT, UPDATE ON SEQUENCE public.seq_corig_cod TO webdelain;

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