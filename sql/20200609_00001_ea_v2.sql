
INSERT INTO public.bonus_type
            (tbonus_libc, tonbus_libelle, tbonus_gentil_positif,tbonus_nettoyable, tbonus_cumulable, tbonus_degressivite,tbonus_compteur)
    VALUES  ('C10', 'Fièvre', false, 'N', 'O', 100, 'O'),
            ('C11', 'Déchaînement', true, 'N', 'O', 100, 'O'),
            ('C12', 'Effervescence ', true, 'N', 'O', 100, 'O'),
            ('VU2', 'Aveuglement', false, 'O', 'O', 50, 'N') ,
            ('FO2', 'Fatigue', false, 'N', 'O', 100, 'N') ;

ALTER TABLE public.carac_orig
   ADD COLUMN corig_tbonus_libc character varying(4);

update carac_orig set corig_tbonus_libc=corig_type_carac where corig_tbonus_libc is null ;


ALTER TABLE public.defi_bmcaracs
   ADD COLUMN dbmc_tbonus_libc character varying(4);

update defi_bmcaracs set dbmc_tbonus_libc=dbmc_type_carac where dbmc_tbonus_libc is null ;

