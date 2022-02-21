--
-- Name: monture_comportement(integer); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function monture_comportement(integer) RETURNS text
LANGUAGE plpgsql
AS $_$declare
  v_perso alias for $1;
  v_cavalier integer ;
  v_monture integer ;
  v_event_chance decimal(10,2) ; 	-- % de chance de déclenchement
	v_event_pa character varying(24);			-- modificateur de PA au format dé rollist
	valeur integer;			-- lecture du dé rolliste
	v_message text;			-- message en cas de déclenchement !
	v_terrain text;			-- nom du terrain
	v_perso_nom text;			-- nom du perso
	v_monture_nom text;			-- nom de la monture
	code_retour text;			-- chaine de retour
begin

  code_retour := '' ;

  -- vérifier s'il y a un terrain specifique a cette position, on regarde si c'est un perso avec une monture avec des caracs speciales sur ce terrain
  select p.perso_cod, m.perso_cod, tmon_event_chance, coalesce(tmon_event_pa, ''), tmon_message, ter_nom, p.perso_nom, m.perso_nom into v_cavalier, v_monture, v_event_chance, v_event_pa, v_message, v_terrain, v_perso_nom, v_monture_nom
    from perso as p
    join perso_position on ppos_perso_cod=p.perso_cod
    join positions on pos_cod=ppos_pos_cod
    join perso as m on m.perso_cod=p.perso_monture and m.perso_actif = 'O' and m.perso_type_perso=2
    join monstre_terrain on tmon_gmon_cod = m.perso_gmon_cod and tmon_ter_cod=pos_ter_cod
    join terrain on ter_cod = pos_ter_cod
    where p.perso_cod=v_perso and p.perso_type_perso=1 and tmon_event_chance > 0  limit 1;
  if not found then

      -- vérifier s'il y a un terrain specifique a cette position, on regarde si c'est une monture qui a un cavalier et si elle a des caracs speciales sur ce terrain
      select p.perso_cod, m.perso_cod, tmon_event_chance, coalesce(tmon_event_pa, ''), tmon_message, ter_nom, p.perso_nom, m.perso_nom into v_cavalier, v_monture, v_event_chance, v_event_pa, v_message, v_terrain, v_perso_nom, v_monture_nom
        from perso as m
        join perso_position on ppos_perso_cod=m.perso_cod
        join positions on pos_cod=ppos_pos_cod
        join perso as p on p.perso_monture=m.perso_cod and p.perso_actif = 'O' and p.perso_type_perso=1
        join monstre_terrain on tmon_gmon_cod = m.perso_gmon_cod and tmon_ter_cod=pos_ter_cod
        join terrain on ter_cod = pos_ter_cod
        where m.perso_cod=v_perso and m.perso_type_perso=2 and tmon_event_chance > 0  limit 1;
      if not found then
          return code_retour ;    -- rien a faire
      end if;

  end if;

  if 100*random() > v_event_chance then
       return code_retour ;    -- rien a faire, la monture n'a pas réagit !
  end if;

  valeur := f_lit_des_roliste(v_event_pa);
  if valeur = 0 then
       return code_retour ;    -- rien a faire, tirage null
  end if;

  -- appliquer le changement (bonus/malus de PA au cavalier)
  update perso set perso_pa = LEAST(GREATEST(0, perso_pa+valeur), 12) where perso_cod = v_cavalier ;

  -- cas d'une monture sur un terrain où la monture a un comportement spécial !
  code_retour := code_retour || 'Votre monture réagit sur un terrain du type <b>' || v_terrain || '</b>' ;
  if valeur > 0 then
      code_retour := code_retour || ' vous faisant regagner <b>' || trim(to_char(valeur,'99')) || 'PA </b> (plafonné à 12)' ;
  else
      code_retour := code_retour || ' vous faisant perdre <b>' || trim(to_char(abs(valeur),'99')) || 'PA</b>' ;
  end if;


  if v_message != '' then

      if strpos(v_message , '[cible]') != 0 then
        perform insere_evenement(v_monture, v_cavalier, 54, v_message, 'O', 'N', null);
      else
        perform insere_evenement(v_monture, v_cavalier, 54, v_message, 'O', 'O', null);
      end if;

      v_message := REPLACE( REPLACE(v_message,'[cible]',  v_perso_nom), '[attaquant]',  v_monture_nom) ;
      code_retour := code_retour || ' : <br />' || v_message ;

  end if;

  return code_retour ;
end;
$_$;


ALTER FUNCTION public.monture_comportement(integer) OWNER TO delain;