CREATE OR REPLACE FUNCTION public.don_br(integer, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/***********************************/
/* fonction don_br                 */
/*  $1 = perso_cod donnant         */
/*  $2 = perso_cod receveur        */
/*  $3 = qte                       */
/***********************************/
declare
	code_retour text;
	donneur alias for $1;
	receveur alias for $2;
	quantite alias for $3;
	pos_donneur integer;
	pos_receveur integer;
	or_donneur integer;
	nom_receveur text;
begin
	if quantite < 0 then
		code_retour := 'Erreur ! Quantité négative !';
		return code_retour;
	end if;
	select into or_donneur,pos_donneur
		perso_po,ppos_pos_cod
		from perso,perso_position
		where perso_cod = donneur
		and ppos_perso_cod = perso_cod;
	if not found then
		code_retour := 'Erreur ! Position non trouvée !';
		return code_retour;
	end if;
	select into nom_receveur perso_nom from perso where perso_cod = receveur;
	if not found then
		code_retour := 'Erreur ! Receveur non trouvé !';
		return code_retour;
	end if;

	-- Interdit de commercer pendant un défi
	if exists(select 1 from defi where defi_statut = 1 and donneur in (defi_lanceur_cod, defi_cible_cod)) then
		code_retour := '<p>Erreur ! Il est interdit de commercer pendant un défi !</p>';
		return code_retour;
	end if;

	if or_donneur < quantite then
		code_retour := 'Erreur ! Pas assez de brouzoufs pour faire ce don !';
		return code_retour;
	end if;
	if ((select ppos_pos_cod from perso_position where ppos_perso_cod = receveur) != pos_donneur) then
		code_retour := 'Erreur ! le donneur et le receveur ne sont pas sur la même position !';
		return code_retour;
	end if;
	update perso set perso_po = perso_po - quantite where perso_cod = donneur;
	update perso set perso_po = perso_po + quantite where perso_cod = receveur;
	code_retour := '[attaquant] a donné '||trim(to_char(quantite,'999999999999999999'))||' brouzoufs à [cible].';
	insert into ligne_evt(levt_tevt_cod,levt_date,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
		values(40,now(),donneur,code_retour,'O','O',donneur,receveur);
	insert into ligne_evt(levt_tevt_cod,levt_date,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
		values(40,now(),receveur,code_retour,'N','O',donneur,receveur);
	code_retour := 'Vous venez de donner '||trim(to_char(quantite,'999999999999999999'))||' brouzoufs à '||nom_receveur||'.<br>';
	return code_retour;
end;
	$function$

