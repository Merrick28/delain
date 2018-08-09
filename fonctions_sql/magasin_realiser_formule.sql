CREATE OR REPLACE FUNCTION public.magasin_realiser_formule(integer, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/**************************************************************************/
/* fonction magasin_realiser_formule                                      */
/* On passe en paramètres :                                               */
/*   $1 = perso_cod du gérant                                             */
/*   $2 = lieu_cod du magasin                                             */
/*   $3 = le code de la formule                                           */
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
----------------------------------------------------------------------------
-- variable concernant la formule
----------------------------------------------------------------------------
	v_formule_cod alias for $3;
  v_formule_cout integer;
  v_formule_resultat integer;
  ligne record;					-- enregistrements
----------------------------------------------------------------------------
-- divers
----------------------------------------------------------------------------
  temp integer;

begin
----------------------------------------------------------------------------
-- Etape 1 : vérification
----------------------------------------------------------------------------

  -- verification magasin
	select into temp,v_mag_caisse
			lieu_cod,lieu_compte
		from lieu
		where lieu_cod = v_magasin;
	if not found then
		code_retour := '<p>Erreur ! Magasin non trouvé !</p>';
		return code_retour;
	end if;
  -- verification formule
  select into v_formule_cout,v_formule_resultat
   frm_cout,frm_resultat
		from formule
		where frm_cod = v_formule_cod;
	if not found then
  code_retour := '<p>Erreur ! Formule non trouvée !</p>';
		return code_retour;
	end if;
  -- verification de la caisse
  if v_formule_cout > v_mag_caisse then
    code_retour := '<p>Pas assez en caisse pour financer cette réalisation !</p>';
		return code_retour;
	end if;


  -- verification stock du magasin
  for ligne in select gobj_nom,frmco_num,COALESCE(mgstock_nombre,0) as stock
      from  	formule_composant,objet_generique
      LEFT OUTER JOIN stock_magasin_generique ON
      (gobj_cod = mgstock_gobj_cod  and mgstock_lieu_cod = v_magasin)
      where frmco_frm_cod = v_formule_cod
      and frmco_gobj_cod = gobj_cod
   loop
    if ligne.stock < ligne.frmco_num then
      code_retour := '<p>Quantité insuffisante ! '||trim(to_char(ligne.frmco_num,'99999999999'))||' '||ligne.gobj_nom||' requis pour cette formule, '||trim(to_char(ligne.stock,'99999999999'))||' en stock</p>';
		  return code_retour;
		end if;
	end loop;
	
	 code_retour := '<p>Tous les composants sont disponibles, la fabrication commence.<br />';
  -- MAJ Caisse
   update lieu set lieu_compte = lieu_compte + v_formule_resultat - v_formule_cout	where lieu_cod = v_magasin;
  -- Retrait des composants.
  
  for ligne in select gobj_nom,gobj_cod,frmco_num,COALESCE(mgstock_nombre,0) as stock
      from formule_composant,objet_generique
      LEFT OUTER JOIN stock_magasin_generique ON
      (gobj_cod = mgstock_gobj_cod  and mgstock_lieu_cod = v_magasin)
      where frmco_frm_cod = v_formule_cod
      and frmco_gobj_cod = gobj_cod
   loop
      if ligne.stock > ligne.frmco_num then
        update stock_magasin_generique
			  set mgstock_nombre = mgstock_nombre - ligne.frmco_num
			  where mgstock_gobj_cod = ligne.gobj_cod
			  and mgstock_lieu_cod = v_magasin;
      else
        delete from stock_magasin_generique
        where mgstock_gobj_cod = ligne.gobj_cod
			  and mgstock_lieu_cod = v_magasin;
      end if;
      code_retour := code_retour||'<b>'||trim(to_char(ligne.frmco_num,'99999999999'))||' '||ligne.gobj_nom||'</b>  Utilisé(s)<br />';

	end loop;
  
  
  -- Ajout des produits.
    for ligne in select gobj_nom,frmpr_gobj_cod,frmpr_num,COALESCE(mgstock_nombre,0) as stock
      from  	formule_produit,objet_generique
      LEFT OUTER JOIN stock_magasin_generique ON
      (gobj_cod = mgstock_gobj_cod  and mgstock_lieu_cod = v_magasin)
      where frmpr_frm_cod = v_formule_cod
      and frmpr_gobj_cod = gobj_cod
   loop
   if ligne.stock <1 then
    delete from stock_magasin_generique
        where mgstock_gobj_cod = ligne.frmpr_gobj_cod
			  and mgstock_lieu_cod = v_magasin;
    insert into stock_magasin_generique (mgstock_lieu_cod,mgstock_gobj_cod,mgstock_nombre)
      values (v_magasin,ligne.frmpr_gobj_cod,ligne.frmpr_num);
   else
      update stock_magasin_generique
			set mgstock_nombre = mgstock_nombre + ligne.frmpr_num
			where mgstock_gobj_cod = ligne.frmpr_gobj_cod
			and mgstock_lieu_cod = v_magasin;
   end if;
   code_retour := code_retour||'<b>'||trim(to_char(ligne.frmpr_num,'99999999999'))||' '||ligne.gobj_nom||'</b>  Produits(s)<br />';
	end loop;




	code_retour := code_retour||'Réalisation Terminée</p>';

	return code_retour;
end;
$function$

