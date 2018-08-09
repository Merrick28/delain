CREATE OR REPLACE FUNCTION public.comp_spe_monstre(integer, integer)
 RETURNS void
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* Fonction comp_spe_monstre : Permet l'utilisation des comp spe */
/*                             des monstres par les IA           */
/* On passe en paramètres                                        */
/*    $1 = Monstre qui attaque                                   */
/*    $2 = perso_cod cible                                       */
/*    Rappel pour la fonction attaque :                          */
/*       0 = normale                                             */
/*       1 = AF lvl 1                                            */
/*       2 = AF lvl 2                                            */
/*       3 = AF lvl 3                                            */
/*       4 = Feinte lvl 1                                        */
/*       5 = Feinte lvl 2                                        */
/*       6 = Feinte lvl 3                                        */
/*       7 = Coup de grace lvl 1                                 */
/*       8 = Coup de grace lvl 2                                 */
/*       9 = Coup de grace lvl 3                                 */
/*      10 = bout portant lvl 1                                  */
/*      11 = bout portant lvl 2                                  */
/*      12 = bout portant lvl 3                                  */
/*      13 = tir précis lvl 1                                    */
/*      14 = tir précis lvl 2                                    */
/*      15 = tir précis lvl 3                                    */
/*      16 = balayage                                            */
/*      17 = garde manger                                        */
/*      18 = Hydre à neuf têtes                                  */
/*      19 = Jeu de Trolls                                       */
/* Le code sortie est une chaine html utilisable directement     */
/*****************************************************************/
/* Créé le 17/07/2006                                            */
/* Liste des modifications :                                     */
/*****************************************************************/

declare
  --------------------------------------------------------------------------------
  -- variables fourre tout
  --------------------------------------------------------------------------------
  code_retour text;				-- chaine qui contiendra le html de retour
  des integer;					-- lancer de des
  compt_loop integer;				-- comptage de boucle pour sortie
  temp_txt text;					-- texte de sortie d'attaque

  --------------------------------------------------------------------------------
  -- renseignements de l attaquant
  --------------------------------------------------------------------------------
  v_attaquant alias for $1;	-- monstre attaquant
  v_cible alias for $2;		-- cible du monstre
  v_pa integer;			-- nombre de PA du monstre
  num_comp_spe integer;		-- compétence de  l'attaque spéciale
  v_type_attaque integer;		-- transco de l'attaque spéciale pour la fonction attaque
  chance integer;			-- chance d'utiliser cette attaque spéciale
  nombre_util integer;		-- nombre d'attaques spéciales

begin
  code_retour := '';
  select into num_comp_spe,chance,nombre_util gmoncomp_comp_cod,gmoncomp_chance,comp_nb_util_tour from monstre_generique_comp,perso,competences
  where perso_gmon_cod = gmoncomp_gmon_cod
        and perso_cod = v_attaquant
        and gmoncomp_comp_cod in (25,61,62,63,64,65,66,67,68,72,73,74,75,76,77,89,94,95,96)
        and gmoncomp_comp_cod = comp_cod
  order by random()
  limit 1;
  if num_comp_spe = 25 then
    v_type_attaque := 1;
  elsif num_comp_spe = 61 then
    v_type_attaque := 2;
  elsif num_comp_spe = 62 then
    v_type_attaque := 3;
  elsif num_comp_spe = 63 then
    v_type_attaque := 4;
  elsif num_comp_spe = 64 then
    v_type_attaque := 5;
  elsif num_comp_spe = 65 then
    v_type_attaque := 6;
  elsif num_comp_spe = 66 then
    v_type_attaque := 7;
  elsif num_comp_spe = 67 then
    v_type_attaque := 8;
  elsif num_comp_spe = 68 then
    v_type_attaque := 9;
  elsif num_comp_spe = 72 then
    v_type_attaque := 10;
  elsif num_comp_spe = 73 then
    v_type_attaque := 11;
  elsif num_comp_spe = 74 then
    v_type_attaque := 12;
  elsif num_comp_spe = 75 then
    v_type_attaque := 13;
  elsif num_comp_spe = 76 then
    v_type_attaque := 14;
  elsif num_comp_spe = 77 then
    v_type_attaque := 15;
  elsif num_comp_spe = 89 then
    v_type_attaque := 16;
  elsif num_comp_spe = 94 then
    v_type_attaque := 17;
  elsif num_comp_spe = 95 then
    v_type_attaque := 18;
  elsif num_comp_spe = 96 then
    v_type_attaque := 19;
  end if;
  des := lancer_des(1,100);
  select into v_pa perso_pa
  from perso
  where perso_cod = v_attaquant;
  if v_type_attaque in (16,17,18,19) then
    if des <= chance and v_pa >= 6 then
      compt_loop := 0;
      while (compt_loop < nombre_util) loop
        compt_loop := compt_loop + 1;
        exit when compt_loop >= 3;
        temp_txt := attaque_spe(v_attaquant,v_cible,v_type_attaque);
      end loop;
    end if;
  elsif v_type_attaque is not null then
    if des <= chance then
      compt_loop := 0;
      while (v_pa >= getparm_n(9)) loop
        compt_loop := compt_loop + 1;
        exit when compt_loop >= 3;
        temp_txt := attaque(v_attaquant,v_cible,v_type_attaque);
      end loop;
    end if;
  end if;
end;$function$

