/usr/local/pgsql/bin/psql -q -t << EOF > /home/sdewitte/logs/feves.txt 2>&1
select perso_cod,perso_nom,count(perobj_cod)
from perso,perso_objets,objets
where perso_type_perso = 1
and perso_actif = 'O'
and perobj_perso_cod = perso_cod
and perobj_obj_cod = obj_cod
and obj_gobj_cod = 89
group by perso_cod,perso_nom
order by count(perobj_cod) desc;
EOF
