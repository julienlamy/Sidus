SET storage_engine=InnoDB;

CREATE TABLE sd_object_type (
	id INTEGER unsigned NOT NULL AUTO_INCREMENT,
	parent_id INTEGER unsigned NOT NULL,

	type_name VARCHAR(20) NOT NULL,
	class_name VARCHAR(30) NOT NULL DEFAULT 'Node',
	class_path VARCHAR(60) NOT NULL DEFAULT 'lib/nodes/class.node_generic.php',
	description TEXT,

	PRIMARY KEY (id),
	FOREIGN KEY (parent_id) REFERENCES sd_object_type(id)
);

CREATE TABLE sd_allowed_object_type(
	type_id INTEGER unsigned NOT NULL,
	allowed_type_id INTEGER unsigned NOT NULL,

	FOREIGN KEY(type_id) REFERENCES sd_object_type(id),
	FOREIGN KEY(allowed_type_id) REFERENCES sd_object_type(id),
	PRIMARY KEY(type_id, allowed_type_id)
);

CREATE TABLE IF NOT EXISTS sd_node (
	id BIGINT(20) unsigned NOT NULL AUTO_INCREMENT,
	parent_id BIGINT(20) unsigned NOT NULL,
	role_id BIGINT(20) unsigned NOT NULL,
	owner_id BIGINT(20) unsigned NOT NULL,

	is_read BOOLEAN DEFAULT 0,
	is_add BOOLEAN DEFAULT 0,
	is_edit BOOLEAN DEFAULT 0,
	is_delete BOOLEAN DEFAULT 0,
	is_admin BOOLEAN DEFAULT 1,

	PRIMARY KEY (id),
	FOREIGN KEY (parent_id) REFERENCES sd_node(id),
	FOREIGN KEY (role_id) REFERENCES sd_node(id),
	FOREIGN KEY (owner_id) REFERENCES sd_node(id),
) DEFAULT CHARSET=ascii;

CREATE TABLE IF NOT EXISTS sd_version (
	node_id BIGINT(20) unsigned NOT NULL,
	lang VARCHAR(4),
	version_date TIMESTAMP,
	object_id BIGINT(20) unsigned NOT NULL,

	PRIMARY KEY (node_id, lang, version_date, object_id)
) DEFAULT CHARSET=ascii;

CREATE TABLE IF NOT EXISTS sd_object (
	id BIGINT(20) unsigned NOT NULL AUTO_INCREMENT,
	type_id INTEGER NOT NULL,

	created_by BIGINT(20),
	created_at TIMESTAMP,
	modified_by BIGINT(20),
	modified_at TIMESTAMP,

	PRIMARY KEY (id),
	FOREIGN KEY (type_id) REFERENCES sd_object_type(id),
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

