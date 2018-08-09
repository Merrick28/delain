CREATE OR REPLACE FUNCTION public.entrer_gm(integer)
 RETURNS void
 LANGUAGE plpgsql
AS $function$/* *********************************************** */
/* Fonction entrer_gm                              */
/*                                                 */
/* Met dans le sac un aventurier ayant subit       */
/*   l attaque spéciale Garde Manger.              */
/*                                                 */
/* Paramètres :                                    */
/*   $1 = perso_cod                                */
/* *********************************************** */
/* Créé le 08/11/2010                              */
/* Modifié le 04/04/2011 (Maverick)                */
/*     |-> Ajout de la requête de suppression      */
/*     |   des locks de combat.                    */
/* *********************************************** */

declare
	in_perso_cod		alias for $1;	-- Numéro de perso
	v_back_pos		integer;	-- Position de retour
	v_perso_total_vue	integer;	-- Vue + améliorations + marge de sécu
	new_pos			integer;	-- Code position du garde manger en x0 y0
	
begin
	-- Initialisation
	new_pos := 152387;
	
	-- Récupération des données
	select into 
		v_back_pos 
		ppos_pos_cod 
	from	perso_position 
	where	ppos_perso_cod = in_perso_cod;
	
	select into 
		v_perso_total_vue 
		((perso_vue + perso_amelioration_vue +10)*-1) as total 
	from	perso 
	where	perso_cod = in_perso_cod;
	
	-- Enregitrement de la position de retour
	insert	into perso_gmanger ( 
		pgm_perso_cod, 
		pgm_pos_cod, 
		pgm_date_entree 
	) 
	values ( 
		in_perso_cod, 
		v_back_pos, 
		now() 
	);
	
        -- Ajout du malus de pvie par tour pour asphyxie du garde manger
        perform ajoute_bonus(in_perso_cod, 'DGM', 2, 10);
        
        -- Ajout du malus de vue
        perform ajoute_bonus(in_perso_cod, 'VUE', 20, v_perso_total_vue);
        
        -- Perte du reste des points d'action
        update	perso 
        set	perso_pa = 0 
        where	perso_cod = in_perso_cod;
        
	-- Déplacement du personnage dans le sac
	update	perso_position 
	set	ppos_pos_cod = new_pos 
	where	ppos_perso_cod = in_perso_cod;
        
	-- Suppression des locks de combat
	delete	from lock_combat 
	where	lock_attaquant = in_perso_cod 
	or	lock_cible = in_perso_cod;
	
end;$function$

