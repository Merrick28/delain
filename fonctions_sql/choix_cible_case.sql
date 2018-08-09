CREATE OR REPLACE FUNCTION public.choix_cible_case(integer, integer, integer, integer, integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/****************************************************/
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
  v_vue alias for $5;
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
  des_choix := lancer_des(1,100);

  --
  -- cible sur même case
  select into nb_cible_sur_case count(perso_cod)
  from perso_position,perso
  where ppos_perso_cod = v_cible
        and ppos_pos_cod = pos_actuelle
        and perso_actif = 'O'
        and perso_type_perso in (1,3)
        and perso_tangible = 'O'
        and not exists
  (select 1 from lieu,lieu_position
      where lpos_pos_cod = ppos_pos_cod
            and lpos_lieu_cod = lieu_cod
            and lieu_refuge = 'O');
  index_cible := lancer_des(1,nb_cible_sur_case);
  select into v_cible perso_cod
  from perso,perso_position,positions
  where ppos_pos_cod = pos_actuelle
        and ppos_perso_cod = perso_cod
        and ppos_pos_cod = pos_cod
        and perso_type_perso in (1,3)
        and perso_actif = 'O'
        and perso_tangible = 'O'
        and not exists
  (select 1 from lieu,lieu_position
      where lpos_pos_cod = ppos_pos_cod
            and lpos_lieu_cod = lieu_cod
            and lieu_refuge = 'O')
  limit 1
  offset index_cible;
  --


  update perso set perso_cible = v_cible where perso_cod = v_monstre;
  code_retour := v_cible;
  return code_retour;
end;
$function$

