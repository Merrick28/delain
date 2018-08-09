CREATE OR REPLACE FUNCTION public.magasin_destocker(integer, integer, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/**************************************************************************/
/* fonction magasin_destockage : Un gérant retire des objets dans son stock */
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
	
	select into nom_objet,prix_init
		gobj_nom,gobj_valeur from objet_generique 		
		where gobj_cod = num_objet;
	prix_total := prix_init * nombre_obj;	
			
	
	select into num_init
		mgstock_nombre
	from stock_magasin_generique 
	where mgstock_gobj_cod = num_objet and mgstock_lieu_cod = v_magasin;
	
	if not found then
		code_retour := '<p> Erreur ! Vous n''avez pas assez d''objets de ce type en stock !';
		return code_retour;
	else
		if nombre_obj > num_init then
			code_retour := '<p> Erreur ! Vous n''avez pas assez d''objets de ce type en stock !';
			return code_retour;
		end if;
		-- On RETIRE les BRs
	
		update lieu set lieu_compte = lieu_compte + prix_total where lieu_cod = v_magasin;
	
		-- MAJ DU STOCK
		
		update stock_magasin_generique
			set mgstock_nombre = mgstock_nombre - nombre_obj
			where mgstock_gobj_cod = num_objet
			and mgstock_lieu_cod = v_magasin;
  -- AJOUT DANS LE LOG
  insert into mag_tran_generique(mgtra_lieu_cod,mgtra_perso_cod,mgtra_gobj_cod,mgtra_sens,mgtra_montant,mgtra_nombre)
		values
		(v_magasin,personnage,num_objet,3,prix_total,nombre_obj);
 
	end if;
	
	code_retour := '<p>Vous avez vendu <b>'||CAST(nombre_obj AS text)||' '||nom_objet||'</b> pour une somme totale de <b>'||CAST(prix_total AS text)||'Br</b></p>';
	
	return code_retour;
end;	
$function$

