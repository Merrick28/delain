-- soyons fou, limite de 10 objets équipable pour les animation (moutball, course de monture, etc...)

update type_objet set tobj_equipable=1, tobj_max_equip=10 where tobj_libelle='Animation';
