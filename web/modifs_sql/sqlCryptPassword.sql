ALTER TABLE compte
   ADD COLUMN compt_passwd_hash character varying(255);

CREATE TABLE public.sessions
(
   sess_cod bigserial NOT NULL, 
   sess_user_cod bigint NOT NULL, 
   sess_hash character varying(255), 
   PRIMARY KEY (sess_cod)
) 
WITH (
  OIDS = FALSE
)
;
ALTER TABLE public.sessions
  OWNER TO delain;

ALTER TABLE sessions
   ADD COLUMN sess_date timestamp with time zone DEFAULT now();

CREATE INDEX 
   ON sessions (sess_user_cod ASC NULLS LAST);


