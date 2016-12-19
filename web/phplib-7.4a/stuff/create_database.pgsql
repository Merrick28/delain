// creates basic database layout
// reincarnation of create_database.pgsql
// $Id: create_database.pgsql,v 1.1.1.1 2000/04/17 16:40:17 kk Exp $

CREATE TABLE active_sessions (
  sid varchar(32) DEFAULT '',
  name varchar(32) DEFAULT '',
  val text,
  changed varchar(14) DEFAULT '' NOT NULL,
  PRIMARY KEY (sid,name)
);
// CREATE INDEX k_changed ON active_sessions USING btree(changed);

CREATE TABLE active_sessions_split (
  ct_sid varchar(32) NOT NULL,
  ct_name varchar(32) NOT NULL,
  ct_pos varchar(6) NOT NULL,
  ct_val text,
  ct_changed varchar(14) DEFAULT '' NOT NULL,
  PRIMARY KEY (ct_sid,ct_name,ct_pos)
);
CREATE INDEX k_asp_changed ON active_sessions_split USING btree(ct_changed);

CREATE TABLE auth_user (
  user_id varchar(32) PRIMARY KEY,
  username varchar(32) DEFAULT '' NOT NULL,
  password varchar(32) DEFAULT '' NOT NULL,
  perms varchar(255)
);

CREATE TABLE auth_user_md5 (
  user_id varchar(32) PRIMARY KEY,
  username varchar(32) DEFAULT '' NOT NULL,
  password varchar(32) DEFAULT '' NOT NULL,
  perms varchar(255)
);

CREATE UNIQUE INDEX k_username ON auth_user (username);
CREATE UNIQUE INDEX k_username_md5 ON auth_user_md5 (username);

// This is an example of a sample row for auth_user

INSERT INTO auth_user VALUES ('c14cbf141ab1b7cd009356f555b607dc','kris','test','admin');
INSERT INTO auth_user_md5 VALUES ('c14cbf141ab1b7cd009356f555b607dc','kris','098f6bcd4621d373cade4e832627b4f6','admin');
