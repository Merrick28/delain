CREATE OR REPLACE FUNCTION public.ia_monstre(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$
declare
  cur refcursor;
  req text;
  sortie text;
  activation text;
  v_monstre alias for $1;
  v_pa integer;
  v_catastrophe_naturelle boolean;
  perso_quete integer; --détermine si on lance une ia de perso, mise arbitrairement en pacifique pour l'instant
  temp integer; -- Savoir si on joue le perso.
begin
  v_catastrophe_naturelle := false; -- false par défaut. Si true, les monstres courent en tous sens.
  activation := calcul_dlt2(v_monstre);
  select into req
    ia_fonction
  from type_ia,perso_ia
  where pia_perso_cod = v_monstre
        and pia_ia_type = ia_type;
  if not found then
    select into req
      ia_fonction
    from type_ia,perso,monstre_generique
    where perso_cod = v_monstre
          and perso_gmon_cod = gmon_cod
          and gmon_type_ia = ia_type;
    if not found then
      req := 'ia_standard([perso])';
    end if;
    select into perso_quete perso_type_perso from perso where perso_cod = v_monstre;
    if perso_quete = 1 then
      req := 'ia_bouge([perso])';
    end if;
  end if;

  select into temp count(1) from perso where perso_cod = v_monstre
                                             and (perso_dlt + (trim(to_char(perso_temps_tour/2, '99999'))||' minutes')::interval) < now();
  --(perso_dlt + (trim(to_char(perso_temps_tour/2, '999'))||' minutes')::interval) < now()
  --or
  --(perso_pa >= 2 and random() < 0.35)
  --);
  if (temp = 0) then
    if (random() < 0.35) then
      temp = 1;
    end if;
  end if;

  if (temp = 1) and not v_catastrophe_naturelle then
    req := 'select '||replace(req,'[perso]',trim(to_char(v_monstre,'9999999999999')));
    open cur for execute req;
    fetch cur into sortie;
    close cur;
    update perso set perso_der_connex = now() where perso_cod = v_monstre;
    insert into logs_ia (lia_perso_cod,lia_texte) values (v_monstre,activation||sortie);
  elsif not v_catastrophe_naturelle then
    sortie := 'Monstre '||trim(to_char(v_monstre,'999999999999'))||' non joué pour le moment';
  else
    sortie := 'Catastrophe naturelle. Le monstre fuit.';
    sortie := sortie || ia_rincevent(v_monstre);
  end if;
  select into v_pa perso_pa
  from perso
  where perso_cod = v_monstre;
  sortie := sortie||E'\
'||trim(to_char(v_pa,'99'))||' pa restants';
  truncate table liste_ouverte;
  truncate table liste_fermee;
  delete from temp_monstres where code_monstre = v_monstre;
  return sortie;
end;
$function$

