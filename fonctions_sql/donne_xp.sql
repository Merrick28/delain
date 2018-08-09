CREATE OR REPLACE FUNCTION public.donne_xp(integer, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*******************************************/
/* fonction donne_xp                       */
/*  actions lorsqu un joueur donne des xp  */
/*  à un autre joueur                      */
/* on passe en paramètres :                */
/*  $1 = donneur                           */
/*  $2 = recevuer                          */
/*  $3 = nombre de px donnés               */
/* on a en sortie une chaine séparée par ; */
/*  0 = code sortie (0 OK, -1 BAD)         */
/*  1 = motif erreur si BAD                */
/*******************************************/
/* Créé le 22/05/2003                      */
/*******************************************/
declare
	code_retour text;
	donneur alias for $1;
	receveur alias for $2;
	nb_px alias for $3;
-- variables donneur
	px_dispo integer;	
-- variables de calcul
	nb_receveur integer;
	texte_evt text;
begin
	code_retour := '0';

	return code_retour;
end;



$function$

