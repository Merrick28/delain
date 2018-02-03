--
-- Name: action_immediate(integer, text, integer); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function action_immediate(integer, text, integer) RETURNS text
LANGUAGE plpgsql
AS $_$/************************************************/
/* action_immediate				                      */
/* Détermine le résultat d'une action immédiate */
/* lors de l'activation d'une DLT, ou en deb de */
/* tour d'un monstre (à vérifier)								*/
/* On passe en paramètres:                      */
/*   $1 = source (perso_cod)        					  */
/*   $2 = bonus (de la table bonus_type)        */
/*   $3 = valeur (Entier)      					        */
/************************************************/
/* Créé le 15 Janvier 2010     	                */
/************************************************/

declare
  -- Parameters
  v_source alias for $1;
  v_bonus alias for $2;
  v_valeur alias for $3;


  -- initial data
  v_valeur2 integer;
  v_pv integer;
  v_pv_max integer;
  v_pos integer;	--position du perso affecté
  v_perso_niveau integer;
  v_type_perso integer;
  nom_cible text;
  -- Output and data holders
  v_fam integer;
  des integer;
  code_retour text;
  v_temp text;
  v_bonus_existant text;
  v_bonus_duree integer;
  v_sort_1 integer;
  v_sort_2 integer;
  v_pgroupe integer;
  v_pgroupe_object integer;
  v_objectif_statut text;

begin
  -- Initialisation des conteneurs
  code_retour := '';
  if v_bonus = 'PVIE' then
    select into v_pv,v_pv_max perso_pv,perso_pv_max from perso where perso_cod = v_source;
    if (v_valeur < 0)  then
      code_retour := '<br>Vous subissez une variation de points de vie de <b>'||to_char(v_valeur,'99999999999999')||'</b><br>';
      if abs(v_valeur) > v_pv then -- On traite le cas où c'est négatif et dégâts supérieurs aux PV
        code_retour := code_retour||'Les dégâts subits sont trop importants, et un retour au dispensaire est nécessaire<br>';
        code_retour := code_retour||tue_perso_final(v_source,v_source);
      else
        update perso set perso_pv = perso_pv + (v_valeur) where perso_cod = v_source;
      end if;
    else
      if v_pv_max = v_pv then
        code_retour := 'KO;'||code_retour;
      elsif v_valeur > (v_pv_max - v_pv) then
        code_retour := '<br>Vous avez été entièrement soigné, gagnant<b> '||to_char(v_valeur,'99999999999999')||'</b> points de vie de <br>';
        update perso set perso_pv = v_pv_max where perso_cod = v_source;
      else
        code_retour := '<br>Vous avez été légèrement soigné, gagnant<b> '||to_char(v_valeur,'99999999999999')||'</b> points de vie<br>';
        update perso set perso_pv = perso_pv + (v_valeur) where perso_cod = v_source;
      end if;
    end if;
  elseif v_bonus = 'BIP2' then
    select into v_temp perso_tangible from perso where perso_cod = v_source;
    if v_temp = 'N' then -- On traite le cas d'un perso intangible pour lui donner bipbip
      v_valeur2 := v_valeur + 1; --Les compteurs des bonus sont décrémentés après le passage de cette fonction
      perform ajoute_bonus(v_source, 'DEP', v_valeur2,-2);
    else
      code_retour := 'KO;'||code_retour;
    end if;
  elseif v_bonus = 'QENL' then --Quête pour enluminure
    if v_valeur = v_source then -- cette case est bien faite pour ce perso
      code_retour := '<br>Une douleur lancinante émerge dans votre tête, des images de votre vie passée dans ces souterrains apparaissent, la douleur devient insoutenable !
											<br>Vous avez l''impression de vous dédoubler ... Et effectivement, il se passe quelque chose ...';
      select into v_pos,nom_cible,v_perso_niveau ppos_pos_cod,perso_nom,perso_niveau from perso_position,perso where ppos_perso_cod = v_source and perso_cod = ppos_perso_cod;
      --On crée le monstre
      v_fam := duplique_perso_nom(v_source,'Avatar de '||nom_cible);
      delete from perso_compte where pcompt_perso_cod = v_fam;
      --On customize le monstre maintenant
      update perso
      set perso_cible = v_source,
        perso_type_perso = 2,
        perso_pv = perso_pv_max,
        perso_pa = 12,
        perso_der_connex = now(),
        perso_po = 0,
        perso_amelioration_armure = 100,
        perso_gmon_cod = 534,
        perso_temps_tour = perso_temps_tour - 90
      where perso_cod = v_fam;
      insert into perso_ia (pia_perso_cod,pia_ia_type) values (v_fam,9); --IA en type magicien
      -- on lui rajoute des sorts, notamment offensifs si il n'en a pas
      delete from perso_sorts where psort_perso_cod = v_fam;
      if v_perso_niveau > 40 then
        insert into perso_sorts (psort_perso_cod,psort_sort_cod) values (v_fam,28);
        insert into perso_sorts (psort_perso_cod,psort_sort_cod) values (v_fam,126);
        insert into perso_sorts (psort_perso_cod,psort_sort_cod) values (v_fam,130);
        insert into perso_sorts (psort_perso_cod,psort_sort_cod) values (v_fam,133);
        insert into perso_sorts (psort_perso_cod,psort_sort_cod) values (v_fam,135);
        insert into perso_sorts (psort_perso_cod,psort_sort_cod) values (v_fam,134);
        insert into perso_sorts (psort_perso_cod,psort_sort_cod) values (v_fam,145);
        insert into perso_sorts (psort_perso_cod,psort_sort_cod) values (v_fam,146);
        insert into perso_sorts (psort_perso_cod,psort_sort_cod) values (v_fam,147);
        insert into perso_sorts (psort_perso_cod,psort_sort_cod) values (v_fam,142);
        insert into perso_sorts (psort_perso_cod,psort_sort_cod) values (v_fam,41);
        insert into perso_sorts (psort_perso_cod,psort_sort_cod) values (v_fam,42);
        insert into bonus (bonus_perso_cod,bonus_nb_tours,bonus_tbonus_libc,bonus_valeur) values (v_fam,20,'PAM','-2');
      elseif v_perso_niveau > 20 then
        insert into perso_sorts (psort_perso_cod,psort_sort_cod) values (v_fam,142);
        insert into perso_sorts (psort_perso_cod,psort_sort_cod) values (v_fam,40);
        insert into perso_sorts (psort_perso_cod,psort_sort_cod) values (v_fam,43);
        insert into perso_sorts (psort_perso_cod,psort_sort_cod) values (v_fam,29);
        insert into perso_sorts (psort_perso_cod,psort_sort_cod) values (v_fam,44);
        insert into perso_sorts (psort_perso_cod,psort_sort_cod) values (v_fam,34);
        insert into perso_sorts (psort_perso_cod,psort_sort_cod) values (v_fam,13);
        insert into perso_sorts (psort_perso_cod,psort_sort_cod) values (v_fam,55);
        insert into bonus (bonus_perso_cod,bonus_nb_tours,bonus_tbonus_libc,bonus_valeur) values (v_fam,15,'PAM','-2');
      else
        insert into perso_sorts (psort_perso_cod,psort_sort_cod) values (v_fam,42);
        insert into perso_sorts (psort_perso_cod,psort_sort_cod) values (v_fam,29);
        insert into perso_sorts (psort_perso_cod,psort_sort_cod) values (v_fam,34);
        insert into perso_sorts (psort_perso_cod,psort_sort_cod) values (v_fam,63);
        insert into perso_sorts (psort_perso_cod,psort_sort_cod) values (v_fam,13);
        insert into bonus (bonus_perso_cod,bonus_nb_tours,bonus_tbonus_libc,bonus_valeur) values (v_fam,10,'PAM','-2');
      end if;
      --On rajoute les deux objets qui vont bien
      perform cree_objet_perso(730,v_fam);
      perform cree_objet_perso(731,v_fam);
      --On supprime la fonction de cette case !!!!!!!!!!! A enlever en commentaire pour activer après tests !!!!!!!!!!!!
      update positions set pos_fonction_dessus = '' where pos_cod = v_pos;
      update quete_perso set pquete_param = v_fam
      where pquete_perso_cod = v_source
            and pquete_quete_cod = 16
            and cast (pquete_param_texte as integer) = v_pos;
    else
      code_retour := 'KO;'||code_retour;
    end if;
  elseif v_bonus = 'QOBJ' then --Mission pour prise d'objectif
    --On regarde si le perso est engagé dans la mission
    select into v_type_perso,v_pos,nom_cible perso_type_perso,ppos_pos_cod,perso_nom from perso_position,perso where ppos_perso_cod = v_source and perso_cod = ppos_perso_cod;
    select into v_fam,v_temp,v_objectif_statut,v_valeur2,v_pgroupe mgroupe_perso_cod,mobject_temps,mobject_statut,mobject_perso_cod,mgroupe_groupe_cod from quetes.mission_groupe,quetes.mission_objectif,quetes.mission_groupe_def
    where mgroupedef_mission_cod = mobject_mission_cod
          and mgroupedef_cod = mgroupe_groupe_cod
          and mobject_pos_cod = v_pos
          and (mgroupe_perso_cod = v_source or v_type_perso = 2);
    if found and (v_valeur2 != v_source or v_valeur2 is null ) and v_objectif_statut != 'D' and v_objectif_statut != 'O' then
      --On vérifie que quelqu'un du même groupe n'a pas déjà validé cet objectif
      select into v_pgroupe_object mgroupe_groupe_cod from quetes.mission_groupe where mgroupe_groupe_cod = v_pgroupe and mgroupe_perso_cod = v_valeur2;
      if found then
        code_retour := 'KO;<br>Un membre de votre groupe de mission a déjà validé cet objectif.';
      else
        --On valide cet objectif de quête
        update quetes.mission_objectif set mobject_date_prise = now(),mobject_perso_cod = v_source,mobject_statut = 'N' where mobject_pos_cod = v_pos;
        code_retour := '<br>La prise de l''objectif n''est pas immédiate. <b>Elle sera confirmée dans '||v_temp||' heures</b>, si personne d''autre (aventurier ou monstre) ne vient vous disputer cet objectif entre temps';
      end if;
    elsif v_valeur2 = v_source then
      code_retour := 'KO;<br>Vous avez déjà validé cet objectif.';
    else
      code_retour := 'KO;';
    end if;
    if v_type_perso = 2  and v_objectif_statut = 'N' then
      update quetes.mission_objectif set mobject_date_prise = now(),mobject_perso_cod = v_source,mobject_statut = 'N' where mobject_pos_cod = v_pos;
      code_retour := 'KO;<br>Il s''agit d''un monstre qui valide cet objectif.';
    end if;
  end if;
  return code_retour;
end;$_$;


ALTER FUNCTION public.action_immediate(integer, text, integer) OWNER TO delain;
