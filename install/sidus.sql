SET storage_engine=InnoDB;

CREATE TABLE IF NOT EXISTS sd_node (
	id BIGINT(20) unsigned NOT NULL AUTO_INCREMENT,
	parent_id BIGINT(20) unsigned NOT NULL,
	object_id BIGINT(20) unsigned NOT NULL,
	
	type_name VARCHAR(32) NOT NULL,
	owner_id BIGINT(20) unsigned NOT NULL,
	can_read BOOLEAN DEFAULT 0,
	can_add BOOLEAN DEFAULT 0,
	can_edit BOOLEAN DEFAULT 0,
	can_delete BOOLEAN DEFAULT 0,
	is_admin BOOLEAN DEFAULT 1,
	role_id BIGINT(20) unsigned NOT NULL,

	PRIMARY KEY (id),
	FOREIGN KEY (parent_id) REFERENCES sd_node(id),
	FOREIGN KEY (object_id) REFERENCES sd_object(id),
	FOREIGN KEY (owner_id) REFERENCES sd_node(id),
	FOREIGN KEY (role_id) REFERENCES sd_node(id),
	INDEX type_name (type_name)
) DEFAULT CHARSET=ascii;

CREATE TABLE IF NOT EXISTS sd_object (
	id BIGINT(20) unsigned NOT NULL AUTO_INCREMENT,
	lang VARCHAR(4),
	current_id BIGINT(20) unsigned NOT NULL,

	created_by BIGINT(20),
	created_at TIMESTAMP,
	modified_by BIGINT(20),
	modified_at TIMESTAMP,

	PRIMARY KEY (id, lang),
	INDEX lang (lang)
) DEFAULT CHARSET=ascii;

CREATE TABLE sd_info(
	object_id BIGINT(20) unsigned NOT NULL,
	title VARCHAR(256),
	slug VARCHAR(128),
	content TEXT,
	tags TEXT,
	PRIMARY KEY (object_id),
	FOREIGN KEY (object_id) REFERENCES sd_object(id)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS sd_permission (
	object_id BIGINT(20) unsigned NOT NULL,
	entity_id BIGINT(20) unsigned NOT NULL,

	is_inverse BOOLEAN DEFAULT 0,
	is_read BOOLEAN DEFAULT 0,
	is_add BOOLEAN DEFAULT 0,
	is_edit BOOLEAN DEFAULT 0,
	is_delete BOOLEAN DEFAULT 0,
	is_admin BOOLEAN DEFAULT 0,

	PRIMARY KEY (object_id),
	FOREIGN KEY (object_id) REFERENCES sd_object(id),
	FOREIGN KEY (entity_id) REFERENCES sd_node(id)
) DEFAULT CHARSET=utf8;

CREATE TABLE sd_user(
	object_id BIGINT(20) unsigned NOT NULL,
	username VARCHAR(32),
	password VARCHAR(40),
	salt VARCHAR(40),
	email VARCHAR(50),
	expire TIMESTAMP,

	PRIMARY KEY (object_id),
	FOREIGN KEY (object_id) REFERENCES sd_object(id),
	UNIQUE (username)
) DEFAULT CHARSET=utf8;

CREATE TABLE sd_session(
	object_id BIGINT(20) unsigned NOT NULL,

	cookie VARCHAR(40),
	remote_addr VARCHAR(40),
	user_agent TEXT,
	expire TIMESTAMP,
	is_permanent BOOLEAN DEFAULT 0,

	PRIMARY KEY (object_id),
	FOREIGN KEY (object_id) REFERENCES sd_object(id)
) DEFAULT CHARSET=utf8;


/*
CREATE TABLE sd_newobject(
	object_id BIGINT(20) unsigned NOT NULL,
	
	//...

	PRIMARY KEY (object_id),
	FOREIGN KEY (object_id) REFERENCES sd_object(id)
);
*/

