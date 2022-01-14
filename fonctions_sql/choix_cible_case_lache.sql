--
-- Name: choix_cible_case_lache(integer, integer, integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function choix_cible_case_lache(integer, integer, integer, integer) RETURNS integer
LANGUAGE plpgsql
AS $_$/****************************************************/
/* fonction choix_cible_case : permet à un monstre de    */
/*   choisir une cible dans sa vue                  */
/* on passe en paramètres :                         */
/*   $1 = perso_cod du monstre                      */
/*   $2 = pos_cod du monstre                        */
/*   $3 = l etage du monstre                        */
/*   $4 = le nombre de persos en vue                */
/*   $5 = distance vue du monstre                   */
/****************************************************/
/* on a en sortie le perso_cod de la cible          */
/****************************************************/
/* créé le 08/08/2003                               */
/****************************************************/
declare
  code_retour integer;
  v_monstre alias for $1;
  pos_actuelle alias for $2;
  v_etage alias for $3;
  nb_joueur_en_vue alias for $4;
  --
  nb_joueur integer;
  v_cible integer;
  des_choix integer;
  nb_cible_en_vue integer;
  nb_cible_sur_case integer;
  index_cible integer;
  ligne_cible record;
  etat_cible numeric;
  dernier_etat_cible numeric;
  v_reputation integer;
  dernier_v_reputation integer;
  v_vie numeric;
  ligne record;



begin
  -- principes de base :
  -- le monstre n a plus sa cible sur la case
  -- on doit donc en choisir un autre
  -- on fait un lancer de dés pour savoir ce que le monstre va faire
  -- 1-25 : il essaie de garder la même cible
  -- 26-50 : il prend une cible sur la même case
  -- 51-75 : il prend la plus blessée
  -- 76-100 : il prend celle à la plus haute réputation
  -- dans tous les cas, si un des choix échoue, on perd en cible aléatoire
  v_vie := 2;
  --
  -- cible sur même case
  for ligne in select perso_cod,perso_pv,perso_pv_max
               from perso_position,perso
               where ppos_pos_cod = pos_actuelle
                     and perso_actif = 'O'
                     and perso_type_perso in (1,3)
                     and perso_tangible = 'O'
                     and ppos_perso_cod = perso_cod
                     and not exists
               (select 1 from lieu,lieu_position
                   where lpos_pos_cod = ppos_pos_cod
                         and lpos_lieu_cod = lieu_cod
                         and lieu_refuge = 'O') loop
    if (ligne.perso_pv/ligne.perso_pv_max::numeric) < v_vie then
      v_cible := ligne.perso_cod;
      v_vie := ligne.perso_pv/ligne.perso_pv_max::numeric;
    end if;
  end loop;
  update perso set perso_cible = v_cible where perso_cod = v_monstre;
  code_retour := v_cible;
  return code_retour;
end;
$_$;


ALTER FUNCTION public.choix_cible_case_lache(integer, integer, integer, integer) OWNER TO delain;