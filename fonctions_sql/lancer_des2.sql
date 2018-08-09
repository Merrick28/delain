CREATE OR REPLACE FUNCTION public.lancer_des2(integer, integer)
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
	i integer;
begin
	i := 1;
	resultat := 0;
	val_des_moins := val_des - 1;
	while i <= nb_des loop
		resultat_intermediaire := trunc(val_des*random()) + 1;
		resultat := resultat + resultat_intermediaire;
		i := i + 1;
	end loop;
if val_des = 100 then
	if nb_des = 1 then
		insert into des (des_valeurs) values(resultat);
	end if;
end if;

	return resultat;
end;$function$

