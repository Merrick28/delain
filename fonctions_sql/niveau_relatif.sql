CREATE OR REPLACE FUNCTION public.niveau_relatif(integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/***********************************************************/
/* fonction niveau_relatif : donne le niveau que pourrait  */
/*   donner un nombre de PX                                */
/* on passe en params :                                    */
/*   $1 = nbre de PX                                       */
/***********************************************************/
declare
	code_retour integer;
	nb_px alias for $1;
	temp_num numeric;
	niveau_relatif integer;
	
begin
	temp_num := 5 + (4*nb_px);
	temp_num := temp_num / 5;
	temp_num := |/temp_num;
	temp_num := temp_num - 3;
	temp_num :=temp_num / 2;
	niveau_relatif := floor(temp_num);
	code_retour := niveau_relatif + 2;
return code_retour;
end;
	
	$function$

CREATE OR REPLACE FUNCTION public.niveau_relatif(numeric)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/***********************************************************/
/* fonction niveau_relatif : donne le niveau que pourrait  */
/*   donner un nombre de PX                                */
/* on passe en params :                                    */
/*   $1 = nbre de PX                                       */
/***********************************************************/
declare
	code_retour integer;
	nb_px alias for $1;
	temp_num numeric;
	niveau_relatif integer;
	
begin
	temp_num := 5 + (4*nb_px);
	temp_num := temp_num / 5;
	temp_num := |/temp_num;
	temp_num := temp_num - 3;
	temp_num :=temp_num / 2;
	niveau_relatif := floor(temp_num);
	code_retour := niveau_relatif + 2;
return code_retour;
end;
	
	$function$

