CREATE OR REPLACE FUNCTION public.magasin_valider_transaction(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/**************************************************************************/
/* fonction magasin_destockage : Un gérant retire des objets dans son stock */
/* On passe en paramètres :                                               */
/*   $1 = numero de la transaction                                             */
/* On a retour une chaine HTML exploitable par action.php                 */
/**************************************************************************/
declare

v_transaction_cod alias for $1;		-- perso_cod du joueur
----------------------------------------------------------------------------
-- variable de retour
----------------------------------------------------------------------------
	code_retour text;					-- texte formatté
----------------------------------------------------------------------------
-- variable concernant le joueur
----------------------------------------------------------------------------
	personnage integer;		-- perso_cod du joueur
	qte_or integer;
----------------------------------------------------------------------------
-- variable concernant le magasin
----------------------------------------------------------------------------
	v_magasin integer;			-- lieu_cod du magasin
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
	num_objet integer;		-- obj_cod de l'objet acheté
	nombre_obj integer;			-- obj_cod de l'objet acheté
	prix_total integer;				-- prix total de la transaction
	prix_init integer;				-- prix de l'objet hors modificateur
	num_init integer;				-- nombre d'objets deja en stock
	nom_objet text;					-- nom de l'objet
----------------------------------------------------------------------------
-- variable de calcul
----------------------------------------------------------------------------
	temp integer;						-- fourre tout
	tmp_txt text;
	modificateur numeric;			-- modif du prix de vente
	bonus_prix integer;
	v_transaction_prix integer;
begin
----------------------------------------------------------------------------
-- Etape 1 : vérification
----------------------------------------------------------------------------

  select into num_objet,v_magasin,personnage,v_transaction_prix,nombre_obj
   tran_gobj_cod,tran_vendeur,tran_acheteur,tran_prix,tran_quantite from transaction_echoppe
   where tran_cod = v_transaction_cod;


	select into temp,marge,prelev ,v_mag_caisse
			lieu_cod,lieu_marge,lieu_prelev,lieu_compte
		from lieu
		where lieu_cod = v_magasin;
	if not found then
		code_retour := '<p>Erreur ! Magasin non trouvé !';
		return code_retour;
	end if;

  select into qte_or perso_po
		from perso
		where perso_cod = personnage;
	if not found then
		code_retour := '<p>Erreur ! Acheteur non trouvé !';
		return code_retour;
	end if;

  if v_transaction_prix > qte_or then
		code_retour := '<p>Erreur ! Vous n''avez pas assez de brouzoufs pour acheter cet objet !';
		return code_retour;
	end if;

	select into num_init
		mgstock_nombre
	from stock_magasin_generique
	where mgstock_gobj_cod = num_objet and mgstock_lieu_cod = v_magasin;

	if not found then
		code_retour := '<p> Erreur ! Il n''y a pas assez d''objets de ce type en stock !';
		return code_retour;
	else
		if nombre_obj > num_init then
			code_retour := '<p> Erreur ! Il n''y a pas assez d''objets de ce type en stock !';
			return code_retour;
		end if;
	end if;
  -- CONTROLES OK

  -- MAJ ECHOPPE
  -- On RETIRE les BRs
	update lieu set lieu_compte = lieu_compte + v_transaction_prix where lieu_cod = v_magasin;
	-- MAJ DU STOCK
 -- on retire de l'inventaire du magasin
	update stock_magasin_generique
		set mgstock_nombre = mgstock_nombre - nombre_obj
		where mgstock_lieu_cod = v_magasin
		and mgstock_gobj_cod = num_objet;
-- on vérifie que le stock soit pas égal à 0
	select into temp
		mgstock_nombre
		from stock_magasin_generique
		where mgstock_lieu_cod = v_magasin
		and mgstock_gobj_cod = num_objet;
	if temp = 0 then
		delete from stock_magasin_generique
			where mgstock_lieu_cod = v_magasin
			and mgstock_gobj_cod = num_objet;
	end if;
	
  -- MAJ PERSO
  update perso
		set perso_po = perso_po - v_transaction_prix
		where perso_cod = personnage;
  tmp_txt := cree_objet_perso_nombre(num_objet,personnage,nombre_obj);
  -- AJOUT DANS LE LOG
  insert into mag_tran_generique
		(mgtra_lieu_cod,mgtra_perso_cod,mgtra_gobj_cod,mgtra_sens,mgtra_montant,mgtra_nombre)
		values
		(v_magasin,personnage,num_objet,4,v_transaction_prix,nombre_obj);
  
  --SUPRESSION DE LA TRANSACTION
  delete from transaction_echoppe where tran_cod = v_transaction_cod;
  select into nom_objet gobj_nom from objet_generique where gobj_cod = num_objet;
	code_retour := '<p>Vous avez acheté <b>'||trim(to_char(nombre_obj,'9999999'))||' '||nom_objet||'</b> pour une somme totale de <b>'||trim(to_char(v_transaction_prix,'9999999999'))||'Br</b></p>';

	return code_retour;
end;
$function$

