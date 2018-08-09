CREATE OR REPLACE FUNCTION public.test()
 RETURNS text
 LANGUAGE plpgsql
AS $function$declare
	v_texte text;
	code_retour text;
	v_temp text;
begin
	v_texte := e'ab';
        v_texte := v_texte||e'a\	b';
        return v_texte;
end;$function$

CREATE OR REPLACE FUNCTION public.test(integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$declare
type_depl alias for $1;
	monstre_cod integer;     -- le perso_cod du monstre
	v_vue integer;           -- la limite de vue du monstre
	v_tobj_cod integer;      -- le type d’objets à ramasser
	v_inventaire_max integer;-- le nombre max à porter en inventaire
	v_ramassage_max integer; -- le nombre max à ramasser à chaque appel
	code_retour text;        -- le code retour (le texte pour les logs IA)
temp_rec ia_donnees;
begin
	temp_rec.monstre_cod := 3981810;
temp_rec.code_retour = '';
update perso set perso_pa = 12 where perso_cod = 3981810;


select into temp_rec.pos_cod, temp_rec.pos_x, temp_rec.pos_y, temp_rec.pos_etage
pos_cod, pos_x, pos_y, pos_etage
from perso_position
inner join positions on pos_cod = ppos_pos_cod
where ppos_perso_cod = temp_rec.monstre_cod;

	temp_rec.perso_pa := 12;

temp_rec := ia_include_deplacement(temp_rec, type_depl, 4, NULL, NULL);

return temp_rec.code_retour;
end;$function$

CREATE OR REPLACE FUNCTION public.test(integer, integer)
 RETURNS SETOF integer
 LANGUAGE plpgsql
AS $function$declare
	code_retour text;
	personnage alias for $1;
	v_dieu alias for $2;
	v_pos integer;
	v_lieu integer;
	ligne record;
	ligne2 record;
	v_req text;
	curs2 refcursor;
begin
	code_retour := '';
	for ligne in select tablename from pg_tables where tableowner = 'delain'
		and tablename like 'perso_vue_pos%' loop
		v_req = 'select 
			lpos_pos_cod,lpos_lieu_cod
			from lieu,lieu_position,'||ligne.tablename||' 
			where pvue_perso_cod = '||trim(to_char(personnage,'99999999999'))||' 
			and pvue_pos_cod =lpos_pos_cod
			and lpos_lieu_cod = lieu_cod
			and lieu_dieu_cod = '||trim(to_char(v_dieu,'99999999999'));
		open curs2 for execute v_req;
		--loop
		
fetch curs2 into v_pos;
			--exit when NOT FOUND;
			if v_pos is not null then
				return next v_pos;
			end if;
		--end loop;
close curs2;
	end loop;
	
	return;
end;
$function$

