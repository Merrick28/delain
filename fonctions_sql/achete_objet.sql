--
-- Name: achete_objet(integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function achete_objet(integer, integer) RETURNS text
LANGUAGE plpgsql
AS $_$/************************************************/
/* fonction vend_objet                          */
/* cette fonction a pour but de vendre un objet */
/* venu des postes de garde                     */
/* on passe en paramètres :                     */
/*  $1 = le perso_cod qui achète                */
/*  $2 = l'objet acheté                         */
/*     1 = tonneau de bière                     */
/*     2 = opium                                */
/* on a en retour une zone texte exploitable    */
/*  dans action.php                             */
/************************************************/
declare
  code_retour text;
  personnage alias for $1;
  v_objet alias for $2;
  v_type_lieu integer;
  v_poids numeric;
  v_poids_max integer;
  v_poids_objet integer;
  v_objet_generique integer;
  v_prix integer;
  v_po integer;
  temp_txt text;
  v_nom_objet text;
  texte_evt text;
begin
  -- vérification de la position du perso
  select into v_type_lieu lieu_tlieu_cod
  from perso_position,lieu_position,lieu
  where ppos_perso_cod = personnage
        and lpos_pos_cod = ppos_pos_cod
        and lpos_lieu_cod = lieu_cod
        and lieu_tlieu_cod in (15,23,27);
  if not found then
    code_retour := 'Anomalie ! Vous ne pouvez pas acheter ces objets ici !';
    return code_retour;
  end if;
  select into v_poids,v_poids_max,v_po
    get_poids(personnage),perso_enc_max,perso_po
  from perso
  where perso_cod = personnage;
  if not found then
    code_retour := 'Anomalie ! Personnage non trouvé !';
    return code_retour;
  end if;
  if v_objet < 1 then
    code_retour := 'Anomalie ! paramètre objet incorrect !';
    return code_retour;
  end if;
  if v_objet > 2 then
    code_retour := 'Anomalie ! paramètre objet incorrect !';
    return code_retour;
  end if;
  if v_objet = 1 then
    v_prix := 0;
    v_objet_generique := 196;
  end if;
  if v_objet = 2 then
    v_prix := 2000;
    v_objet_generique := 186;
  end if;
  select into v_poids_objet,v_nom_objet gobj_poids,gobj_nom
  from objet_generique
  where gobj_cod = v_objet_generique;
  if ((v_poids + v_poids_objet) > (v_poids_max * 3))	then
    v_poids_max := v_poids_max * 3;
    code_retour := '<p>Vous ne pouvez acheter un objet qui vous fait dépasser '||trim(to_char(v_poids_max,'99999999'))||' d''encombrement.';
    return code_retour;
  end if;
  if v_po < v_prix then
    code_retour := 'Anomalie ! Pas assez de brouzoufs pour acheter cet objet !';
    return code_retour;
  end if;
  --
  -- à partir d'ici, normalement, tout est bon, on peut passer à la suite
  --
  update perso set perso_po = perso_po - v_prix where perso_cod = personnage;
  temp_txt := cree_objet_perso_nombre(v_objet_generique,personnage,1);
  code_retour := 'Vous venez d''acheter l''objet <b>'||v_nom_objet||'</b>, il est maintenant dans votre inventaire.';
  texte_evt := '[perso_cod1] a acheté l''objet '||v_nom_objet||'.';
  insert into ligne_evt(levt_tevt_cod,levt_date,levt_perso_cod1,levt_texte,levt_lu,levt_visible)
  values(44,now(),personnage,texte_evt,'O','N');
  if v_objet = 1 then
    update parametres set parm_valeur = parm_valeur + 1 where parm_cod in (76,77);
  end if;
  if v_objet = 2 then
    update parametres set parm_valeur = parm_valeur + 1 where parm_cod in (80,81);
  end if;
  return code_retour;
end;

$_$;


ALTER FUNCTION public.achete_objet(integer, integer) OWNER TO delain;
