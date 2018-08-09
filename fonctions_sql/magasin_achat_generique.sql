CREATE OR REPLACE FUNCTION public.magasin_achat_generique(integer, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/**************************************************************************/
/* fonction magasin_achat_generique : gère l'achat dans un magasin par un */ 
/* joueur.                                                                */
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
	objet_reserve text;			-- objet appartenant à la réserve
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
	tmp_txt text;
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
	
	select into temp,objet_reserve
		mgstock_nombre,mgstock_vente_persos
		from stock_magasin_generique
		where mgstock_lieu_cod = magasin
		and mgstock_gobj_cod = num_objet
		and mgstock_nombre >= 1;
	if not found then
		code_retour := '<p>Erreur ! L''objet ne fait partie de l''inventaire du magasin !';
		return code_retour;
	elsif objet_reserve != 'O' then
		code_retour := '<p>Erreur ! Ce type d''objet appartient à la réserve et ne peut pas être acheté sans action du gérant !';
		return code_retour;
	end if;
	prix_objet2 := f_prix_obj_perso_a_generique(personnage,magasin,num_objet);

	select into nom_objet gobj_nom
		from objet_generique
		where gobj_cod = num_objet;
	if prix_objet2 > qte_or then
		code_retour := '<p>Erreur ! Vous n''avez pas assez de brouzoufs pour acheter cet objet !';
		return code_retour;
	end if;
----------------------------------------------------------------------------
-- Etape 2 : on passe à la vente 
----------------------------------------------------------------------------
-- on stocke pour les stats
	insert into mag_tran_generique
		(mgtra_lieu_cod,mgtra_perso_cod,mgtra_gobj_cod,mgtra_sens,mgtra_montant,mgtra_nombre)
		values
		(magasin,personnage,num_objet,1,prix_objet2,1);
 
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
	update stock_magasin_generique
		set mgstock_nombre = mgstock_nombre - 1
		where mgstock_lieu_cod = magasin
		and mgstock_gobj_cod = num_objet;
-- on vérifie que le stock soit pas égal à 0
	select into temp
		mgstock_nombre
		from stock_magasin_generique
		where mgstock_lieu_cod = magasin
		and mgstock_gobj_cod = num_objet;
	if temp = 0 then
		delete from stock_magasin_generique
			where mgstock_lieu_cod = magasin
			and mgstock_gobj_cod = num_objet;
	end if;
-- on rajoute dans l'inventaire
	tmp_txt := cree_objet_perso_nombre(num_objet,personnage,1);
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

