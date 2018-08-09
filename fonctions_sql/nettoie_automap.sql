CREATE OR REPLACE FUNCTION public.nettoie_automap()
 RETURNS integer
 LANGUAGE plpgsql
AS $function$declare
	ligne record;
	nb_efface integer;
	v_query text;
	v_nb_query integer;
begin
	nb_efface := 0;
	for ligne in select tablename from pg_tables
		where tableowner = 'sdewitte'
		and tablename like 'perso_vue_pos_%' loop
		v_query = 'delete from '||ligne.tablename||' 
			where not exists
			(select 1 from perso where perso_cod = pvue_perso_cod);';
			execute v_query;
			get diagnostics v_nb_query = ROW_COUNT;
			nb_efface := nb_efface + v_nb_query;
	end loop;
	return nb_efface;
end;$function$

CREATE OR REPLACE FUNCTION public.nettoie_automap(integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$declare
	ligne record;
	nb_efface integer;
	v_query text;
	v_nb_query integer;
	personnage alias for $1;
begin
	nb_efface := 0;
	for ligne in select tablename from pg_tables
		where tableowner = 'sdewitte'
		and tablename like 'perso_vue_pos_%' loop
		v_query = 'delete from '||ligne.tablename||' 
			where pvue_perso_cod = '||personnage||';';
			execute v_query;
			get diagnostics v_nb_query = ROW_COUNT;
			nb_efface := nb_efface + v_nb_query;
	end loop;
	return nb_efface;
end;$function$

