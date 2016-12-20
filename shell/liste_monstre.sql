select * from (select perso_cod from perso
where perso_type_perso = 2
and perso_actif = 'O'
and perso_tangible = 'O'
and perso_der_connex + ((perso_temps_tour/2)::text || ' minutes')::interval < now()
and (perso_dlt < now() or perso_pa >= 4)
and (perso_dirige_admin != 'O' or perso_dirige_admin is null)
order by random()) t1
union all (select perso_cod from perso where perso_type_perso = 1
and perso_actif = 'O'
and (perso_dlt < now() or perso_pa >= 4)
and perso_quete in ('quete_ratier.php','enchanteur.php','quete_chasseur.php','quete_dispensaire.php','quete_alchimiste.php','quete_groquik.php', 'quete_accompagnateur.php'))
