
create table trackvg
(
	tgv_cod serial
		constraint trackvg_pk
			primary key,
	tgv_varname varchar,
	tgv_page varchar,
	tvg_type varchar,
	tgv_traite int default 0
);

create index trackvg_tgv_page_tgv_varname_index
	on trackvg (tgv_page, tgv_varname);

