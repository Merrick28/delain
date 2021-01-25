

UPDATE type_ia SET ia_nom='Monture de course', ia_fonction='ia_monture([perso], 2)' where ia_type = 18 ;

UPDATE type_ia SET ia_nom='Monture docile', ia_fonction='ia_monture([perso], 1)' where ia_type = 17 ;

INSERT INTO type_ia( ia_type, ia_nom, ia_fonction) VALUES ( 19, 'Monture indomptée', 'ia_monture([perso], 0)');

INSERT INTO type_ia( ia_type, ia_nom, ia_fonction) VALUES ( 20, 'Monture bourrique', 'ia_monture([perso], -1)');

INSERT INTO type_ia( ia_type, ia_nom, ia_fonction) VALUES ( 21, 'Monture bornée', 'ia_monture([perso], -2)');


DROP FUNCTION ia_monture_speed(integer) ;