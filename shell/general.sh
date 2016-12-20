/home/delain/public_html/www/envois_mails.sh >> /home/delain/logs/envois_mails.log 2>&1
/usr/bin/psql -t -d delain -U delainadm << EOF | grep -v '^[ ]*$' >> /home/delain/logs/general.log 2>&1
select '------------------------------';
select to_char(now(),'DD/MM/YYYY hh24:mi:ss');
update lieu
		set lieu_url = 'escalier_ferme.php',lieu_dfin = null
		where lieu_dfin < now();
delete from guilde_revolution_vote
		where vrevguilde_revguilde_cod
		in 
		(select revguilde_cod
			from guilde_revolution
			where revguilde_datfin < now());
	delete from guilde_revolution
		where revguilde_datfin < now();
	update compte
		set compt_dfin_hiber = null,
		compt_hibernation = 'T'
		where compt_hibernation = 'O'
		and compt_dfin_hiber < now();
-- actions
	delete from action
		where act_date + '2 days'::interval < now();
select piqure_rappel();
select f_remise_carac();
--select reduc_compt_pvp();
select cron_comptes_temp();
select cron_formule_parchemin();
select cron_valide_objectif();
select cron_sequence_etape_ia();
select action_generique();
EOF
