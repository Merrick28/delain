--
-- Name: entrer_controle_arene(integer, integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION entrer_controle_arene(integer, integer, integer) RETURNS integer
    LANGUAGE plpgsql
    AS $_$/* fonction entrer_controle_arene                                     */
/*                                                           */
/*   on passe en paramètres :                                */
/*   $1 = perso_cod                                          */
/*   $2 = pos_cod départ                                     */
/*   $3 = pos_cod arrivée                                    */
/*************************************************************/


declare

	v_perso_cod alias for $1;
	v_pos_depart alias for $2;
	v_pos_arrive alias for $2;
	
  v_depart_etage_arene character(1);      -- etage de départ de la teleportation
  v_arrive_etage_arene character(1);      -- etage d'arrivée de la teleportation
  v_arrive_pos_etage integer;      -- etage d'arrivée de la teleportation
	

begin

  select etage_arene into v_depart_etage_arene from positions inner join etage on etage_numero = pos_etage where pos_cod = v_pos_depart ; 
  select etage_arene, pos_etage into v_arrive_etage_arene, v_arrive_pos_etage from positions inner join etage on etage_numero = pos_etage where pos_cod = v_pos_arrive ;
             
  if  v_depart_etage_arene = 'N' and v_arrive_etage_arene = 'O' then
  
      -- D’un étage normal vers une arène
      delete from perso_arene where parene_perso_cod = v_perso_cod ;
      insert into perso_arene (parene_perso_cod, parene_etage_numero, parene_pos_cod, parene_date_entree) values(v_perso_cod, v_arrive_pos_etage, v_pos_depart, now()) ;
      
  
  elseif v_depart_etage_arene = 'O' and v_arrive_etage_arene = 'O' then

      -- D’une arène vers une autre
       update perso_arene set parene_etage_numero = v_arrive_pos_etage where parene_perso_cod = v_perso_cod ;
                                       
                                   
  elseif v_depart_etage_arene = 'O' and v_arrive_etage_arene = 'N' then

      -- D’une arène vers un étage normal
      delete from perso_arene where parene_perso_cod = v_perso_cod ;

  end if;
                                                                               
  return 0;

end;$_$;


ALTER FUNCTION public.entrer_controle_arene(integer, integer, integer) OWNER TO delain;
