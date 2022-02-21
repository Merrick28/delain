-- supression des pochettes existante
select f_del_objet(obj_cod) as nombre from objets where obj_gobj_cod = 642 ;

-- nouvelle distrib
select cree_pochette_surprise() as resultat ;