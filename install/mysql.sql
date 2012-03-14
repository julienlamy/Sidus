SET storage_engine=InnoDB;

CREATE TABLE node_type (
	type_name VARCHAR(20) NOT NULL,
	class_name VARCHAR(30) NOT NULL DEFAULT 'node_generic',
	class_path VARCHAR(60) NOT NULL DEFAULT 'includes/nodes/class.node_generic.php',
	is_active BOOLEAN DEFAULT 1,
	PRIMARY KEY (type_name)
);


INSERT INTO node_type (type_name, class_name, class_path) VALUES ('root', 'node_generic', 'includes/nodes/class.node_generic.php');
INSERT INTO node_type (type_name, class_name, class_path) VALUES ('users', 'node_generic', 'includes/nodes/class.node_generic.php');
INSERT INTO node_type (type_name, class_name, class_path) VALUES ('groups', 'node_generic', 'includes/nodes/class.node_generic.php');
INSERT INTO node_type (type_name, class_name, class_path) VALUES ('user', 'node_user', 'includes/nodes/class.node_user.php');
INSERT INTO node_type (type_name, class_name, class_path) VALUES ('group', 'node_group', 'includes/nodes/class.node_group.php');
INSERT INTO node_type (type_name, class_name, class_path) VALUES ('folder', 'node_generic', 'includes/nodes/class.node_generic.php');
INSERT INTO node_type (type_name, class_name, class_path) VALUES ('page', 'node_generic', 'includes/nodes/class.node_generic.php');
INSERT INTO node_type (type_name, class_name, class_path) VALUES ('comment', 'node_generic', 'includes/nodes/class.node_generic.php');
INSERT INTO node_type (type_name, class_name, class_path) VALUES ('blog', 'node_generic', 'includes/nodes/class.node_generic.php');
INSERT INTO node_type (type_name, class_name, class_path) VALUES ('album', 'node_generic', 'includes/nodes/class.node_generic.php');
INSERT INTO node_type (type_name, class_name, class_path) VALUES ('forum', 'node_generic', 'includes/nodes/class.node_generic.php');
INSERT INTO node_type (type_name, class_name, class_path) VALUES ('topic', 'node_generic', 'includes/nodes/class.node_generic.php');
INSERT INTO node_type (type_name, class_name, class_path) VALUES ('post', 'node_generic', 'includes/nodes/class.node_generic.php');
INSERT INTO node_type (type_name, class_name, class_path) VALUES ('wiki', 'node_generic', 'includes/nodes/class.node_generic.php');
INSERT INTO node_type (type_name, class_name, class_path) VALUES ('article', 'node_generic', 'includes/nodes/class.node_generic.php');
INSERT INTO node_type (type_name, class_name, class_path) VALUES ('revision', 'node_generic', 'includes/nodes/class.node_generic.php');
INSERT INTO node_type (type_name, class_name, class_path) VALUES ('sound', 'node_media', 'includes/nodes/class.node_media.php');
INSERT INTO node_type (type_name, class_name, class_path) VALUES ('video', 'node_media', 'includes/nodes/class.node_media.php');
INSERT INTO node_type (type_name, class_name, class_path) VALUES ('image', 'node_media', 'includes/nodes/class.node_media.php');
INSERT INTO node_type (type_name, class_name, class_path) VALUES ('document', 'node_media', 'includes/nodes/class.node_media.php');
INSERT INTO node_type (type_name, class_name, class_path) VALUES ('icon', 'node_media', 'includes/nodes/class.node_media.php');
INSERT INTO node_type (type_name, class_name, class_path) VALUES ('link', 'node_link', 'includes/nodes/class.node_link.php');


CREATE TABLE allowed_type(
	type_name VARCHAR(20) NOT NULL,
	allowed_type VARCHAR(20) NOT NULL,
	FOREIGN KEY(type_name) REFERENCES node_type(type_name),
	FOREIGN KEY(allowed_type) REFERENCES node_type(type_name),
	PRIMARY KEY(type_name,allowed_type)
);

INSERT INTO allowed_type (type_name,allowed_type) VALUES ('root','folder');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('users','user');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('groups','group');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('user','folder');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('user','icon');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('user','image');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('user','sound');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('user','video');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('user','album');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('user','document');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('user','link');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('user','blog');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('group','icon');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('group','folder');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('group','image');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('group','sound');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('group','video');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('group','album');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('group','document');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('group','link');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('group','blog');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('group','wiki');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('group','forum');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('folder','folder');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('folder','icon');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('folder','page');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('folder','blog');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('folder','album');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('folder','forum');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('folder','wiki');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('folder','sound');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('folder','video');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('folder','image');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('folder','document');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('folder','link');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('page','comment');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('page','icon');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('page','link');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('blog','page');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('blog','icon');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('blog','sound');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('blog','video');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('blog','image');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('blog','document');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('blog','link');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('album','sound');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('album','icon');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('album','video');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('album','image');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('forum','topic');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('forum','icon');
INSERT INTO allowed_type (type_name,objectallowed_type) VALUES ('topic','post');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('topic','icon');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('post','sound');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('post','video');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('post','image');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('post','document');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('post','link');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('wiki','article');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('wiki','icon');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('article','revision');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('article','icon');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('article','comment');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('sound','comment');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('video','comment');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('image','comment');
INSERT INTO allowed_type (type_name,allowed_type) VALUES ('document','comment');

CREATE TABLE node_generic(
	node_id INTEGER NOT NULL AUTO_INCREMENT,
	parent_node_id INTEGER NOT NULL,
	name VARCHAR(128),
	type_name VARCHAR(20) NOT NULL,
	index_num INTEGER DEFAULT 0,
	anonymous_read BOOLEAN DEFAULT 0,
	anonymous_add BOOLEAN DEFAULT 0,
	anonymous_edit BOOLEAN DEFAULT 0,
	anonymous_delete BOOLEAN DEFAULT 0,
	inherit_permissions BOOLEAN DEFAULT 1,
	creation DATETIME,
	modification DATETIME,
	PRIMARY KEY(node_id),
	FOREIGN KEY(parent_node_id) REFERENCES node_generic(node_id),
	FOREIGN KEY(type_name) REFERENCES node_type(type_name)
);
CREATE INDEX titles ON node_generic(title);

CREATE TABLE node_info(
	node_id INTEGER NOT NULL AUTO_INCREMENT,
	slug VARCHAR(128),
	content TEXT,
	tags TEXT,
	PRIMARY KEY(node_id),
	FOREIGN KEY(node_id) REFERENCES node_generic(node_id)
);
CREATE INDEX slugs ON node_info(slug);

CREATE TABLE node_media(
	node_id INTEGER NOT NULL,
	media_type VARCHAR(20),
	filename VARCHAR(50),
	media_url TEXT,
	PRIMARY KEY(node_id),
	FOREIGN KEY(node_id) REFERENCES node_generic(node_id)
);

CREATE TABLE node_user(
	node_id INTEGER NOT NULL,
	username VARCHAR(20),
	password VARCHAR(40),
	salt VARCHAR(40),
	email VARCHAR(40),
	PRIMARY KEY(node_id),
	UNIQUE (username),
	FOREIGN KEY(node_id) REFERENCES node_generic(node_id)
);

CREATE TABLE sys_session(
	session_id INTEGER NOT NULL AUTO_INCREMENT,
	user_id INTEGER NOT NULL,
	signature TEXT,
	remote_addr VARCHAR(40),
	user_agent TEXT,
	expire DATETIME,
	permanent BOOLEAN DEFAULT 0,
	PRIMARY KEY(session_id),
	FOREIGN KEY(user_id) REFERENCES node_user(node_id)
);

CREATE TABLE node_permission(
	entity_id INTEGER NOT NULL,
	node_id INTEGER NOT NULL,
	b_inverse BOOLEAN DEFAULT 0,
	b_read BOOLEAN DEFAULT 0,
	b_add BOOLEAN DEFAULT 0,
	b_edit BOOLEAN DEFAULT 0,
	b_delete BOOLEAN DEFAULT 0,
	b_ownership BOOLEAN DEFAULT 0,
	b_mastership BOOLEAN DEFAULT 0,
	FOREIGN KEY(entity_id) REFERENCES node_generic(node_id),
	FOREIGN KEY(node_id) REFERENCES node_generic(node_id),
	PRIMARY KEY(entity_id,node_id)
);
--
--CREATE TABLE sys_error(
--	error_id INTEGER NOT NULL AUTO_INCREMENT,
--	error_code INTEGER NOT NULL DEFAULT 0,
--	error_date DATETIME,
--	error_severity INTEGER DEFAULT 1,
--	error_message VARCHAR(255),
--	error_file VARCHAR(255),
--	error_line INTEGER,
--	error_method VARCHAR(30),
--	node_id INTEGER,
--	request_uri TEXT,
--	user_id INTEGER,
--	user_agent VARCHAR(255),
--	remote_addr VARCHAR(40),
--	flag TINYINT DEFAULT 0,
--	PRIMARY KEY(error_id)
--);

CREATE TABLE node_data(
	node_id INTEGER NOT NULL,
	data_order INTEGER,
	data_type VARCHAR(10) NOT NULL,
	data_label VARCHAR(20),
	data_value TEXT,
	FOREIGN KEY(node_id) REFERENCES node_generic(node_id)
);

CREATE TABLE node_link(
	node_id INTEGER NOT NULL,
	linked_node_id INTEGER,
	hard_link BOOLEAN DEFAULT 0,
	FOREIGN KEY(node_id) REFERENCES node_generic(node_id)
);

INSERT INTO node_generic(node_id,parent_node_id,type_name,title,creator,index_num,anonymous_read,anonymous_add,inherit_permissions,creation,modification) VALUES (1,1,'root','Home','Admin',1,1,0,0,NOW(),NOW());
INSERT INTO node_generic(node_id,parent_node_id,type_name,title,creator,index_num,anonymous_read,anonymous_add,inherit_permissions,creation,modification) VALUES (2,1,'folder','Site','Admin',1,1,0,0,NOW(),NOW());
INSERT INTO node_generic(node_id,parent_node_id,type_name,title,creator,index_num,anonymous_read,anonymous_add,inherit_permissions,creation,modification) VALUES (3,1,'users','Users','Admin',2,0,1,0,NOW(),NOW());
INSERT INTO node_generic(node_id,parent_node_id,type_name,title,creator,index_num,anonymous_read,anonymous_add,inherit_permissions,creation,modification) VALUES (4,1,'groups','Groups','Admin',3,0,0,0,NOW(),NOW());
INSERT INTO node_generic(node_id,parent_node_id,type_name,title,creator,index_num,anonymous_read,anonymous_add,inherit_permissions,creation,modification) VALUES (7,3,'user','Admin','Admin',1,0,0,0,NOW(),NOW());


INSERT INTO node_info(node_id,content) VALUES (1,'');
INSERT INTO node_info(node_id,content) VALUES (2,'<p>Start your website here...</p>');
INSERT INTO node_info(node_id,content) VALUES (3,'');
INSERT INTO node_info(node_id,content) VALUES (4,'');
INSERT INTO node_info(node_id,content) VALUES (7,'');

INSERT INTO node_user(node_id,username,password,salt,email) VALUES(7,'Admin','10a34637ad661d98ba3344717656fcc76209c2f8','10a34637ad661d98ba3344717656fcc76209c2f8','admin@localhost');
INSERT INTO node_permission(entity_id,node_id,b_ownership,b_mastership) VALUES (7,1,1,1);
INSERT INTO node_permission(entity_id,node_id,b_ownership,b_mastership) VALUES (7,7,1,1);

INSERT INTO node_data(node_id,data_type,data_label,data_value) VALUES (1,'text','lang','fr');
INSERT INTO node_data(node_id,data_type,data_label,data_value) VALUES (1,'text','safe_tags','<a><article><audio><b><big><blockquote><br><cite><code><em><embed><h1><h2><h3><h4><h5><h6><hr><i><img><li><ol><p><pre><section><small><span><strong><sub><sup><table><tbody><td><tr><th><ul><video>');
INSERT INTO node_data(node_id,data_type,data_label,data_value) VALUES (1,'text','thumbs_directory','secure/thumbs/');
INSERT INTO node_data(node_id,data_type,data_label,data_value) VALUES (1,'text','tmp_directory','secure/tmp/');
INSERT INTO node_data(node_id,data_type,data_label,data_value) VALUES (1,'text','previews_directory','secure/previews/');
INSERT INTO node_data(node_id,data_type,data_label,data_value) VALUES (1,'text','originals_directory','secure/originals/');
INSERT INTO node_data(node_id,data_type,data_label,data_value) VALUES (1,'text','icons_directory','images/icons/');
INSERT INTO node_data(node_id,data_type,data_label,data_value) VALUES (1,'integer','max_image_width','800');
INSERT INTO node_data(node_id,data_type,data_label,data_value) VALUES (1,'integer','max_image_height','600');
INSERT INTO node_data(node_id,data_type,data_label,data_value) VALUES (1,'integer','max_thumb_width','160');
INSERT INTO node_data(node_id,data_type,data_label,data_value) VALUES (1,'integer','max_thumb_height','160');
INSERT INTO node_data(node_id,data_type,data_label,data_value) VALUES (1,'integer','default_icon_size','64');
INSERT INTO node_data(node_id,data_type,data_label,data_value) VALUES (1,'boolean','keep_original','1');
INSERT INTO node_data(node_id,data_type,data_label,data_value) VALUES (1,'timestamp','session_expire','604800');
INSERT INTO node_data(node_id,data_type,data_label,data_value) VALUES (1,'text','timezone','Europe/Paris');

