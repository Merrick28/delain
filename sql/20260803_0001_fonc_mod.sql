ALTER TABLE public.fonction_specifique
    ADD COLUMN fonc_mode character varying(20) DEFAULT NULL;

UPDATE fonction_specifique enfant
    SET fonc_mode = 'ea_implantation'
        FROM fonction_specifique parent
            WHERE parent.fonc_nom = 'ea_implantation_ea'
              AND parent.fonc_effet = enfant.fonc_cod::text
              AND enfant.fonc_mode IS NULL;