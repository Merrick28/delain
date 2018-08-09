CREATE OR REPLACE FUNCTION public.magasin_stocker(integer, integer, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/**************************************************************************/
/* fonction magasin_stockage : Un gérant ajoute des objets dans son stock */
/* On passe en paramètres :                                               */
/*   $1 = perso_cod du gérant                                             */
/*   $2 = lieu_cod du magasin                                             */
/*   $3 = obj_cod de l'objet acheté                                       */
/*   $3 = nombre_obj nombre d'objets                                      */
/* On a retour une chaine HTML exploitable par action.php                 */
/**************************************************************************/
/* Créé le 08/03/2004                                                     */
/* ATTENTION ! NE CONCERNE QUE LES OBJETS MODIFIES                        */
/**************************************************************************/
declare
----------------------------------------------------------------------------
-- variable de retour 
----------------------------------------------------------------------------
	code_retour text;					-- texte formatté
----------------------------------------------------------------------------
-- variable concernant le joueur
----------------------------------------------------------------------------	
	personnage alias for $1;		-- perso_cod du joueur
----------------------------------------------------------------------------
-- variable concernant le magasin
----------------------------------------------------------------------------
	v_magasin alias for $2;			-- lieu_cod du magasin
	v_mag_caisse integer;
	pos_magasin integer;				-- position du magasin
	montant_marge integer;			-- marge encaissée par le magasin
	montant_prelev integer;			-- montant reversé à l'administration
	prix_origine integer;			-- prix sans marge
	marge integer;						-- marge du magasin
	prelev integer;					-- % de reversement
----------------------------------------------------------------------------
-- variable concernant l'objet
----------------------------------------------------------------------------
	num_objet alias for $3;			-- obj_cod de l'objet acheté
	nombre_obj alias for $4;			-- obj_cod de l'objet acheté
	prix_total integer;				-- prix total de la transaction
	prix_init integer;				-- prix de l'objet hors modificateur
	num_init integer;				-- nombre d'objets deja en stock
	nom_objet text;					-- nom de l'objet
----------------------------------------------------------------------------
-- variable de calcul
----------------------------------------------------------------------------
	temp integer;						-- fourre tout
	modificateur numeric;			-- modif du prix de vente
	bonus_prix integer;
begin
----------------------------------------------------------------------------
-- Etape 1 : vérification 
----------------------------------------------------------------------------
	
	select into temp,marge,prelev ,v_mag_caisse
			lieu_cod,lieu_marge,lieu_prelev,lieu_compte
		from lieu
		where lieu_cod = v_magasin;
	if not found then
		code_retour := '<p>Erreur ! Magasin non trouvé !';
		return code_retour;
	end if;	
	
	select into nom_objet,prix_init,num_init
		gobj_nom,gobj_valeur,mgstock_nombre from objet_generique 
		LEFT OUTER JOIN stock_magasin_generique ON (gobj_cod = mgstock_gobj_cod and mgstock_lieu_cod = v_magasin)
		where gobj_cod = num_objet;
	prix_total := prix_init * nombre_obj;
	
	if prix_total > v_mag_caisse then
		code_retour := '<p>Erreur ! Vous n''avez pas assez de brouzoufs en caisse pour acheter ces objets !';
		return code_retour;
	end if;
	
	-- On RETIRE les BRs
	
	update lieu set lieu_compte = lieu_compte - prix_total where lieu_cod = v_magasin;
	
	-- MAJ DU STOCK
	
	select into num_init
		mgstock_nombre
	from stock_magasin_generique 
	where mgstock_gobj_cod = num_objet and mgstock_lieu_cod = v_magasin;
	if not found then
		insert into stock_magasin_generique
			(mgstock_gobj_cod,mgstock_lieu_cod,mgstock_nombre)
			values
			(num_objet,v_magasin,nombre_obj);
	else
		update stock_magasin_generique
			set mgstock_nombre = mgstock_nombre + nombre_obj
			where mgstock_gobj_cod = num_objet
			and mgstock_lieu_cod = v_magasin;
	end if;
	
  -- AJOUT DANS LE LOG
  insert into mag_tran_generique
		(mgtra_lieu_cod,mgtra_perso_cod,mgtra_gobj_cod,mgtra_sens,mgtra_montant,mgtra_nombre)
		values
		(v_magasin,personnage,num_objet,2,prix_total,nombre_obj);
 

	code_retour := '<p>Vous avez acheté <b>'||CAST(nombre_obj AS text)||' '||nom_objet||'</b> pour une somme totale de <b>'||CAST(prix_total AS text)||'Br</b></p>';
	
	return code_retour;
end;	
$function$

