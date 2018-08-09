CREATE OR REPLACE FUNCTION public.lieu_arrive2()
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/*************************************************************/
/* fonction lieu_arrive : donne un pos_cod au lvl 0 pour les */
/*   lieux d'arrivée concernant sal'morv                     */
/*************************************************************/
declare
	code_retour integer;
	cp_lieu integer;
	temp integer;
begin
	select into cp_lieu
		count(lieu_cod)
		from lieu,lieu_position,positions
		where lieu_tlieu_cod = 27
		and lieu_cod = lpos_lieu_cod
		and lpos_pos_cod = pos_cod
		and pos_etage = 0;
	temp := lancer_des(1,cp_lieu);
	temp := temp - 1;
	select into temp 
		pos_cod
 		from lieu,lieu_position,positions
		where lieu_tlieu_cod = 27
		and lieu_cod = lpos_lieu_cod
		and lpos_pos_cod = pos_cod
		and pos_etage = 0
		offset temp
		limit 1;
	return temp;
end;$function$

CREATE OR REPLACE FUNCTION public.lieu_arrive2(integer)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$/*************************************************************/
/* fonction lieu_arrive : donne un pos_cod au lvl 0 pour les */
/*   lieux d'arrivée concernant sal'morv  (pas d'aléatoire)  */
/*************************************************************/
declare
	compte alias for $1;
        code_retour integer;
	cp_lieu integer;
	temp integer;
begin
	select into cp_lieu
		count(lieu_cod)
		from lieu,lieu_position,positions
		where lieu_tlieu_cod = 27
		and lieu_cod = lpos_lieu_cod
		and lpos_pos_cod = pos_cod
		and pos_etage = 0;
	temp := compte % cp_lieu;

	select into temp 
		pos_cod
 		from lieu,lieu_position,positions
		where lieu_tlieu_cod = 27
		and lieu_cod = lpos_lieu_cod
		and lpos_pos_cod = pos_cod
		and pos_etage = 0
		offset temp
		limit 1;
	return temp;
end;$function$

