ALTER TABLE public.bonus_type
   ADD COLUMN tbonus_compteur character varying(1) NOT NULL DEFAULT 'N';


INSERT INTO public.bonus_type
            (tbonus_libc, tonbus_libelle, tbonus_gentil_positif,tbonus_nettoyable, tbonus_cumulable, tbonus_degressivite,tbonus_compteur)
    VALUES  ('C01', 'Exaltation', true, 'N', 'O', 100, 'O'),
            ('C02', 'Déchéance', false, 'N', 'O', 100, 'O'),
            ('C03', 'Flétrissure', false, 'N', 'O', 100, 'O'),
            ('C04', 'Véhémence', false, 'N', 'O', 100, 'O'),
            ('C05', 'Altération', false, 'N', 'O', 100, 'O'),
            ('C06', 'Dépravation', false, 'N', 'O', 100, 'O'),
            ('C07', 'Excitation', true, 'N', 'O', 100, 'O'),
            ('C08', 'Embrasement', true, 'N', 'O', 100, 'O'),
            ('COR', 'Corruption', false, 'O', 'N', 50, 'N'),
            ('IRR', 'Irradiance', false, 'O', 'N', 50, 'N') ;


ALTER TABLE public.fonction_specifique
   ADD COLUMN fonc_trigger_param json;
COMMENT ON COLUMN public.fonction_specifique.fonc_trigger_param
  IS 'Paramètre spécifique de déclenchement de l''EA (le format dépend du déclencheur)';
