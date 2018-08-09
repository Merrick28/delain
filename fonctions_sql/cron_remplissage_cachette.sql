CREATE OR REPLACE FUNCTION public.cron_remplissage_cachette()
 RETURNS void
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function cron_remplissage_cachette : gère le remplissage des  */
/*											cachettes avec des objets dans des listes*/
/*                                                               */
/*****************************************************************/
/* Créé le 19/02/2008                                            */
/*****************************************************************/
declare
		ligne record;
		objet_select integer;
		num integer;

begin
	for ligne in select cache_cod,cache_max_respawn,cache_chance_respawn,cache_liste_respawn from cachettes 
	where cache_max_respawn > (select count(objcache_obj_cod) as nombre from cachettes_objets where objcache_cod_cache_cod = cache_cod) loop
		if lancer_des(1,100) <= ligne.cache_chance_respawn then
			select into objet_select,num creappro_gobj_cod,lancer_des(1,1000) from cachette_reappro 
							where ligne.cache_liste_respawn = creappro_cache_liste_respawn 
							order by random() limit 1;
			perform cree_objet_cachette(objet_select,ligne.cache_cod,1);
		end if;
	end loop;
end;$function$

