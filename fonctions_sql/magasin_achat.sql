CREATE OR REPLACE FUNCTION public.magasin_achat(integer, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/**************************************************************************/
/* fonction magasin_achat : gère l'achat dans un magasin par un joueur    */
/* On passe en paramètres :                                               */
/*   $1 = perso_cod du joueur                                             */
/*   $2 = lieu_cod du magasin                                             */
/*   $3 = obj_cod de l'objet acheté                                       */
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
	qte_or integer;					-- nb de brouzoufs du joueur
	pos_joueur integer;				-- position du joueur
	karma_perso numeric;			-- karma du joueur
----------------------------------------------------------------------------
-- variable concernant le magasin
----------------------------------------------------------------------------
	magasin alias for $2;			-- lieu_cod du magasin
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
	prix_objet2 integer;				-- prix de l'objet
	prix_init integer;				-- prix de l'objet hors modificateur
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
	select into pos_magasin
		lpos_pos_cod
		from lieu_position
		where lpos_lieu_cod = magasin;
	if not found then
		code_retour := '<p>Erreur ! Position magasin non trouvéé !';
		return code_retour;
	end if;
	if pos_joueur != pos_magasin then
		code_retour := '<p>Erreur ! Position magasin et joueur non concordantes !';
		return code_retour;
	end if;		
	
	select into temp
		mstock_obj_cod
		from stock_magasin
		where mstock_lieu_cod = magasin
		and mstock_obj_cod = num_objet;
	if not found then
		code_retour := '<p>Erreur ! L''objet ne fait partie de l''inventaire du magasin !';
		return code_retour;
	end if;
	prix_objet2 := f_prix_obj_perso_a(personnage,magasin,num_objet);

	select into nom_objet obj_nom
		from objets
		where obj_cod = num_objet;
	if prix_objet2 > qte_or then
		code_retour := '<p>Erreur ! Vous n''avez pas assez de brouzoufs pour acheter cet objet !';
		return code_retour;
	end if;
----------------------------------------------------------------------------
-- Etape 2 : on passe à la vente 
----------------------------------------------------------------------------
-- on stocke
	insert into mag_tran
		(mtra_lieu_cod,mtra_perso_cod,mtra_obj_cod,mtra_sens,mtra_montant)
		values
		(magasin,personnage,num_objet,1,prix_objet2);
-- on enlève les brouzoufs
	update perso
		set perso_po = perso_po - prix_objet2
		where perso_cod = personnage;
-- on passe aux marges
	prix_origine = (prix_objet2*100)/(100 + marge);
	montant_marge = round((prix_objet2 * marge)/100);
	montant_prelev = round((prix_objet2 * prelev) / 100);
-- on met la marge au magasin
	update lieu set lieu_compte = lieu_compte + montant_marge where lieu_cod = magasin;
	update lieu set lieu_compte = lieu_compte - montant_prelev where lieu_cod = magasin;
	update parametres set parm_valeur = parm_valeur + montant_prelev where parm_cod = 39;

-- on retire de l'inventaire du magasin
	delete from stock_magasin
		where mstock_obj_cod = num_objet;
-- on rajoute dans l'inventaire
	insert into perso_objets
		(perobj_obj_cod,perobj_perso_cod,perobj_identifie,perobj_equipe)
		values
		(num_objet,personnage,'O','N');
-- on rajoute une transaction dans le total
	select into temp pvmag_cod
		from perso_visite_magasin
		where pvmag_perso_cod = personnage
		and pvmag_lieu_cod = magasin;
	if not found then
		insert into perso_visite_magasin
			(pvmag_perso_cod,pvmag_lieu_cod,pvmag_nombre)
			values
			(personnage,magasin,(prix_objet2/5000)::numeric);
	else
		update perso_visite_magasin
			set pvmag_nombre = pvmag_nombre + (prix_objet2/5000)::numeric
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
	code_retour := '<p>Vous avez acheté l''objet '||nom_objet||' pour la somme de '||trim(to_char(prix_objet2,'99999999999'))||' brouzoufs.';
	return code_retour;
end;
$function$

