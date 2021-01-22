

ALTER TABLE perso ADD COLUMN perso_misc_param json DEFAULT NULL;


INSERT INTO public.type_ia( ia_type, ia_nom, ia_fonction) VALUES ( 18, 'Monture de course', 'ia_monture_speed([perso])');

