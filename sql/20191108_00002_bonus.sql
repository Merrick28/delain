

INSERT INTO public.bonus_type(tbonus_libc, tonbus_libelle, tbonus_gentil_positif,  tbonus_nettoyable) VALUES
('FOR', 'Force', true, 'N'),
('INT', 'Intelligence', true, 'N'),
('DEX', 'Dexterité', true, 'N'),
('CON', 'Constitution', true, 'N');



INSERT INTO quetes.aquete_type_carac(aqtypecarac_cod, aqtypecarac_nom, aqtypecarac_type) VALUES
  (25, 'A terminé l''étape de QA', 'QUETE');