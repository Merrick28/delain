CREATE OR REPLACE FUNCTION public.cadeau_mj(integer)
 RETURNS text
 LANGUAGE plpgsql
AS $function$declare
  code_retour text;
  personnage alias for $1;
  temp integer;
  v_num_mes integer;
  v_texte_mes text;
begin
  temp := cree_objet_perso_nombre(327,personnage,1);
  v_texte_mes := 'Bienheureux garnements,<br>
Vous avez aidé Jack comme de braves petits chiens rapportant fidèlement. Afin de maintenir ce conditionnement, il va sans dire que vous méritez une jolie récompense. La taille du sucre dépendant bien entendu de l''effort fourni, les moins bons pourront se ronger les pattes arrières. Vous ne vouliez quand même pas que nous fassions de Noël une fête de la solidarité ? Continuez de vous méfier des vieux barbus rougeauds, ils distribuent souvent de la publicité. Longue vie à vous mes canailloux!';
  v_num_mes := nextval('seq_msg_cod');
  insert into messages (msg_cod,msg_titre,msg_corps)
  values (v_num_mes,'Récompense....',v_texte_mes);
  insert into messages_exp (emsg_msg_cod,emsg_perso_cod) values (v_num_mes,721464);
  insert into messages_dest (dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive) values (v_num_mes,personnage,'N','N');
  return 'OK';
end;


$function$

