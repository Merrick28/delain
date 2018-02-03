
--
-- Name: potion_instable(integer); Type: FUNCTION; Schema: potions; Owner: delain
--

CREATE or REPLACE FUNCTION potions.potion_instable(integer) RETURNS text
LANGUAGE plpgsql
AS $_$/****************************************************/
/* potion_instable                                  */
/*  génère un effet aléatoire à une potion instable */
/* PARAMETRES :                                     */
/*  $1 = perso_cod du lanceur de potions            */
/* RETOUR                                           */
/*  texte à utiliser directement                    */
/****************************************************/
declare
  code_retour text;
  personnage alias for $1;
  v_des integer;
  v_constit integer;
  v_force integer;
  v_dexterite integer;
  v_pv integer;
  v_perte_pv integer;
  v_perte_regen integer;
  v_temps_perte_regen integer;
  v_baisse_dex integer;
  v_temps_perte_vue integer;
begin
  v_des := lancer_des(1,100);
  if v_des <= 10 then
    -- gestion de perte de pv.
    select into v_constit,v_pv
      perso_pv,perso_con
    from perso
    where perso_cod = personnage;
    v_perte_pv = v_constit * 2;
    if v_perte_pv >= v_pv then
      v_perte_pv = v_pv - 1;
    end if;
    update perso set perso_pv = perso_pv - v_perte_pv where perso_cod = personnage;
    code_retour := 'Votre potion a un goût abominable ! Vous perdez '||trim(to_char(v_perte_pv,'999999'))||' points de vie.<br>';
  elsif v_des <= 20 then
    -- annulation de la régénération
    perform ajoute_bonus(personnage, '0RG', 2, 1);
    code_retour := 'Votre potion vous brûle la gorge. Vous ne pourrez pas régénérer de points de vie pendant 2 tours';
  elsif v_des <= 23 then
    -- régen baissée
    select into v_constit,v_force
      perso_con,perso_for
    from perso
    where perso_cod = personnage;
    v_perte_regen := floor(v_force/2);
    v_temps_perte_regen := floor(v_constit/4);
    perform ajoute_bonus(personnage, 'REG', v_temps_perte_regen, -v_perte_regen);
    code_retour := E'Votre potion vous brûle l''estomac. Votre régénération est diminuée de '||trim(to_char(v_perte_regen,'99999'))||' pendant '||trim(to_char(v_temps_perte_regen,'99999'))||' tours.<br>';
  elsif v_des <= 31 then
    -- baisse de l'esquive
    select into v_dexterite perso_dex
    from perso
    where perso_cod = personnage;
    v_baisse_dex := v_dexterite * 3;
    perform ajoute_bonus(personnage, 'ESQ', 2, -v_baisse_dex);
    code_retour := E'Votre potion vous donne la nausée. Vous avez un malus à l''esquive de '||trim(to_char(v_baisse_dex,'999999'))||' pendant 2 tours.';
  elsif v_des <= 42 then
    -- hallucinations, compteur d'esquive augmenté
    update perso
    set perso_nb_esquive = perso_nb_esquive + 4
    where perso_cod = personnage;
    code_retour := E'Vous avez des hallucinations, et esquivez des attaques qui n''existent pas....<br>';
  elsif v_des <= 48 then
    -- pieds gonflés
    perform ajoute_bonus(personnage, 'DEP', 2, 2);
    code_retour := 'Vos pieds gonflent, et vous êtes donc ralentis dans vos mouvements....';
  elsif v_des <= 55 then
    -- perte d'armure
    perform ajoute_bonus(personnage, 'ARM', 2, -2);
    code_retour := 'Cette potion vous brûle la peau, et fait baisser votre armure...';
  elsif v_des <= 65 then
    -- hypnose légère
    update perso
    set perso_pa = 0 where perso_cod = personnage;
    code_retour := E'Cette potion brûle tellement la gorge, qu''elle vous coupe le souffle. Vous ne pouvez plus rien faire d''autre qu''attendre que ça passe...';
  elsif v_des <= 70 then
    -- baisse des caracs de combat
    perform ajoute_bonus(personnage, 'TOU', 2, -15);
    code_retour := E'Cette potion vous retourne l''estomac, vous perdez de votre habileté au combat';
  elsif v_des <= 76 then
    -- baisse des caracs de combat
    perform ajoute_bonus(personnage, 'TOU', 2, -30);
    code_retour := E'Cette potion vous retourne l''estomac, vous perdez de votre habileté au combat';
  elsif v_des <= 82 then
    -- diminution des dégats
    select into v_constit,v_force
      perso_con,perso_for
    from perso
    where perso_cod = personnage;
    v_perte_regen := floor(v_force/2);
    v_temps_perte_regen := floor(v_constit/4);
    perform ajoute_bonus(personnage, 'DEG', v_temps_perte_regen, -v_perte_regen);
    code_retour := E'Vous vous sentez tout à coup très faible, et la force de vos coups s''en ressent immédiatement !';
  elsif v_des <= 89 then
    -- BERNARDO
    perform ajoute_bonus(personnage, 'BER', 4, 1);
    code_retour := 'Votre gorge vous brûle tellement que vous ne pouvez plus articuler une seule parole !';
  elsif v_des <= 95 then
    -- aveugle
    select into v_constit,v_force
      perso_con,perso_for
    from perso
    where perso_cod = personnage;
    v_temps_perte_vue := floor(v_constit/4);
    perform ajoute_bonus(personnage, 'VUE', v_temps_perte_vue, -10);
    code_retour := 'À peine buvez vous la potion que vous êtes aveuglé...';
  end if;
  return code_retour;
end;$_$;


ALTER FUNCTION potions.potion_instable(integer) OWNER TO delain;