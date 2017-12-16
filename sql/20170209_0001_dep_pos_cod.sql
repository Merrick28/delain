CREATE OR REPLACE FUNCTION public.dep_pos_cod(integer,integer,integer)
  RETURNS text AS
  $BODY$
  /*****************************************************************/
  /* function dep_pos_cod					 																 */
  /*         								                                       */
  /*****************************************************************/
  /* Créé le 04/06/2010                                            */
  /*****************************************************************/
  declare
  -- variables E/S
  code_retour integer;
  pos_actuelle alias for $1;
  pos_dest alias for $2;
  v_monstre alias for $3;
  -- variables de calcul
  distance_init integer;
  distance_temp integer;
  nouvelle_pos integer;
  r_pos record;
  etage integer;
  v_x integer;
  v_y integer;
  v_pos integer;
  v_temp integer;
  cout_g integer;
  temp_cout_g integer;
  temp_cout_h integer;
  temp_cout_f integer;
  parent integer;
  compt integer;
  temp integer;

  begin

  cout_g := 0;
  temp_cout_g := 0;
  temp_cout_h := 0;
  temp_cout_f := 0;
  v_pos := pos_actuelle;
  compt := 1;

  -- table liste ouverte, liste fermée
  --on ajoute la position de départ dans la liste fermée, car elle n'est pas sélectionnable
  insert into liste_fermee (lferm_pos_cod,lferm_parent,lferm_compt,lferm_perso_cod) values (pos_actuelle,pos_actuelle,compt,v_monstre);
  select into v_x,v_y,etage pos_x,pos_y,pos_etage from positions where pos_cod = pos_actuelle;
  select into temp count(pos_cod) from positions
  where pos_x >= (v_x - 1) and pos_x <= (v_x + 1)
  and pos_y >= (v_y - 1) and pos_y <= (v_y + 1)
  and pos_etage = etage
  and not exists (select 1 from murs where mur_pos_cod = pos_cod)
  and pos_cod != pos_actuelle;
  if temp = 0 then
  -- aucune bonne position trouvée
  return;
  end if;
  --On sélectionne tous les noeuds qui sont adjacents, dans le même étage et ne sont pas des murs
  for r_pos in select pos_cod from positions
  where pos_x >= (v_x - 1) and pos_x <= (v_x + 1)
        and pos_y >= (v_y - 1) and pos_y <= (v_y + 1)
        and pos_etage = etage
        and not exists (select 1 from murs where mur_pos_cod = pos_cod)
        and pos_cod != pos_actuelle
  loop
  --On vérifie qu'il n'est pas dans la liste fermée
  select into v_temp lferm_pos_cod from liste_fermee where lferm_pos_cod = r_pos.pos_cod and lferm_perso_cod = v_monstre;
  if not found then
  --Calcul de G
  temp_cout_g := cout_G + distance(r_pos.pos_cod,pos_actuelle);
  --Calcul H et F
  temp_cout_h := distance(r_pos.pos_cod,pos_dest);
  temp_cout_f := temp_cout_g + temp_cout_h;
  parent := pos_actuelle;
  select into v_temp louv_cout_f from liste_ouverte where louv_pos_cod = r_pos.pos_cod and louv_perso_cod = v_monstre;
  if found then
  if temp_cout_f < v_temp then --MAJ
  update liste_ouverte set louv_parent = parent,louv_cout_f = temp_cout_f,louv_cout_g = temp_cout_g,louv_cout_h = temp_cout_h where louv_pos_cod = r_pos.pos_cod and louv_perso_cod = v_monstre;
  end if;
  else
  insert into liste_ouverte (louv_pos_cod,louv_parent,louv_cout_f,louv_cout_g,louv_cout_h,louv_perso_cod) values (r_pos.pos_cod,parent,temp_cout_f,temp_cout_g,temp_cout_h,v_monstre);
  end if;
  end if;
  end loop;
  while v_pos != pos_dest loop
  select into v_pos,parent,cout_G louv_pos_cod,louv_parent,louv_cout_g from liste_ouverte where louv_perso_cod = v_monstre order by louv_cout_f asc limit 1;
  compt := compt +1;
  insert into liste_fermee (lferm_pos_cod,lferm_parent,lferm_compt,lferm_perso_cod) values (v_pos,parent,compt,v_monstre);
  delete from liste_ouverte where louv_pos_cod = v_pos and louv_perso_cod = v_monstre;
  select into v_x,v_y,etage pos_x,pos_y,pos_etage from positions where pos_cod = v_pos;
  for r_pos in select pos_cod from positions
  where pos_x >= (v_x - 1) and pos_x <= (v_x + 1)
        and pos_y >= (v_y - 1) and pos_y <= (v_y + 1)
        and pos_etage = etage
        and not exists (select 1 from murs where mur_pos_cod = pos_cod)
        and pos_cod != v_pos
  loop
  --On vérifie qu'il n'est pas dans la liste fermée
  select into v_temp lferm_pos_cod from liste_fermee where lferm_pos_cod = r_pos.pos_cod and lferm_perso_cod = v_monstre;
  if not found then
  --Calcul de G
  temp_cout_g := cout_G + distance(r_pos.pos_cod,v_pos);
  --Calcul H et F
  temp_cout_h := distance(r_pos.pos_cod,pos_dest);
  temp_cout_f := temp_cout_g + temp_cout_h;
  parent := v_pos;
  select into v_temp louv_cout_f from liste_ouverte where louv_pos_cod = r_pos.pos_cod and louv_perso_cod = v_monstre;
  if found then
  if temp_cout_f < v_temp then --MAJ
  update liste_ouverte set louv_parent = parent,louv_cout_f = temp_cout_f,louv_cout_g = temp_cout_g,louv_cout_h = temp_cout_h where louv_pos_cod = r_pos.pos_cod and louv_perso_cod = v_monstre;
  end if;
  else
  insert into liste_ouverte (louv_pos_cod,louv_parent,louv_cout_f,louv_cout_g,louv_cout_h,louv_perso_cod) values (r_pos.pos_cod,parent,temp_cout_f,temp_cout_g,temp_cout_h,v_monstre);
  end if;
  end if;
  end loop;
  end loop;
  while compt > 1 loop
  select into v_temp lferm_compt from liste_fermee where lferm_pos_cod = (select lferm_parent from liste_fermee where lferm_compt = compt and  lferm_perso_cod = v_monstre) and  lferm_perso_cod = v_monstre;
  if v_temp != compt - 1 then
  delete from liste_fermee where lferm_compt > v_temp and lferm_compt < compt and  lferm_perso_cod = v_monstre;
  end if;
  compt := v_temp;
  end loop;
  end;
$BODY$
LANGUAGE plpgsql VOLATILE
COST 100;
ALTER FUNCTION public.dep_pos_cod(integer,integer,integer)
OWNER TO delain;
GRANT EXECUTE ON FUNCTION public.dep_pos_cod(integer,integer,integer) TO delain;
GRANT EXECUTE ON FUNCTION public.dep_pos_cod(integer,integer,integer) TO public;