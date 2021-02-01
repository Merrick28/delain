--
-- Name: calcul_temps(smallint); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function calcul_temps(smallint) RETURNS text
    LANGUAGE plpgsql IMMUTABLE
    AS $_$/*****************************************************************/
/* Fonction calcul_temps : transforme un nombre de minutes en    */
/*   heures et minutes                                           */
/* On passe en paramètres :                                      */
/*    1 : le nombre de minutes                                   */
/* On a en sortie une chaine séparée par des ;                   */
/*    pos 1 : nombre d heures                                    */
/*    pos 2 : nombre de minutes                                  */
/*****************************************************************/
/* Créé le 18/04/2003                                            */
/* Liste des modifications :                                     */
/*****************************************************************/
declare
	temp_heure numeric;
 	code_retour text;
	heures integer;
	minutes integer;
	temps alias for $1;
begin
	temp_heure = temps / 60;
	heures := ceil(temp_heure);
	minutes := mod(temps,60);
	code_retour := to_char(heures,'00')||';'||to_char(minutes,'00')||';';
return code_retour;
end;
$_$;


ALTER FUNCTION public.calcul_temps(smallint) OWNER TO delain;

--
-- Name: calcul_temps(integer); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function calcul_temps(integer) RETURNS text
    LANGUAGE plpgsql IMMUTABLE
    AS $_$/*****************************************************************/
/* Fonction calcul_temps : transforme un nombre de minutes en    */
/*   heures et minutes                                           */
/* On passe en paramètres :                                      */
/*    1 : le nombre de minutes                                   */
/* On a en sortie une chaine séparée par des ;                   */
/*    pos 1 : nombre d heures                                    */
/*    pos 2 : nombre de minutes                                  */
/*****************************************************************/
/* Créé le 18/04/2003                                            */
/* Liste des modifications :                                     */
/*****************************************************************/
declare
	temp_heure numeric;
 	code_retour text;
	heures integer;
	minutes integer;
	temps alias for $1;
begin
	temp_heure = temps / 60;
	heures := ceil(temp_heure);
	minutes := mod(temps,60);
	code_retour := to_char(heures,'00')||';'||to_char(minutes,'00')||';';
return code_retour;
end;
$_$;


ALTER FUNCTION public.calcul_temps(integer) OWNER TO delain;

--
-- Name: calcul_temps(numeric); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function calcul_temps(numeric) RETURNS text
    LANGUAGE plpgsql IMMUTABLE
    AS $_$/*****************************************************************/
/* Fonction calcul_temps : transforme un nombre de minutes en    */
/*   heures et minutes                                           */
/* On passe en paramètres :                                      */
/*    1 : le nombre de minutes                                   */
/* On a en sortie une chaine séparée par des ;                   */
/*    pos 1 : nombre d heures                                    */
/*    pos 2 : nombre de minutes                                  */
/*****************************************************************/
/* Créé le 18/04/2003                                            */
/* Liste des modifications :                                     */
/*****************************************************************/
declare
	entier integer;
	temp_heure numeric;
	code_retour text;
	heures integer;
	minutes integer;
	temps alias for $1;
begin
	entier := round(temps);
	temp_heure = entier / 60;
	heures := ceil(temp_heure);
	minutes := mod(temps,60);
	code_retour := to_char(heures,'00')||';'||to_char(minutes,'00')||';';
return code_retour;
end;
$_$;


ALTER FUNCTION public.calcul_temps(numeric) OWNER TO delain;