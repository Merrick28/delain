CREATE OR REPLACE FUNCTION public.magasin_identifie(integer, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/**************************************************************************/
/* fonction magasin_identifie : gère l'identification d'un objet dans un  */
/*   magasin                                                              */
/* On passe en paramètres :                                               */
/*   $1 = perso_cod du joueur                                             */
/*   $2 = lieu_cod du magasin                                             */
/*   $3 = obj_cod de l'objet acheté                                       */
/* On a retour une chaine HTML exploitable par action.php                 */
/**************************************************************************/
/* Créé le 08/03/2004                                                     */
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
	qte_or integer;					-- nb de brouzoufs du joueur
	pos_joueur integer;
	karma_perso numeric;			-- karma du joueur

----------------------------------------------------------------------------
-- variable concernant le magasin
----------------------------------------------------------------------------
	magasin alias for $2;			-- lieu_cod du magasin
	pos_magasin integer;
		montant_marge integer;			-- marge encaissée par le magasin
	montant_prelev integer;			-- montant reversé à l'administration
	prix_origine integer;			-- prix sans marge
	marge integer;						-- marge du magasin
	prelev integer;					-- % de reversement
----------------------------------------------------------------------------
-- variable concernant l'objet
----------------------------------------------------------------------------
	num_objet alias for $3;			-- obj_cod de l'objet acheté
	prix_objet integer;				-- prix de l'objet
	prix_init integer;				-- prix de l'objet hors modificateur
	nom_objet text;					-- nom de l'objet
----------------------------------------------------------------------------
-- variable de calcul
----------------------------------------------------------------------------
	temp integer;						-- fourre tout
	modificateur numeric;			-- modif du prix de vente
	cout integer;
begin
----------------------------------------------------------------------------
-- Etape 1 : vérification 
----------------------------------------------------------------------------
	select into qte_or,karma_perso perso_po,perso_kharma
		from perso
		where perso_cod = personnage;
	if not found then
		code_retour := '<p>Erreur ! Acheteur non trouvé !';
		return code_retour;
	end if;
	select into pos_joueur
		ppos_pos_cod
		from perso_position
		where ppos_perso_cod = personnage;
	if not found then
		code_retour := '<p>Erreur ! Position acheteur non trouvé !';
		return code_retour;
	end if;

	select into temp,marge,prelev lieu_cod,lieu_marge,lieu_prelev
		from lieu
		where lieu_cod = magasin;
	if not found then
		code_retour := '<p>Erreur ! Magasin non trouvé !';
		return code_retour;
	end if;
	select into temp
		perobj_cod
		from perso_objets
		where perobj_perso_cod = personnage
		and perobj_obj_cod = num_objet;
	if not found then
		code_retour := '<p>Erreur ! L''objet n''est pas dans votre inventaire !';
		return code_retour;
	end if;
	if qte_or < (100 + marge) then
		code_retour := '<p>Erreur ! Vous n''avez pas assez de brouzoufs pour faire identifier cet objet !';
		return code_retour;
	end if;
	select into prix_init,nom_objet obj_valeur,obj_nom
		from objets
		where obj_cod = num_objet;
----------------------------------------------------------------------------
-- Etape 2 : on passe à l'identification
----------------------------------------------------------------------------
-- on ajoute les brouzoufs
	update perso
		set perso_po = perso_po - 100 - marge
		where perso_cod = personnage;
	update lieu set lieu_compte = lieu_compte + marge - prelev where lieu_cod = magasin;
	update parametres set parm_valeur = parm_valeur + prelev where parm_cod = 39;
		
-- on update dans l'inventaire
	update perso_objets
		set perobj_identifie = 'O'
		where perobj_obj_cod = num_objet;
-- on rajoute une transaction dans le total
	select into temp pvmag_cod
		from perso_visite_magasin
		where pvmag_perso_cod = personnage
		and pvmag_lieu_cod = magasin;
	if not found then
		insert into perso_visite_magasin
			(pvmag_perso_cod,pvmag_lieu_cod,pvmag_nombre)
			values
			(personnage,magasin,1);
	else
		update perso_visite_magasin
			set pvmag_nombre = (pvmag_nombre + 0.02)::numeric
			where pvmag_perso_cod = personnage
			and pvmag_lieu_cod = magasin;
	end if;
-- on modifie l'alignement
	if karma_perso < 0 then
		update lieu
		set lieu_alignement = lieu_alignement - 1
		where lieu_cod = magasin;
	else
		update lieu
		set lieu_alignement = lieu_alignement + 1
		where lieu_cod = magasin;		
	end if;
	cout := 100 + marge;
	code_retour := '<p>Vous avez fait identifier l''objet '||nom_objet||' pour la somme de '||trim(to_char(cout,'9999'))||' brouzoufs.';
	return code_retour;
end;


	
	$function$

