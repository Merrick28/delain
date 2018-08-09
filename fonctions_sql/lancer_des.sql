CREATE OR REPLACE FUNCTION public.lancer_des(integer, integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function lancer_des : Procédure de lancer de dés              */
/* On passe en paramètres  :                                     */
/*    1 le nombre de dés                                         */
/*    2 la valeur des dés                                        */
/* En sortie, on a le résultat du lancer                         */
/* Ex : select lancer_des(3,6) donnera en sortie 3D6             */
/*****************************************************************/
declare
	resultat integer;
	nb_des alias for $1;
	val_des alias for $2;
	val_des_moins integer;
	resultat_intermediaire integer;
	avril integer;
	i integer;
begin
	avril := 0;
	i := 1;
	-- ajout par Merrick, on arrête tout si le nombre de dés est supérieur à 100 000
	-- ca va provoquer un plantage de la fonction appelante, et on va aller voir à l'intérieur ce qu'il y a
	-- car ce n'est pas normal
	-- on provoque donc un plantage pour division par zéro
	if (nb_des > 100000) then
		resultat := i/avril;
		return resultat;
	end if;
	resultat := 0;
	val_des_moins := val_des - 1;
	/*Mise en commentaire du précédent premier avril Blade remplacé par nouveau
	if to_char(now(),'DD/MM/YYYY') = '01/04/2009' then
		if val_des = 100 then
			avril := 1;
				while i <= nb_des loop
					resultat_intermediaire := trunc(5*random()) + 97;
					resultat := resultat + resultat_intermediaire;
					i := i + 1;
				end loop;
		end if;
	end if;*/
	if avril = 0 then
		while i <= nb_des loop
			resultat_intermediaire := trunc(val_des*random()) + 1;
			resultat := resultat + resultat_intermediaire;
			i := i + 1;
		end loop;
	end if;
	return resultat;
end;$function$

