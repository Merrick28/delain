SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET row_security = off;

SET search_path = quetes, pg_catalog;

--
-- TOC entry 4617 (class 0 OID 647854)
-- Dependencies: 794
-- Data for Name: aquete; Type: TABLE DATA; Schema: quetes; Owner: delain
--

COPY aquete (aquete_cod, aquete_nom, aquete_description, aquete_etape_cod, aquete_actif, aquete_date_debut, aquete_date_fin, aquete_nb_max_instance, aquete_nb_max_participant, aquete_nb_max_rejouable, aquete_nb_max_quete, aquete_max_delai) FROM stdin;
1	Quête de demo	Quête de démonstration de la mécanique de «Quête Auto»	3	O	2018-10-21 17:27:56+02	\N	1	\N	\N	\N	\N
\.


--
-- TOC entry 4618 (class 0 OID 647871)
-- Dependencies: 795
-- Data for Name: aquete_element; Type: TABLE DATA; Schema: quetes; Owner: delain
--

COPY aquete_element (aqelem_cod, aqelem_aquete_cod, aqelem_aqetape_cod, aqelem_param_id, aqelem_type, aqelem_misc_cod, aqelem_param_num_1, aqelem_param_num_2, aqelem_param_num_3, aqelem_param_txt_1, aqelem_param_txt_2, aqelem_param_txt_3, aqelem_param_ordre, aqelem_aqperso_cod, aqelem_quete_step, aqelem_nom) FROM stdin;
66	1	15	1	valeur	0	10	\N	\N	\N	\N	\N	0	\N	\N	\N
67	1	15	2	valeur	0	0	\N	\N	\N	\N	\N	0	\N	\N	\N
68	1	15	3	element	3	1	0	0				0	\N	\N	\N
8	1	3	1	perso	7842876	\N	\N	\N	\N	\N	\N	0	\N	\N	\N
9	1	3	2	choix	0	\N	\N	\N	Mais oui bien sûr.	\N	\N	0	\N	\N	\N
10	1	3	2	choix	-1	\N	\N	\N	Désolé, mais je n'ai pas le temps.	\N	\N	1	\N	\N	\N
59	1	11	1	delai	0	0	\N	\N	\N	\N	\N	0	\N	\N	\N
60	1	11	2	element	9	4	0	0				0	\N	\N	\N
61	1	11	3	valeur	0	0	\N	\N	\N	\N	\N	0	\N	\N	\N
62	1	11	4	etape	18	\N	\N	\N	\N	\N	\N	0	\N	\N	\N
55	1	10	1	delai	0	0	\N	\N	\N	\N	\N	0	\N	\N	\N
56	1	10	2	element	8	4	0	0				0	\N	\N	\N
57	1	10	3	valeur	0	1	\N	\N	\N	\N	\N	0	\N	\N	\N
58	1	10	4	etape	18	\N	\N	\N	\N	\N	\N	0	\N	\N	\N
11	1	4	1	delai	0	0	\N	\N	\N	\N	\N	0	\N	\N	\N
12	1	4	2	perso	7842877	\N	\N	\N	\N	\N	\N	1	\N	\N	\N
13	1	4	3	valeur	0	1	\N	\N	\N	\N	\N	0	\N	\N	\N
14	1	4	4	objet_generique	185	\N	\N	\N	\N	\N	\N	1	\N	\N	\N
47	1	8	1	valeur	0	1	\N	\N	\N	\N	\N	0	\N	\N	\N
48	1	8	2	position	62421	-4	3	-100	\N	\N	\N	1	\N	\N	\N
15	1	5	1	delai	0	0	\N	\N	\N	\N	\N	0	\N	\N	\N
16	1	5	2	element	3	1	0	0				0	\N	\N	\N
17	1	5	3	valeur	0	1	\N	\N	\N	\N	\N	0	\N	\N	\N
18	1	5	4	element	4	4	0	0				0	\N	\N	\N
42	1	6	1	valeur	0	0	\N	\N	\N	\N	\N	0	\N	\N	\N
43	1	6	2	valeur	0	1000	\N	\N	\N	\N	\N	0	\N	\N	\N
44	1	6	3	element	3	1	0	0				0	\N	\N	\N
49	1	8	3	valeur	0	0	\N	\N	\N	\N	\N	0	\N	\N	\N
50	1	8	4	monstre_generique	264	0	0	0	\N	\N	\N	1	\N	\N	\N
52	1	9	2	element	8	4	0	0				0	\N	\N	\N
63	1	13	1	delai	0	0	\N	\N	\N	\N	\N	0	\N	\N	\N
64	1	13	2	element	3	1	0	0				0	\N	\N	\N
65	1	14	1	texte	0	\N	\N	\N	Tueur du Seigneur Orgre	\N	\N	0	\N	\N	\N
45	1	7	1	choix	0	\N	\N	\N	Mais oui, bien sûr dites moi qui je dois éliminer?	\N	\N	0	\N	\N	\N
46	1	7	1	choix	17	\N	\N	\N	Malheureusement je suis un pacifique, cette mission n'est pas pour moi.	\N	\N	1	\N	\N	\N
51	1	9	1	valeur	0	5	\N	\N	\N	\N	\N	0	\N	\N	\N
177	1	9	4	perso	0	\N	\N	\N	\N	\N	\N	0	\N	\N	\N
240	1	19	1	element	5	4	\N	\N	\N	\N	\N	0	\N	\N	\N
\.


--
-- TOC entry 4619 (class 0 OID 647889)
-- Dependencies: 797
-- Data for Name: aquete_etape; Type: TABLE DATA; Schema: quetes; Owner: delain
--

COPY aquete_etape (aqetape_cod, aqetape_nom, aqetape_aquete_cod, aqetape_aqetapmodel_cod, aqetape_parametres, aqetape_texte, aqetape_etape_cod) FROM stdin;
7	#7: Quête - Faire un CHOIX	1	8	\N	Si vous avez encore un peu de temps, j'ai une autre mission pour vous. Cette mission est un peu plus risquée, mais vous me paraissez assez malin pour trouver les ressources nécessaires pour la mener à bien. Il s'agirait de nous libérer d'un fléau qui sévit à cet étage ces derniers temps.\r\nEn retour je saurai me montrer généreux.\r\n[1]	8
5	#5: Objets - Remettre un objet à un PNJ 	1	15	\N	Warlock le morbelin vous remets un «  Anneau de Troll » Vous pouvez maintenant retourner voir l'elfe noir pour le lui donner.\r\n\r\n(vous pouvez-vérifier l'anneau est maintenant dans votre inventaire, remettez l'objet à l'elfe noir par le menu transaction)	6
3	#3: Quête - Déclenchement sur perso	1	1	\N	Un elfe noir, vous aborde. \r\n« Bonjour mon ami, seriez vous en mesure de me rendre un petit service? Je serai vous récompenser pour vos efforts »\r\n [2]	4
6	#6: Gain - Recevoir PX/PO	1	12	\N	Je tenais beaucoup à cet anneau, merci de me l'avoir rapporté. \r\nVoici les 1000 bz promis.	19
19	#19: Quête - Nettoyer la quête	1	16	\N		7
8	#8: Monstres - Invoquer des Monstres ou PNJ sur une position	1	17	\N		9
9	#9: Monstres - Invoquer une armée de Monstres	1	19	\N		10
11	#11: Tuer - Tuer des Monstres ou PNJ (armée)	1	20	\N	Vous devez aussi supprimer la garde rapprochée du seigneur Ogre. [2]	13
18	#18: Quête - Fin de la quête sur ECHEC	1	10	\N	C'est bien dommage, mais vous n’êtes pas celui qui a achevé le seigneur Ogre. Vous ne pourrez pas recevoir la prime.	\N
10	#10: Tuer - Tuer des Monstres ou PNJ (boss)	1	20	\N	Il y a un seigneur ogre qui sévit alentour, tuez le et supprimez la petite armée qui marche avec lui. Vous pouvez vous faire aider mais vous devez être celui qui achève le seigneur orge.\r\nBon courage., revenez me voir si y arrivez.	11
17	#17: Quête - Fin de la quête avec SUCCES (pacifique)	1	9	\N	Je comprends votre position, ce n'est pas grave je finirais bien par trouver un autre volontaire. \r\nJe vous remercie encore infiniment pour la course.\r\nA bientôt.	18
16	#16: Quête - Fin de la quête avec SUCCES	1	9	\N	Merci tout le Proving ground se souviendra de vous et de vos faits d'arme.	17
4	#4: Objets - Recevoir un objet d'un PNJ	1	14	\N	Allez trouver mon ami Warlock le Morbelin. Il traîne quelque part dans le proving ground.\r\nIl devrait vous remettre un anneau.\r\nSi vous me le rapportez, je vous payerai 1000 Bz pour la course.	5
15	#15: Gain - Recevoir PX/PO	1	12	\N	[3] vous récompenses de vos efforts, vous recevez [1] PX.	16
14	#14: Gain - Recevoir un TITRE	1	13	\N	Vous recevez le titre de "Tueur du Seigneur Orgre"	15
13	#13: Déplacement - Vers un PERSO	1	4	\N	Félicitation, vous avez libéré la zone de du seigneur ogre et de son armée.\r\nRetournez voir l'elfe noir.	14
\.
