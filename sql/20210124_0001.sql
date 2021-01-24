
UPDATE type_ia SET ia_nom='Monture docile', ia_fonction='ia_monture([perso], 0)' where ia_type = 17 ;

UPDATE type_ia SET ia_nom='Monture de course', ia_fonction='ia_monture([perso], 1)' where ia_type = 18 ;

INSERT INTO type_ia( ia_type, ia_nom, ia_fonction) VALUES ( 19, 'Monture bourriqe', 'ia_monture([perso], -2)');

INSERT INTO type_ia( ia_type, ia_nom, ia_fonction) VALUES ( 20, 'Monture indomptée', 'ia_monture([perso], -1)');

DROP FUNCTION ia_monture_speed(integer) ;