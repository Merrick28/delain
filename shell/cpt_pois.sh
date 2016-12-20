/usr/local/pgsql/bin/psql -q -t << EOF >> /home/sdewitte/logs/poissons.txt 2>&1
select to_char(now(),'DD/MM/YY hh24:mi:ss'),perso_cod,perso_nom,
	(select count(*) from perso_objets,objets
	where perobj_perso_cod = perso_cod
	and perobj_obj_cod = obj_cod
	and obj_gobj_cod = 226) as petit_pois,
	(select count(*) from perso_objets,objets
	where perobj_perso_cod = perso_cod
	and perobj_obj_cod = obj_cod
	and obj_gobj_cod = 227) as grand_pois
from perso
where perso_actif = 'O'
and perso_type_perso = 1;
EOF
