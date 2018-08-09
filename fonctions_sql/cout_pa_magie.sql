CREATE OR REPLACE FUNCTION public.cout_pa_magie(integer, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function cout_pa_magie : calcul le cout en pa pour les sorts  */
/* On passe en paramètres                                        */
/*   $1 = lanceur                                                */
/*   $2 = numéro du sort lancé                                   */
/*   $3 = type lancer                                            */
/*        0 = rune                                               */
/*        1 = mémo                                               */
/*        2 = receptacle                                         */
/* Le code sortie est une valeur html du cout en pa              */
/*****************************************************************/
/* Créé le 20/07/2003                                            */
/* Liste des modifications :                                     */
/*   08/09/2003 : ajout d un tag pour amélioration auto          */
/*   29/01/2004 : modif du type code sortie                      */
/*   05/10/2004 : rajout des modificateurs de niveau             */
/*                changement du cout en PA si raté               */
/*   14/04/2005 : nouveau système de PX                          */
/*****************************************************************/
declare
-------------------------------------------------------------
-- variables servant pour la sortie
-------------------------------------------------------------
	code_retour text;				-- chaine html de sortie
-------------------------------------------------------------
-- variables concernant le lanceur	
-------------------------------------------------------------
	lanceur alias for $1;		-- perso_cod du lanceur
	-------------------------------------------------------------
-- variables concernant la cible
-------------------------------------------------------------
-------------------------------------------------------------
-- variables concernant le sort
-------------------------------------------------------------
	num_sort alias for $2;		-- numéro du sort à lancer
	type_lancer alias for $3;	-- type de lancer (memo ou rune)
	cout_pa integer;		-- Cout en PA du sort
	temp text;			-- passage de la valeur en txt
        pa_magie integer;               -- different bonus en pa sur sort

-------------------------------------------------------------
-- variables de contrôle
-------------------------------------------------------------
	
-------------------------------------------------------------
-- variables de calcul
-------------------------------------------------------------

	
begin
-------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
	code_retour := '';
-- les px
-------------------------------------------------------------
-- Etape 2 : contrôles
-------------------------------------------------------------	
select into cout_pa sort_cout from sorts where sort_cod = num_sort;
-- calcul des pa
-- modification du cout pour les lancer via receptacles
-- on exclue le receptacle du traitement des bonus

if type_lancer != 2 then
	cout_pa := cout_pa + valeur_bonus(lanceur, 'PAM');
        if type_lancer != 4 then
           if num_sort in(128,139) then
	        cout_pa := cout_pa + valeur_bonus(lanceur, 'ERU');
           end if;
           if num_sort in(11,67) then
	        cout_pa := cout_pa + valeur_bonus(lanceur, 'ERU');
           end if;
           if num_sort in(25,38) then
	        cout_pa := cout_pa + valeur_bonus(lanceur, 'ERU');
           end if;
          if num_sort = '136' then
	        cout_pa := cout_pa + valeur_bonus(lanceur, 'ERU');
           end if;
         end if;
end if;
-- le cout depends du cout du sort d'origine
if type_lancer = 2 then
       if cout_pa < 9 then
	cout_pa := 2;
         else
        cout_pa := 4;
	end if;
end if;
-- pour les parchemins le cout est le cout normal - 1
   if type_lancer = 4 then 
       cout_pa := cout_pa - 1;
   end if;
temp := cout_pa;
code_retour := code_retour||temp;
	return code_retour;
end;
$function$

