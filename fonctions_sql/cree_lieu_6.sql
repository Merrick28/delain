CREATE OR REPLACE FUNCTION public.cree_lieu_6(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$declare
	code_retour text;
	v_pos alias for $1;	
	v_lieu integer;
begin
	v_lieu := nextval('seq_lieu_cod');
	-- insertion du lieu
	insert into lieu (
		lieu_cod,
		lieu_tlieu_cod,
		lieu_nom,
		lieu_description,
		lieu_refuge,
  		lieu_url)
  		values
  		(	
  		v_lieu,
  		24,
  		'Pavage morbelin ondulant',
  		'Ces dalles ressemblent aux autres de prime abord malgré des couleurs changeantes. Pourtant, un observateur un tant soit peu versé dans les arts magiques peut constater la puissance de flux magiques entremêlés à la surface des pavés.<br>
		D''ailleurs, à y regarder de plus près, il constatera que ce n''est pas de la magie, mais bien plutôt de l''anti-magie. A croire que certains sortilèges pourraient être bloqués ... Cela serait-il une réponse apportée par les Shamans morbelins pour maitriser cet art ?',
		'N',
		' '
  		);
  	insert into lieu_position
  		(lpos_pos_cod,lpos_lieu_cod)
  		values
  		(v_pos,v_lieu);
  	return 'ok';
 end;$function$

