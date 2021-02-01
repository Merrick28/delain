-- Function: public.magasin_destock_en_generique( integer, integer)

-- DROP FUNCTION public.magasin_destock_en_generique( integer, integer);

CREATE OR REPLACE FUNCTION public.magasin_destock_en_generique(
    integer,
    integer)
  RETURNS text AS
$BODY$/**************************************************************************/
/* fonction magasin_destock_en_generique : supprime un objet standard     */
/* du stock magasin et le converti en stock generique                     */
/* On passe en paramètres :                                               */
/*   $1 = lieu_cod du magasin                                             */
/*   $2 = obj_cod de l'objet acheté                                       */
/**************************************************************************/
/* Créé le 02/02/2019   Par Marlyza                                       */
/**************************************************************************/
declare
----------------------------------------------------------------------------
-- variable de retour
----------------------------------------------------------------------------
	code_retour text;					-- texte formatté
	temp integer;						-- fourre tout
----------------------------------------------------------------------------
-- variable concernant le magasin
----------------------------------------------------------------------------
	magasin alias for $1;			-- lieu_cod du magasin
	montant_marge integer;			-- marge encaissée par le magasin
	montant_prelev integer;			-- montant reversé à l'administration
	prix_origine integer;			-- prix sans marge
	marge integer;						-- marge du magasin
	prelev integer;					-- % de reversement
	type_magasin integer;			-- type de magasin (runique ou normal)
	taux_rachat numeric;				-- taux de rachat
----------------------------------------------------------------------------
-- variable concernant l'objet
----------------------------------------------------------------------------
	num_objet alias for $2;			-- obj_cod de l'objet acheté
	nom_objet text;					-- nom de l'objet
	v_etat numeric;					-- etat de l'objet
  v_obj_modifie integer;      -- indique si l'objet a été modifie
  v_gobj_cod integer;
begin
----------------------------------------------------------------------------
-- Etape 1 : vérification
----------------------------------------------------------------------------


	select into temp,marge,prelev,type_magasin lieu_cod,lieu_marge,lieu_prelev,lieu_tlieu_cod
		from lieu
		where lieu_cod = magasin;
	if not found then
		code_retour := '<p>Erreur ! Magasin non trouvé !';
		return code_retour;
	end if;

	select into temp,v_etat,v_obj_modifie,v_gobj_cod
		mstock_obj_cod,obj_etat,obj_modifie,obj_gobj_cod
		from stock_magasin join objets on mstock_obj_cod= obj_cod
		where obj_cod = num_objet ;
	if not found then
		code_retour := '<p>Erreur ! L''objet n''est pas dans le stock magasin !';
		return code_retour;
	end if;

	select into nom_objet obj_nom
		from objets
		where obj_cod = num_objet;

  if v_obj_modifie != 0 then
      code_retour := '<p>Erreur ! On ne peut pas déstocker les objets spécifiques!';
      return code_retour;
  end if;

----------------------------------------------------------------------------
-- Etape 2 : on passe au destockage vers stock générique
----------------------------------------------------------------------------

    select into temp f_del_objet(num_objet);
    select into temp
		mgstock_nombre
		from stock_magasin_generique
		where mgstock_lieu_cod = magasin
		and mgstock_gobj_cod = v_gobj_cod;
    if not found then
		insert into stock_magasin_generique
			(mgstock_gobj_cod,mgstock_lieu_cod,mgstock_nombre)
			values
			(v_gobj_cod,magasin,1);
	  else
      update stock_magasin_generique
        set mgstock_nombre = mgstock_nombre + 1
        where mgstock_gobj_cod = v_gobj_cod
        and mgstock_lieu_cod = magasin;
    end if;

	code_retour := '<p>Vous avez transferé l''objet '||nom_objet||' du stock objet vers le stock générique';
	return code_retour;
end;$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION public.magasin_destock_en_generique(integer, integer)
  OWNER TO delain;
