--
-- Name: f_perso_vampirisme(integer); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function f_perso_vampirisme(integer) RETURNS numeric
LANGUAGE plpgsql
AS $_$/*******************************************/
/* f_perso_vampirisme                      */
/*  params : $1 = perso_cod                */
/*******************************************/
declare

  personnage alias for $1;
  code_retour numeric;
  v_vampirisme integer;

begin
  code_retour := 0;

  -- le vampirisme de l'arme
  select coalesce(sum(obj_vampire),0) into v_vampirisme
    from perso_objets,objets,objet_generique
    where perobj_perso_cod = personnage
        and perobj_equipe = 'O'
        and perobj_obj_cod = obj_cod
        and obj_gobj_cod = gobj_cod
        and gobj_tobj_cod = 1
        and coalesce(obj_vampire,0) != 0 ;

  -- le bonus de VaMPirisme
  v_vampirisme := COALESCE(v_vampirisme,0) + valeur_bonus(personnage, 'VMP');


  code_retour := GREATEST( 0, LEAST( 100, v_vampirisme));

  return code_retour;
end;$_$;


ALTER FUNCTION public.f_perso_vampirisme(integer) OWNER TO delain;
