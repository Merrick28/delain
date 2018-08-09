CREATE OR REPLACE FUNCTION public.regle_groupe(integer, integer, integer, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/***********************************************/
/* regle_groupe                                */
/*  $1 = perso_cod                             */
/*  $2 = pa                                    */
/*  $3 = pv                                    */
/*  $4 = dlt                                   */
/***********************************************/
declare 
	code_retour text;
	v_perso alias for $1;
	v_pa alias for $2;
	v_pv alias for $3;
	v_dlt alias for $4;
	v_bonus alias for $5;
	temp integer;
	v_num_groupe integer;
begin
	update groupe_perso
		set pgroupe_montre_pa = v_pa,
		pgroupe_montre_pv = v_pv,
		pgroupe_montre_dlt = v_dlt,
		pgroupe_montre_bonus = v_bonus
		where pgroupe_perso_cod = v_perso;
	return 'Le autorisations ont bien été réglées.<br>
		<a href="groupe.php">Retour à la gestion du groupe</a>';
end;$function$

CREATE OR REPLACE FUNCTION public.regle_groupe(integer, integer, integer, integer, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/***********************************************/
/* regle_groupe                                */
/*  $1 = perso_cod                             */
/*  $2 = pa                                    */
/*  $3 = pv                                    */
/*  $4 = dlt                                   */
/*  $5 = bonus                                 */
/*  $6 = messages                              */
/***********************************************/
declare 
	code_retour text;
	v_perso alias for $1;
	v_pa alias for $2;
	v_pv alias for $3;
	v_dlt alias for $4;
	v_bonus alias for $5;
	v_messages alias for $6;
	temp integer;
	v_num_groupe integer;
begin
	update groupe_perso
		set pgroupe_montre_pa = v_pa,
		pgroupe_montre_pv = v_pv,
		pgroupe_montre_dlt = v_dlt,
		pgroupe_montre_bonus = v_bonus,
		pgroupe_messages = v_messages
		where pgroupe_perso_cod = v_perso;
	return 'Le autorisations ont bien été réglées.<br>
		<a href="groupe.php">Retour à la gestion du groupe</a>';
end;$function$

CREATE OR REPLACE FUNCTION public.regle_groupe(integer, integer, integer, integer, integer, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/***********************************************/
/* regle_groupe                                */
/*  $1 = perso_cod                             */
/*  $2 = pa                                    */
/*  $3 = pv                                    */
/*  $4 = dlt                                   */
/*  $5 = bonus                                 */
/*  $6 = messages                              */
/***********************************************/
declare 
	code_retour text;
	v_perso alias for $1;
	v_pa alias for $2;
	v_pv alias for $3;
	v_dlt alias for $4;
	v_bonus alias for $5;
	v_messages alias for $6;
	v_message_mort alias for $7;
	temp integer;
	v_num_groupe integer;
begin
	update groupe_perso
		set pgroupe_montre_pa = v_pa,
		pgroupe_montre_pv = v_pv,
		pgroupe_montre_dlt = v_dlt,
		pgroupe_montre_bonus = v_bonus,
		pgroupe_messages = v_messages,
		pgroupe_message_mort = v_message_mort
		where pgroupe_perso_cod = v_perso;
	return 'Le autorisations ont bien été réglées.<br>
		<a href="groupe.php">Retour à la gestion du groupe</a>';
end;$function$

CREATE OR REPLACE FUNCTION public.regle_groupe(integer, integer, integer, integer, integer, integer, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/***********************************************/
/* regle_groupe                                */
/*  $1 = perso_cod                             */
/*  $2 = pa                                    */
/*  $3 = pv                                    */
/*  $4 = dlt                                   */
/*  $5 = bonus                                 */
/*  $6 = messages                              */
/*  $7 = envoi de message en mourrant          */
/*  $8 = participe au concours des champions   */
/***********************************************/
declare 
	code_retour text;
	v_perso alias for $1;
	v_pa alias for $2;
	v_pv alias for $3;
	v_dlt alias for $4;
	v_bonus alias for $5;
	v_messages alias for $6;
	v_message_mort alias for $7;
	v_champions alias for $8;
	temp integer;
	v_num_groupe integer;
begin
	update groupe_perso
		set pgroupe_montre_pa = v_pa,
		pgroupe_montre_pv = v_pv,
		pgroupe_montre_dlt = v_dlt,
		pgroupe_montre_bonus = v_bonus,
		pgroupe_messages = v_messages,
		pgroupe_message_mort = v_message_mort,
		pgroupe_champions = v_champions
		where pgroupe_perso_cod = v_perso;
	return 'Le autorisations ont bien été réglées.<br>
		<a href="groupe.php">Retour à la gestion du groupe</a>';
end;$function$

CREATE OR REPLACE FUNCTION public.regle_groupe(integer, integer, integer, integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$/***********************************************/
/* regle_groupe                                */
/*  $1 = perso_cod                             */
/*  $2 = pa                                    */
/*  $3 = pv                                    */
/*  $4 = dlt                                   */
/***********************************************/
declare 
	code_retour text;
	v_perso alias for $1;
	v_pa alias for $2;
	v_pv alias for $3;
	v_dlt alias for $4;
	temp integer;
	v_num_groupe integer;
begin
	update groupe_perso
		set pgroupe_montre_pa = v_pa,
		pgroupe_montre_pv = v_pv,
		pgroupe_montre_dlt = v_dlt
		where pgroupe_perso_cod = v_perso;
	return 'Le autorisations ont bien été réglées.<br>
		<a href="groupe.php">Retour à la gestion de la coterie</a>';
end;$function$

