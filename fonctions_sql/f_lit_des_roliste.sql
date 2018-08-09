CREATE OR REPLACE FUNCTION public.f_lit_des_roliste(character varying)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/*************************************************************/
/* fonction f_lit_des_roliste                                */
/*   Lit une chaîne de type 3D8-7 et renvoie une valeur      */
/*          correspondante.                                  */
/*   on passe en paramètres :                                */
/*   $1 = v_valeur : la chaîne à analyser.                   */
/* on a en sortie une valeur aléatoire correspondante        */
/*************************************************************/
/* Créé le 09/06/2014                                        */
/*************************************************************/
declare
	v_valeur alias for $1; -- La chaîne donnant les dés à tirer
	code_retour integer;   -- La valeur tirée aux dés
	n integer;             -- Le nombre de dés
	d integer;             -- Le nombre de face des dés
	s integer;             -- Le signe devant le nombre de dés
	b integer;             -- La valeur du bonus
	sb integer;            -- Le signe du bonus
	ch char;               -- Le caractère en cours de lecture
begin
	code_retour := 0;

	n := 0;
	d := -1;
	s := 1;
	sb := 0;
	b := 0;
	for i in 1..length(v_valeur) loop
		ch := substr(v_valeur , i , 1);
		if ch IN ('D' , 'd') then
			d := 0;
		elseif ch <> ' ' then
			if ch = '-' AND d = -1 then
				s := -1;
			elseif ch = '+' AND d = -1 then
				s := 1;
			elseif ch = '-' AND d > -1 then
				sb := -1;
			elseif ch = '+' AND d > -1 then
				sb := 1;
			elseif d = -1 then
				n := n * 10 + cast(ch AS integer);
			elseif sb = 0 then
				d := d * 10 + cast(ch AS integer);
			else
				b := b * 10 + cast(ch AS integer);
			end if;
		end if;
	end loop;
	if (d = -1) then
		d := 1;
	end if;
	code_retour := s * lancer_des(n, d) + sb * b;

	return code_retour;
end;$function$

