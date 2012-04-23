SET STORAGE_ENGINE = InnoDB;
SET NAMES 'utf8' COLLATE 'utf8_general_ci';
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS sd_object_type;
DROP TABLE IF EXISTS sd_allowed_object_type;
DROP TABLE IF EXISTS sd_forbidden_object_type;
DROP TABLE IF EXISTS sd_node;
DROP TABLE IF EXISTS sd_version;
DROP TABLE IF EXISTS sd_object;
DROP TABLE IF EXISTS sd_info;
DROP TABLE IF EXISTS sd_permission;
DROP TABLE IF EXISTS sd_user;
DROP TABLE IF EXISTS sd_session;

CREATE TABLE IF NOT EXISTS sd_object_type (
	object_id BIGINT(20) unsigned NOT NULL,

	type_name VARCHAR(30) NOT NULL,
	class_name VARCHAR(30) NOT NULL DEFAULT '\Sidus\Node',
	class_path VARCHAR(60) NOT NULL DEFAULT 'lib/nodes/class.simple.php',

	PRIMARY KEY (object_id),
	FOREIGN KEY (object_id) REFERENCES sd_object(id)
);

CREATE TABLE IF NOT EXISTS sd_allowed_object_type(
	object_type_id BIGINT(20) unsigned NOT NULL,
	allowed_object_type_id BIGINT(20) unsigned NOT NULL,

	FOREIGN KEY(object_type_id) REFERENCES sd_object_type(object_id),
	FOREIGN KEY(allowed_object_type_id) REFERENCES sd_object_type(object_id),
	PRIMARY KEY(object_type_id, allowed_object_type_id)
);

CREATE TABLE IF NOT EXISTS sd_forbidden_object_type(
	object_type_id BIGINT(20) unsigned NOT NULL,
	forbidden_object_type_id BIGINT(20) unsigned NOT NULL,

	FOREIGN KEY(object_type_id) REFERENCES sd_object_type(object_id),
	FOREIGN KEY(forbidden_object_type_id) REFERENCES sd_object_type(object_id),
	PRIMARY KEY(object_type_id, forbidden_object_type_id)
);

CREATE TABLE IF NOT EXISTS sd_node (
	id BIGINT(20) unsigned NOT NULL AUTO_INCREMENT,
	parent_id BIGINT(20) unsigned NOT NULL,
	object_id BIGINT(20) unsigned NOT NULL,
	object_type_id BIGINT(20) unsigned NOT NULL,

	inherit_permissions BOOLEAN NOT NULL DEFAULT 1,

	PRIMARY KEY (id),
	FOREIGN KEY (parent_id) REFERENCES sd_node(id),
	FOREIGN KEY (object_id) REFERENCES sd_object(id),
	FOREIGN KEY (object_type_id) REFERENCES sd_object_type(object_id)
);

CREATE TABLE IF NOT EXISTS sd_version (
	node_id BIGINT(20) unsigned NOT NULL,
	lang VARCHAR(4) NOT NULL DEFAULT 'en',
	revision_number INTEGER NOT NULL,
	object_id BIGINT(20) unsigned NOT NULL,

	revision_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

	PRIMARY KEY (node_id, lang, revision_number, object_id),
	FOREIGN KEY (node_id) REFERENCES sd_node(id),
	FOREIGN KEY (object_id) REFERENCES sd_object(id)
);

CREATE TABLE IF NOT EXISTS sd_object (
	id BIGINT(20) unsigned NOT NULL AUTO_INCREMENT,
	
	created_by BIGINT(20) unsigned,
	created_at TIMESTAMP NOT NULL,
	modified_by BIGINT(20) unsigned,
	modified_at TIMESTAMP NOT NULL,

	PRIMARY KEY (id),
	FOREIGN KEY (created_by) REFERENCES sd_user(object_id),
	FOREIGN KEY (modified_by) REFERENCES sd_user(object_id)
);

CREATE TABLE IF NOT EXISTS sd_info(
	object_id BIGINT(20) unsigned NOT NULL,

	title VARCHAR(256),
	slug VARCHAR(128),
	content TEXT,
	tags TEXT,

	PRIMARY KEY (object_id),
	FOREIGN KEY (object_id) REFERENCES sd_object(id)
);

CREATE TABLE IF NOT EXISTS sd_permission (
	object_id BIGINT(20) unsigned NOT NULL,
	entity_id BIGINT(20) unsigned NOT NULL,

	permission_set TINYINT(3) unsigned NOT NULL DEFAULT 0,
	
	PRIMARY KEY (object_id, entity_id),
	FOREIGN KEY (object_id) REFERENCES sd_object(id),
	FOREIGN KEY (entity_id) REFERENCES sd_node(id)
);

CREATE TABLE IF NOT EXISTS sd_user(
	object_id BIGINT(20) unsigned NOT NULL,
	username VARCHAR(32),
	password VARCHAR(40),
	salt VARCHAR(40),
	email VARCHAR(50),
	expire TIMESTAMP,

	PRIMARY KEY (object_id),
	FOREIGN KEY (object_id) REFERENCES sd_object(id),
	UNIQUE (username)
);

CREATE TABLE IF NOT EXISTS sd_session(
	object_id BIGINT(20) unsigned NOT NULL,

	cookie VARCHAR(40),
	remote_addr VARCHAR(40),
	user_agent TEXT,
	expire TIMESTAMP NOT NULL,
	is_permanent BOOLEAN NOT NULL DEFAULT 0,

	PRIMARY KEY (object_id),
	FOREIGN KEY (object_id) REFERENCES sd_object(id)
);


/*
CREATE TABLE sd_newobject(
	object_id BIGINT(20) unsigned NOT NULL,
	
	//...

	PRIMARY KEY (object_id),
	FOREIGN KEY (object_id) REFERENCES sd_object(id)
);
*/

SET FOREIGN_KEY_CHECKS = 1;