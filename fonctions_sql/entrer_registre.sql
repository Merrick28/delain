-- Function: public.entrer_registre(integer)

-- DROP FUNCTION public.entrer_registre(integer);

CREATE OR REPLACE FUNCTION public.entrer_registre(
    integer)
  RETURNS text AS
$BODY$/* fonction entrer_registre                            */
/*                                                           */
/*   on passe en paramètres :                                */
/*   $1 = perso_cod                                          */
/*************************************************************/
/* Créé le 13/02/2018                                        */
/*	   par Marlyza (c) LAG                                   */
/*************************************************************/

/*************************************************************/
/* Modifié le xx/xx/xxxx par                                 */
/*************************************************************/

declare

	v_perso alias for $1;
	v_pa integer;
	v_preg_pos_cod integer;
	v_pos_depart integer;
	v_etage_arene text;    -- type de la position de départ arene ou dongeon
	v_tangible text;
  v_type_perso integer;
  v_level_max integer;
  v_level_min integer;
  v_level_perso integer;
	v_familier integer;
	v_etage integer;
  texte_evt text;
  texte_fuite text;
  fam_actif integer; -- familier actif paramétré pour l'étage ou non

begin



/***************************************************/
/* on vérifie que le perso à les droits de rentrer */
/***************************************************/

select into v_pa, v_tangible, v_type_perso, v_level_perso, v_pos_depart, v_preg_pos_cod, v_level_max, v_level_min, v_etage
    perso_pa, perso_tangible, perso_type_perso, perso_niveau, ppos_pos_cod, preg_pos_cod, COALESCE(carene_level_max,0), COALESCE(carene_level_min,0), pos_etage
    from perso
    join perso_position on ppos_perso_cod=perso_cod
    join perso_registre on preg_perso_cod=perso_cod
    join positions on pos_cod=preg_pos_cod
    join carac_arene on carene_etage_numero=pos_etage
    where perso_cod = v_perso;
if not found then
   return '1;Vous ne pouvez pas entrer dans l''arène, le préposé ne trouve pas votre nom sur le registre!';
end if;

/* retour gratuit par le registre d'inscription
if v_pa < 4 then
	return '1;Vous n''avez pas assez de PA pour rentrer dans l''arène.';
end if; */

/* retour intangible possible, car si on utilise le registre c'est qu'on vient de décéder
if v_tangible = 'N' then
	return '1;Vous ne pouvez pas entrer dans l''arène en étant impalpable.';
end if;*/

if v_type_perso = 3 then
        return '1;Un familier ne peut pas se déplacer seul.';
end if;

-- on vérifie que le perso peut entrer dans l'arène en fonction de son niveau
if v_level_max > 0 then
   if v_level_perso > v_level_max then
        return '1;Vous ne pouvez pas entrer dans l''arène car votre niveau est trop élevé.';
   end if;
end if;

if v_level_min > 0 then
   if v_level_perso < v_level_min then
        return '1;Vous ne pouvez pas entrer dans l''arène car votre niveau est trop faible.';
   end if;
end if;

texte_fuite := '';
if exists (select 1 from lock_combat where lock_cible = v_perso) then
    texte_fuite := fuite(v_perso);
    if split_part(texte_fuite, '#', 1) = '1' then
        -- Échec.
        return '1;' || split_part(texte_fuite, '#', 2);
    else
        texte_fuite := split_part(texte_fuite, '#', 2);
    end if;
end if;

-- on regarde si le perso a un familier. Si oui, on le met impalpable dans certaines arènes
select into v_familier max(pfam_familier_cod) from perso_familier
where pfam_perso_cod = v_perso;
if found then
        select into fam_actif etage_familier_actif from etage where etage_cod = v_etage;
        if fam_actif < 1 then
             	update perso set perso_tangible = 'N', perso_nb_tour_intangible = 9999
	        where perso_cod = v_familier;
        end if;
end if;

/**************************************************/
/* on insère les données du perso dans perso_arene*/
/**************************************************/
-- On verifie si le perso n'avait pas déjà une sortie d'arène de programmée
-- Cela ne devrait pas arriver, mais il y en a !!
select into v_etage_arene etage_arene from perso_arene
  inner join perso_position on ppos_perso_cod = parene_perso_cod
  inner join positions on pos_cod = ppos_pos_cod
  inner join etage on etage_numero = pos_etage
  where parene_perso_cod=v_perso ;
if found then
  if v_etage_arene = 'O' then
      -- D’une étage arène vers une arène
    update perso_arene set parene_etage_numero = v_etage where parene_perso_cod = v_perso ;
  else
    -- D’un étage normal vers une arène
    delete from perso_arene where parene_perso_cod = v_perso ;
    insert into perso_arene (parene_perso_cod, parene_etage_numero, parene_pos_cod, parene_date_entree)
      values (v_perso, v_etage, v_pos_depart, now());
  end if;
else
  -- cas normal, le perso n'avait pas de sortie d'arene de definie
  insert into perso_arene (parene_perso_cod, parene_etage_numero, parene_pos_cod, parene_date_entree)
  values (v_perso, v_etage, v_pos_depart, now());
end if;

texte_evt := 'On a retrouvé le nom de [perso_cod1] dans les registres, [perso_cod1] est renvoyé au batiment d''inscription.';
	insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
		values(nextval('seq_levt_cod'),86,now(),1,v_perso,texte_evt,'O','O',null,null);

/**************************************************/
/* On le positionne sur son lieu d'inscription    */
/**************************************************/

-- déplacement du joueur
update perso_position
set ppos_pos_cod = v_preg_pos_cod
where ppos_perso_cod = v_perso;

/*-- on retire les PA
update perso
set perso_pa = perso_pa - 4
where perso_cod = v_perso;*/

return '0;' || texte_fuite || 'Le préposé a retrouvé votre nom dans les registres, vous êtes renvoyé au batiment d''inscription.';

end;$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION public.entrer_arene(integer, integer, integer)
  OWNER TO delain;
