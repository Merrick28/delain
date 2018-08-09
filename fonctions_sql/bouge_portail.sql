CREATE OR REPLACE FUNCTION public.bouge_portail()
 RETURNS text
 LANGUAGE plpgsql
AS $function$declare
  ligne record;
  etage integer;
  code_retour text;
  nb_bouge integer;
  ancienne_pos integer;
  nouvelle_pos integer;
  compt integer;

begin
  nb_bouge := 0;
  for ligne in select * from lieu where lieu_mobile = 'O'
                                        and lieu_date_bouge + '7 days'::interval < now() loop
    if lancer_des(1,100) < 10 then
      nb_bouge := nb_bouge + 1;
      select into etage,ancienne_pos
        pos_etage,pos_cod
      from lieu_position,positions
      where lpos_lieu_cod = ligne.lieu_cod
            and lpos_pos_cod = pos_cod;
      update lieu_position
      set lpos_pos_cod = pos_aleatoire(etage)
      where lpos_lieu_cod = ligne.lieu_cod;
      select into nouvelle_pos
        pos_cod
      from lieu_position,positions
      where lpos_lieu_cod = ligne.lieu_cod
            and lpos_pos_cod = pos_cod;
      update lieu set lieu_date_bouge = now() where lieu_cod = ligne.lieu_cod;
      perform init_automap_pos(ancienne_pos);
      perform init_automap_pos(nouvelle_pos);
    end if;
  end loop;
  code_retour := trim(to_char(nb_bouge,'999999'))||' lieux bougés.';
  return code_retour;
end;


$function$

CREATE OR REPLACE FUNCTION public.bouge_portail(integer, integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$declare
  ligne record;
  etage integer;
  temp_automap integer;

begin
  for ligne in select * from lieu where lieu_tlieu_cod = 8 loop
    select into etage
      pos_etage
    from lieu_position,positions
    where lpos_lieu_cod = ligne.lieu_cod
          and lpos_pos_cod = pos_cod;
    update lieu_position
    set lpos_pos_cod = pos_aleatoire(etage)
    where lpos_lieu_cod = ligne.lieu_cod;
  end loop;
  temp_automap := init_automap();
  return 0;
end;
$function$

