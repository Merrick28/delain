CREATE OR REPLACE FUNCTION public.deb_tour_rouille(integer, integer, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/************************************************/
/* deb_tour_rouille : agit sur l'usure de       */
/*                    l'équipement d'une cible  */
/*                    en début de tour          */
/* $1 : personnage qui agit                     */
/* $2 : chances de déclenchement                */
/* $3 : impact sur le matériel (degré d'usure)  */ 
/* $4 : type d'effet pour le texte de sortie    */
/*      (acide, rouille ...)                    */
/************************************************/
/* Création : 22/08/2007 RMT                    */
/************************************************/


declare
	personnage alias for $1;
	chance alias for $2;
	impact alias for $3;
	effet alias for $4;
	v_pos integer;
	ligne record;
	texte_evt text;
	cible integer;
	cible_nom text;
	obj_cible integer;			-- objet de la cible qui va rouiller
	obj_cible_etat numeric;			-- impact après rouille
	obj_cible_nom text;			-- nom de l'objet cible
	compt integer;					-- fourre tout


/************************************************/
begin
	select into v_pos
		ppos_pos_cod
		from perso_position 
		where ppos_perso_cod = personnage;
	if not found then
		return 'Anomalie sur position !';
	end if;
if lancer_des(1,100) < chance then
--sélection de la cible potentielle
	select into cible,cible_nom perso_cod,perso_nom
			from perso,perso_position
			where perso_type_perso in (1,3)
			and perso_actif = 'O'
			and perso_tangible = 'O'
			and ppos_perso_cod = perso_cod
			and ppos_pos_cod = v_pos
			and not exists
			(select 1 from lieu,lieu_position
			where lpos_pos_cod = ppos_pos_cod
			and lpos_lieu_cod = lieu_cod
			and lieu_refuge = 'O')
			order by random()
			limit 1;
--La cible est sélectionnée, on récupère ses objets

	select into obj_cible,obj_cible_etat,obj_cible_nom obj_cod,obj_etat,obj_nom from objets,perso_objets 
		where perobj_perso_cod = cible
		and perobj_equipe = 'O' 
		and perobj_obj_cod = obj_cod
		order by random()
		limit 1;

	if obj_cible is null then
		return 'OK';
	end if;
--L'objet cible est impacté
	obj_cible_etat := obj_cible_etat - impact;
--L'objet se détruit
	if obj_cible_etat <= 0 then
		texte_evt := 'Votre '||obj_cible_nom||' a fini en morceaux !';
				
		compt := f_del_objet(obj_cible);

		insert into ligne_evt(levt_tevt_cod,levt_date,levt_perso_cod1,levt_texte,levt_lu,levt_visible)
					values(75,now(),cible,texte_evt,'N','O');

--l'objet résiste
	else
		update objets set obj_etat = obj_cible_etat where obj_cod = obj_cible;
		if effet = 1 then
------------------------------
-- effet d'acide
			texte_evt := 'Votre '||obj_cible_nom||' a subit des dégâts dus à un jet d''acide, rendant son utilisation plus hasardeuse sans réparation !';


		elsif effet = 2 then
------------------------------
-- effet projection de tentacules
			texte_evt := 'Deux tentacules projetées par [attaquant] viennent perforer votre '||obj_cible_nom||', attaquant sa résistance ! Vous assistez impuissant à cette agression, espérant qu''elle ne se reproduira pas';

		
		elsif effet = 3 then
------------------------------
-- effet Lave / Flammes
			texte_evt := 'Un jet de flamme part directement du [attaquant] endommageant fortement votre '||obj_cible_nom;

		elsif effet = 4 then
------------------------------
-- effet ectoplasmique
			texte_evt := 'Vous êtes traversé de part en part par [attaquant]. Bizarrement, vous ne ressentez aucune douleur, en revanche, vous voyez votre '||obj_cible_nom||' s''effriter ...';

		else
------------------------------
--texte générique d'effet
			texte_evt := 'Votre '||obj_cible_nom||' a subit des dégâts, rendant son utilisation plus hasardeuse sans réparation !';
		end if;
insert into ligne_evt(levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
     				values(75,now(),1,personnage,texte_evt,'O','N',personnage,cible);
   			insert into ligne_evt(levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
     			values(75,now(),1,cible,texte_evt,'N','N',personnage,cible);
	end if;
end if;
return 'OK';
end;$function$

