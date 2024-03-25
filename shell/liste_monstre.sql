select count(*) from (
  select perso_cod, 2 as priorite from perso
  where perso_type_perso = 2
  and perso_actif = 'O'
  and perso_tangible = 'O'
  and perso_der_connex + ((perso_temps_tour/2)::text || ' minutes')::interval < now()
  and (perso_dlt < now() or perso_pa >= 4)
  and (perso_dirige_admin != 'O' or perso_dirige_admin is null)
  and perso_cod not in (
	  		select perso_cod from perso,perso_position, positions
			where  pos_cod=ppos_pos_cod and perso_cod=ppos_perso_cod and pos_etage=162 and perso_type_perso=2 and perso_actif='O'
	   )
union
  select m.perso_cod, 1 as priorite from perso p join perso m on m.perso_cod=p.perso_monture
  where m.perso_type_perso = 2 and p.perso_type_perso = 1
  and m.perso_actif = 'O' and p.perso_actif = 'O'
  and m.perso_tangible = 'O' and p.perso_tangible = 'O'
  and (m.perso_dirige_admin != 'O' or m.perso_dirige_admin is null)
union
  select perso_cod, 2 as priorite from perso where perso_type_perso = 1
  and perso_actif = 'O'
  and (perso_dlt < now() or perso_pa >= 4)
  and perso_quete in ('quete_ratier.php','enchanteur.php','quete_chasseur.php','quete_dispensaire.php','quete_alchimiste.php','quete_groquik.php', 'quete_accompagnateur.php')
) t1 order by priorite, random()
