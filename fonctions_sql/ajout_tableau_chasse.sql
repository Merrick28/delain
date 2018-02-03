--
-- Name: ajout_tableau_chasse(integer, integer, integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function ajout_tableau_chasse(integer, integer, integer, integer) RETURNS text
LANGUAGE plpgsql
AS $_$/**************************************************************************/
/* fonction ajout_tableau_chasse                                          */
/* On passe en paramètres :                                               */
/*   $1 = perso_cod                                                       */
/*   $2 = gmon_cod                                                        */
/*   $3 = ajout au total                                                  */
/*   $3 = ajout au solo                                                   */
/**************************************************************************/
declare
  ----------------------------------------------------------------------------
  -- variable de retour
  ----------------------------------------------------------------------------
  code_retour text;

  a_personnage alias for $1;		-- perso_cod du joueur
  a_gmon alias for $2;		-- gmon_cod du monstre
  a_num_total alias for $3;		-- total
  a_num_solo alias for $4;		-- solo


  v_temp integer;
  v_gmon integer;
begin
  code_retour := 'ok';
  if a_gmon is not null then
    v_gmon := a_gmon;
  else
    v_gmon := 0;
  end if;
  insert into perso_tableau_chasse
  (ptab_perso_cod,ptab_gmon_cod,ptab_total,ptab_solo,ptab_date)
  values
    (a_personnage,v_gmon,a_num_total,a_num_solo,now());

  return code_retour;
end;


$_$;


ALTER FUNCTION public.ajout_tableau_chasse(integer, integer, integer, integer) OWNER TO delain;
