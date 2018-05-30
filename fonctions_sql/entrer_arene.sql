--
-- Name: entrer_arene(integer, integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION entrer_arene(integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
    AS $_$/* fonction entrer_arene                                     */
/*                                                           */
/*   on passe en paramètres :                                */
/*   $1 = perso_cod                                          */
/*   $2 = etage_numero                                       */
/*   $3 = pos_cod                                            */
/*************************************************************/
/* Créé le 18/09/2010                                        */
/*	   par Morgenese                                     */
/*************************************************************/

/*************************************************************/
/* Modifié le 17/12/2010 par Maverick                        */
/* pour l animation "Salon d Automne Herbes et Pierres"      */
/* qui se déroule dans l Arène (beta) jusqu au 09/01/11.     */
/* Tous les perso doivent être impalpables pour éviter       */
/* les combat durant l animation.                            */
/* Modifié le 07/04/2011 par Bleda : Fuite si locks          */
/*************************************************************/

declare

	v_perso alias for $1;
	v_etage alias for $2;
	v_pos_depart alias for $3;
	v_nombre_entree integer;
	des_entree integer;
	v_pa integer;
	v_new_pos integer;
	v_tangible text;
        v_type_perso integer;
        v_level_max integer;
        v_level_perso integer;
	v_familier integer;
        texte_evt text;
        texte_fuite text;

        -- Ajouté pour l anim
        imp_actif integer; -- 1 = Impalpable ON
        imp_nbtour integer; -- Nombre de tour impalpable
        fam_actif integer; -- familier actif paramétré pour l'étage ou non

begin

-- Paramètres pour l animation
imp_actif := 0;
imp_nbtour := 100;


/**************************************************/
/* on vérifie les pa et l impalpabilité           */
/**************************************************/


select into v_pa, v_tangible, v_type_perso perso_pa, perso_tangible, perso_type_perso from perso
where perso_cod = v_perso;

if v_pa < 4 then
	return '1;Vous n''avez pas assez de PA pour rentrer dans l''arène.';
end if;

if v_tangible = 'N' then
	return '1;Vous ne pouvez pas entrer dans l''arène en étant impalpable.';
end if;

if v_type_perso = 3 then
        return '1;Un familier ne peut pas se déplacer seul.';
end if;

-- on vérifie que le perso peut entrer dans l'arène en fonction de son niveau
select into v_level_max carene_level_max from carac_arene
where carene_etage_numero = v_etage;

if v_level_max > 0 then
   select into v_level_perso perso_niveau from perso
   where perso_cod = v_perso;

   if v_level_perso > v_level_max then
        return '1;Vous ne pouvez pas entrer dans l''arène car votre niveau est trop élevé.';
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

-- on regarde si le perso a un familier. Si oui, on le met impalpale

select into v_familier max(pfam_familier_cod) from perso_familier
where pfam_perso_cod = v_perso;

if found and v_familier != 2201872 then
        select into fam_actif etage_familier_actif from etage where etage_cod = v_etage;
        if fam_actif < 1 then
             	update perso set perso_tangible = 'N', perso_nb_tour_intangible = 9999
	        where perso_cod = v_familier;
        end if;
end if;

/**************************************************/
/* on insère les données du perso dans perso_arene*/
/**************************************************/

insert into perso_arene (parene_perso_cod, parene_etage_numero, parene_pos_cod, parene_date_entree)
values (v_perso, v_etage, v_pos_depart, now());

texte_evt := 'Entrée en arène';
	insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
		values(nextval('seq_levt_cod'),86,now(),1,v_perso,texte_evt,'O','O',null,null);

/**************************************************/
/* On calcule la position d'entrée dans l'arène   */
/**************************************************/

-- calcul du nombre d'entrées de l'arène
select into v_nombre_entree count(*) from positions
where pos_etage = v_etage
and pos_entree_arene = 'O';

-- choix de l'entrée aléatoire
des_entree := lancer_des(1,v_nombre_entree);
des_entree := des_entree - 1;

select into v_new_pos pos_cod
from positions
where pos_etage = v_etage
and pos_entree_arene = 'O'
offset des_entree
limit 1;

-- déplacement du joueur
update perso_position
set ppos_pos_cod = v_new_pos
where ppos_perso_cod = v_perso;

-- on retire les PA
update perso
set perso_pa = perso_pa - 4
where perso_cod = v_perso;

-- Pour les besoins de l animation
if imp_actif = 1 and v_etage=87 then
  update perso
  set perso_tangible = 'N', perso_nb_tour_intangible = imp_nbtour
  where perso_cod = v_perso;
return '0;' || texte_fuite || '<p><b>Salon d''Automne Herbes et Pierres</b></p><p>Dans cette arène, ouverture pour quelques courtes semaines, d''une grande vente aux herbes et pierres de toutes sortes, des plus rares, aux plus savoureuses et scintillantes !!!</p>';

else

return '0;' || texte_fuite || 'Vous entrez dans une arène de combat : ici, pas de réforme pvp, pas de drop ni de calcul de kharma. Les gains d''expérience sont doublés.';

end if;
end;$_$;


ALTER FUNCTION public.entrer_arene(integer, integer, integer) OWNER TO delain;
