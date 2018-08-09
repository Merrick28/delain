CREATE OR REPLACE FUNCTION public.soin_compteur_pvp(integer)
 RETURNS void
 LANGUAGE plpgsql
AS $function$/*********************************************/
/* soin_compteur_pvp                         */
/*  $1 = perso sur lequel on fait les soins  */
/* Retour : void  			                     */
/*********************************************/
/* créé le 08/11/2009 par Blade              */
/*********************************************/
declare
	personnage alias for $1;
	v_compt_pvp integer;
  v_compt_pvp1 integer;    -- ajout azaghal pour garde fou
	v_pv integer;
	v_pv_max numeric;
	v_niveau integer;
		
begin
/*La fonction permet après des soins de recalculer le compteur pvp. A inclure dans toute fonction de soin */
	select into v_compt_pvp,v_pv,v_pv_max
		perso_compt_pvp,perso_pv,perso_pv_max
		from perso
		where perso_cod = personnage;
	if ((v_pv/v_pv_max) < 0.20) then
		v_niveau := 4;
	elsif ((v_pv/v_pv_max) < 0.33) then
		v_niveau := 3;
	elsif ((v_pv/v_pv_max) < 0.66) then
		v_niveau := 2;
	elsif  ((v_pv/v_pv_max) < 0.80) then
		v_niveau := 1;
	elsif  ((v_pv/v_pv_max) >= 0.80) then
		v_niveau := 0;
	end if;
	if v_compt_pvp < v_niveau then
		v_niveau = v_compt_pvp;
	end if;
	update perso
		set perso_compt_pvp = v_niveau
		where perso_cod = personnage;
end;$function$

