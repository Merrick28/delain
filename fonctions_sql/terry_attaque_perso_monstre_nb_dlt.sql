CREATE OR REPLACE FUNCTION public.terry_attaque_perso_monstre_nb_dlt(integer, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$
declare
code_retour char;
attaquant alias for $1;
cible alias for $2;
nb_dlt alias for $3;
i integer;

begin
code_retour := 'a';

for i in 1..nb_dlt loop
	perform attaque(attaquant,cible,0);
	perform attaque(attaquant,cible,0);
	perform attaque(attaquant,cible,0);
	perform attaque(attaquant,cible,0);
	perform attaque(attaquant,cible,0);
	perform attaque(attaquant,cible,0);
	update perso set perso_pa=12 where perso_cod=attaquant;
end loop;

return code_retour;
end;
$function$

