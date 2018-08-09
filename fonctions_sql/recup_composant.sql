CREATE OR REPLACE FUNCTION potions.recup_composant(integer, integer, numeric)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*********************************************************/
/* function recup_composant                              */
/* récupération des composants à partir d’une position   */
/*  vide                                                 */
/* parametres :                                          */
/*  $1 = personnage qui tente la récupération            */
/*  $2 = position concernée                              */
/*  $3 = valeur de la phase lunaire                      */
/* Sortie :                                              */
/*  code_retour = texte exploitable par php              */
/*********************************************************/
/**********************************************************/
/*																														*/
/**************************************************************/
declare
  personnage alias for $1;	-- perso_cod
  posale alias for $2;				-- pos_cod
  phase alias for $3;				-- valeur de la phase lunaire, critère de détermination de la quantité récupurée
  code_retour text;				-- code retour
  quantite integer;				-- quantité de la position scrutée
  cod_composant integer;				-- type de composant sur la position
  intel integer;								--intelligence du perso
  dex integer;									-- dextérité du perso
  qte_max_lunaison integer;			-- qté max récupérable en fonction de la lunaison
  qte_max_intel integer;				-- qté max récupérable en fonction de l'intelligence
  qte_max integer;							-- qté max récupérable en fonction des max précédents
  qte_ramassable integer;				--qté qui peut être ramassée en fonction du max et de la quantité sur la case
  quantite_totale	integer;			--Qté sommée de tous les composants présents sur la case, quel que soit le type
  alchimie integer;			-- valeur de la compétence en alchimie du perso
  comp_alchimie integer;			-- Compétence en alchimie du perso (Niv1, 2 ou 3)
  des integer;					--jet de dés
  resultat text;				--texte d'amélioration de la comp
  amelioration integer;	--Détermine si amélioration de comp ou pas
  px_gagne numeric;						--Gains de pxs au ramassage, en fonction de la  quantité max et de la quantité ramassée
  v_req text;
  curs2 refcursor;
  ligne record;
  compteur integer;
  bis_position integer;
  gain_renommee numeric;		-- gain (ou perte) de renommée artisanale

begin
  /*********************************************************/
  /*        I N I T I A L I S A T I O N S                  */
  /*********************************************************/
  code_retour := '';
  px_gagne := 0;
  qte_max := 0;
  gain_renommee := 0.1;

  select into quantite_totale, bis_position
    sum(ingrpos_qte), ingrpos_pos_cod
  from ingredient_position
  where ingrpos_pos_cod = posale
  group by ingrpos_pos_cod;

  if not found or quantite_totale = 0 then
    return '<br>Pas de chance, cet endroit ne contient aucun composant. Si vous avez bien cherché auparavant, alors c’est que rien n’a eu le temps de repousser depuis le dernier passage. Autrement, il est possible que rien ne poussera jamais ici !<br>';
  else
    code_retour := code_retour||'<br>Aujourd’hui, vous avez de la chance, en cherchant bien, vous arrivez à trouver quelques ingrédients qui devraient être utiles pour faire des potions.<br> Vous vous baissez, et les mettez dans votre besace.<br>';
  end if;

  select into intel, dex perso_int, perso_dex from perso where perso_cod = personnage;
  /*On va déterminer le max de composants récupérables*/
  /*étape intelligence*/
  if intel < 10 then
    qte_max_intel = 2;
  elsif intel < 14 then
    qte_max_intel = 4;
  elsif intel < 18 then
    qte_max_intel = 5;
  else qte_max_intel = 6;
  end if;

  /*étape lunaison*/
  if ( phase < 2.5) or ( 47.5 <= phase and phase  < 52.5) or ( 97.5 <= phase ) /*phase de pleine lune ou nouvelle lune*/
  then
    qte_max_lunaison = 6 ;
    gain_renommee := gain_renommee * 3;
    code_retour := code_retour||'<br>Vous êtes dans la période la plus propice à la collecte d’ingrédients ! Profitez-en, elle ne dure que peu de temps, et vous permet vraiment de remplir votre besace.<br>';
  elsif ( 2.5 <= phase and phase  < 22.5) or ( 77.5 <= phase and phase  < 97.5) /*Premier croissant et dernier croissant*/
    then
      qte_max_lunaison = 2 ;
      code_retour := code_retour||'<br>Vous ne deviez rien avoir à faire d’autre, parce qu’avec ce que vous allez récolter, vous ne pourrez sans doute pas faire grand chose.<br>';
  elsif ( 22.5 <= phase and phase  < 27.5) or ( 73.5 <= phase and phase  < 77.5) /*premier quartier ou dernier quartier*/
    then
      qte_max_lunaison = 4 ;
      gain_renommee := gain_renommee * 2;
      code_retour := code_retour||'<br>C’est une bonne période, vous avez de fortes chances de récupérer de bonnes choses aujourd’hui.<br>';
  elsif ( 27.5 <= phase and phase  < 47.5) or ( 52.5 <= phase and phase  < 73.5) /*lune gibeuse*/
    then
      qte_max_lunaison = 3 ;
      code_retour := code_retour||'<br>Allez, un peu de coeur à l’ouvrage. Ca vous amènera de quoi concocter quelques fioles, mais ce sera limité.<br>';
  else code_retour := '<br>Cette lunaison n’existe pas. Merci de reporter le problème sur le forum. Information complémentaire : phase = ' || trim(to_char(phase,'99999999999')) || '.<br>';
    return code_retour;
  end if;

  /*déterminaiton de la quantité max finale*/
  qte_max := min(qte_max_lunaison,qte_max_intel);

  /*On détermine la quantité ramassée. Pour cela, on va tester par rapport au score en alchimie, compétence qui va être utilisée pour le ramassage, avec facteur de la dex*/
  select into alchimie, comp_alchimie
    pcomp_modificateur, pcomp_pcomp_cod
  from perso_competences
  where pcomp_perso_cod = personnage and pcomp_pcomp_cod in (97,100,101);

  des := lancer_des(1,100);
  if alchimie <= getparm_n(1) then -- amélioration auto then
    amelioration := 1;
  end if;
  if des < (alchimie + dex) then
    code_retour := code_retour||'Grâce à vos compétences, vous êtes capable de récupérer un maximum d’ingrédients pour fabriquer vos potions. Cela vous sera surement utile pour la suite.<br>';
    if alchimie < 70 then
      amelioration := 1;
    else
      code_retour := code_retour||'<b>Il n’est pas possible d’augmenter sa compétence alchimie au delà de 70% en effectuant des ramassages</b><br>';
    end if;
  else
    code_retour := code_retour||'Cette recherche n’est pas la meilleure que vous puissiez faire, mais c’est déjà ça pour votre concoction de potion.<br>';
  end if;

  if amelioration = 1 then
    resultat := ameliore_competence_px(personnage,comp_alchimie,alchimie);
    code_retour := code_retour||'Votre jet d’amélioration est de '||split_part(resultat,';',1)||', '; -- pos 7 8 9 10
    if split_part(resultat,';',2) = '1' then
      code_retour := code_retour||'vous avez donc <b>amélioré</b> cette compétence. <br>';
      code_retour := code_retour||'Sa nouvelle valeur est '||split_part(resultat,';',3)||'<br><br>';
      px_gagne := px_gagne + 1;
    else
      code_retour := code_retour||'vous n’avez pas amélioré cette compétence.<br><br> ';
    end if;
  end if; --  fin d'amélioration des compétences


  /*Ligne de contrôle avec les variables*/
  --code_retour := code_retour||'<br>Phase de lune : '||phase||'<br>max lunaison : '||trim(to_char(qte_max_lunaison,9999999))||'<br>max intelligence : '||trim(to_char(qte_max_intel,999999))||'<br>Quantité totale : '||trim(to_char(quantite_totale,9999999))||'<br>';

  /*Modification des PA et des Px - Gain de Px au ramassage dépendant notamment de la lunaison, de la quantité max, et de la quantité de la case */
  px_gagne := px_gagne + (min(qte_max,quantite_totale) / 2);
  update perso
  set perso_pa = perso_pa - 4,
    perso_px = perso_px + px_gagne,
    perso_renommee_artisanat = perso_renommee_artisanat + gain_renommee
  where perso_cod = personnage;

  /*Modification de la quantité dans la besace*/
  /* Pour cela, on détermine ce qui est ramassé*/
  compteur := 1;
  qte_ramassable := min(qte_max,quantite_totale);

  if compteur <= qte_ramassable then
    while (compteur <= min(qte_max,quantite_totale))
    loop
      v_req = 'select ingrpos_qte,ingrpos_gobj_cod,ingrpos_cod as code,lancer_des(1,1000) as num,gobj_nom from ingredient_position,objet_generique
				where ingrpos_pos_cod = '||trim(to_char(posale,'99999999999'))||'
				and ingrpos_qte != 0
				and gobj_cod = ingrpos_gobj_cod
				order by num limit 1';
      open curs2 for execute v_req;
      fetch curs2 into ligne;

      update ingredient_position
      set ingrpos_qte = ingrpos_qte - 1
      where ingrpos_pos_cod = posale and ingrpos_gobj_cod = ligne.ingrpos_gobj_cod and ingrpos_cod=ligne.code;

      perform cree_objet_perso(ligne.ingrpos_gobj_cod,personnage);

      if compteur = 1 then
        code_retour := code_retour||'Vous ramassez une once de '||ligne.gobj_nom||' par-ci';
      elsif compteur = 2 then
        code_retour := code_retour||', une once de '||ligne.gobj_nom||' par-là';
      elsif compteur = 3 then
        code_retour := code_retour||',une de '||ligne.gobj_nom||' dans ce coin';
      elsif compteur = 4 then
        code_retour := code_retour||', une autre de '||ligne.gobj_nom||' bien cachée';
      elsif compteur = 5 then
        code_retour := code_retour||', en vous baissant une once de '||ligne.gobj_nom;
      else
        code_retour := code_retour||' et enfin, une once de '||ligne.gobj_nom||' !';
      end if;
      compteur := compteur + 1;
      close curs2;
    end loop;
  end if;
  code_retour := code_retour||'.<br>';
  -- code_retour := code_retour||'Debug ramassage : '||trim(to_char(qte_ramassable,9999999))||'<br>';
  code_retour := code_retour||'Vous gagnez '||trim(to_char(px_gagne,'99999'))||' PX pour cette action.<br>';
  return code_retour;
end;$function$

