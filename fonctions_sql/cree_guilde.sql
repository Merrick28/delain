CREATE OR REPLACE FUNCTION public.cree_guilde(integer, text, text)
 RETURNS text
 LANGUAGE plpgsql
AS $function$
/**********************************************/
/* fonction cree_guilde : crée une guilde     */
/* on passe en paramètres :                   */
/*   $1 : perso_cod du créateur               */
/*   $2 : nom de la guilde                    */
/*   $3 : description                         */
/**********************************************/
/* on a en retour :                           */
/*   0 : code retour (0 OK, -1 bad)           */
/*   1 : explication si BAD                   */
/**********************************************/
/* créé le 03/06/2003                         */
/**********************************************/
declare
	code_retour text;
	personnage alias for $1;
	nom_guilde alias for $2;
	desc_guilde alias for $3;
	v_num_guilde integer;
	v_pa integer;
	v_brouzoufs integer;
begin
	code_retour := '0';
	select into v_pa,v_brouzoufs perso_pa,perso_po
		from perso where perso_cod = personnage;
	if v_pa < getparm_n(27) then
		code_retour := 'Vous n''avez pas assez de PA pour créer cette guilde !';
		return code_retour;
	end if;
	if v_brouzoufs < getparm_n(28) then
		code_retour := 'Vous n''avez pas assez de brouzoufs pour créer cette guilde !';
		return code_retour;
	end if;
	update perso set perso_pa = perso_pa - getparm_n(27),perso_po = perso_po - getparm_n(28)
		where perso_cod = personnage;
	v_num_guilde := nextval('seq_guilde_cod');
-- on insère la guilde
	insert into guilde (guilde_cod,guilde_nom,guilde_description)
		values (v_num_guilde,nom_guilde,desc_guilde);
-- on insère deux rangs fictifs (admin et membre)
	insert into guilde_rang (rguilde_rang_cod,rguilde_guilde_cod,rguilde_libelle_rang,rguilde_admin)
		values (0,v_num_guilde,'Administrateur','O');
	insert into guilde_rang (rguilde_rang_cod,rguilde_guilde_cod,rguilde_libelle_rang)
		values (1,v_num_guilde,'Membre');
-- on insère le joueur en tant qu'admin
	insert into guilde_perso (pguilde_guilde_cod,pguilde_perso_cod,pguilde_rang_cod,pguilde_valide,pguilde_message)
		values (v_num_guilde,personnage,0,'O','O');
	return code_retour;
end;

$function$

