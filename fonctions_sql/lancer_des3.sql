CREATE OR REPLACE FUNCTION public.lancer_des3(integer, integer, integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/*****************************************************************/
/* function lancer_des : Procédure de lancer de dés              */
/* On passe en paramètres  :                                     */
/*    1 le nombre de dés                                         */
/*    2 la valeur des dés                                        */
/*    3 valeur bonus malus                                       */
/* En sortie, on a le résultat du lancer                         */
/* Ex : select lancer_des(3,6) donnera en sortie 3D6             */
/*****************************************************************/

declare
	nb_des alias for $1;
	val_des alias for $2;
        bonmal alias for $3;
        resultat integer;
	val_des_moins integer;
	resultat_intermediaire integer;
	avril integer;
	i integer;
begin
	avril := 0;
	i := 1;
	resultat := 0;
	val_des_moins := val_des - 1;
	/*Mise en commentaire du premier avril Blade
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
        -- ajout d'azaghal on utilise le paramètre de bonus malus
        -- ce bonus malus ne doit etre utilisé normalement que sur 1D100
           if bonmal = 0 then
		while i <= nb_des loop
			resultat_intermediaire := trunc(val_des*random()) + 1;
			resultat := resultat + resultat_intermediaire;
			i := i + 1;
		end loop;
           else
              if val_des = 100 then
                 while i <= nb_des loop
			resultat_intermediaire := trunc(val_des*random()) + 1;
			resultat := resultat + resultat_intermediaire;
			i := i + 1;
	         end loop;
               resultat := resultat + bonmal;
                  if resultat > 100 then
                  resultat := 100;
                  end if;
                  if resultat < 1 then
                  resultat := 1;
                  end if;
               else
                  while i <= nb_des loop
			resultat_intermediaire := trunc(val_des*random()) + 1;
			resultat := resultat + resultat_intermediaire;
			i := i + 1;
		   end loop;
               end if;
            end if;
	end if;
	return resultat;
end;$function$

