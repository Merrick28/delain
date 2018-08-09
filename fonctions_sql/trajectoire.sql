CREATE OR REPLACE FUNCTION public.trajectoire(integer, integer, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/**************************************************/
/* fonction trajectoire : calcule une trajectoire */
/*  pour une arme à distance                      */
/* on passe en paramètres :                       */
/*  $1 = pos_cod 1                                */
/*  $2 = pos_cod 2                                */
/*  $3 = perso_cod cible                          */
/*  $4 = perso_cod du lanceur                     */
/* on a en retour une chaine séparée par ;        */
/*  pos0 = 0 tout est OK, la cible est atteinte   */
/*  pos0 = 1 on atteint un mur (pos_cod en 1)     */
/*  pos0 = 2 on atteint un autre perso            */
/*      (perso_cod en 1)                          */
/**************************************************/
/* créé le 15/09/2003                             */
/* 15/12/2007 : modification totale de la fonction*/
/* Pas du tout en phase avec les ligne de vue     */
/**************************************************/
declare
	code_retour text;
	pos1 alias for $1;
	pos2 alias for $2;
	v_cible alias for $3;
	personnage alias for $4;
	toucher_intermediaire integer;
	v_modif_change_cible integer;
	ligne record;

begin
	-- On détermine si le projectile change de cible, à cause d’une mauvaise dext
	v_modif_change_cible := modif_change_cible(personnage);

	code_retour := '0;0;';

	for ligne in select nv_cible from trajectoire_perso_hors_lieu(pos1, pos2) as (nv_cible int, v_pos int, type_perso int)
	loop
		toucher_intermediaire := lancer_des(1, 100);
		if toucher_intermediaire < (getparm_n(24) - v_modif_change_cible)  then
			-- On teste si le projectile ne les touche pas, en fonction d’un % de chance, soit 40% - (dex-11 * 2)
			--  le test est fait pour chaque perso sur la route du projectile !
			-- Si un projectile atteint un des persos on arrête
			code_retour := '2;' || trim(to_char(ligne.nv_cible, '999999999')) || ';';
			return code_retour;
		else
			code_retour := '0;0;';
		end if;
	end loop;
	return code_retour;
end;$function$

