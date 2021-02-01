--
-- Name: cherche_multi(integer); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function cherche_multi(integer) RETURNS text
    LANGUAGE plpgsql
    AS $_$/*************************************/
/* fonction cherche_multi(integer)   */
/*************************************/
declare
	ligne record;
	duree alias for $1;
	i_duree interval;
	code_retour text;
	v_cpt1 integer;
	v_cpt2 integer;

begin
	i_duree := trim(to_char(duree,'9999'))||' days';
	code_retour := '';
	for ligne in
		select distinct t1.multi_cpt1 as cpt1,t1.multi_cpt2 as cpt2,
(select count(t3.multi_cod) from multi_trace t3
		where (t3.multi_cpt1 = t1.multi_cpt1 and t3.multi_cpt2 = t1.multi_cpt2)) as nombre
		from multi_trace t1,multi_trace t2,compte c1,compte c2
		where t1.multi_cpt1 = c1.compt_cod
		and c1.compt_confiance = 'N'
		and t1.multi_cpt2 = c2.compt_cod
		and c2.compt_confiance = 'N'
		and t2.multi_date  >= t1.multi_date + '7 days'::interval
		and t1.multi_cpt1 = t2.multi_cpt1 and t1.multi_cpt2 = t2.multi_cpt2
group by cpt1,cpt2
union
select distinct t1.multi_cpt1 as cpt1,t1.multi_cpt2 as cpt2,
(select count(t3.multi_cod) from multi_trace t3
		where (t3.multi_cpt1 = t1.multi_cpt1 and t3.multi_cpt2 = t1.multi_cpt2)) as nombre
		from multi_trace t1,multi_trace t2,compte c1,compte c2
		where t1.multi_cpt1 = c1.compt_cod
		and c1.compt_confiance = 'N'
		and t1.multi_cpt2 = c2.compt_cod
		and c2.compt_confiance = 'N'
		and t2.multi_date  >= t1.multi_date + '7 days'::interval
		and t1.multi_cpt1 = t2.multi_cpt2 and t1.multi_cpt2 = t2.multi_cpt1
group by cpt1,cpt2
order by nombre desc
		loop
		code_retour := code_retour||trim(to_char(ligne.cpt1,'999999999'))||';'||trim(to_char(ligne.cpt2,'999999999'))||';'||trim(to_char(ligne.nombre,'999999999'))||'#';
	end loop;
	return code_retour;
end;$_$;


ALTER FUNCTION public.cherche_multi(integer) OWNER TO delain;