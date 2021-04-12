DROP DATABASE IF EXISTS kriekon;
CREATE DATABASE kriekon;
USE kriekon;

CREATE TABLE user
(
	USERID				INT				PRIMARY KEY 	AUTO_INCREMENT,
    first_name			VARCHAR(45)		NOT NULL DEFAULT '',
    last_name			VARCHAR(45)		NOT NULL DEFAULT '',
    email				VARCHAR(255)	NOT NULL,
    username			VARCHAR(45)		NOT NULL DEFAULT '',
    password_hash		VARCHAR(255)	NOT NULL,
    date_of_birth		DATE			NOT NULL,
    registration_time	DATETIME		NOT NULL,
    verified			TINYINT(1)		DEFAULT 0,
    locked				TINYINT(1)		DEFAULT 0
);

CREATE TABLE user_status
(
	STATUSID				INT			PRIMARY KEY		AUTO_INCREMENT,
    USERID					INT			NOT NULL,
    status_date				DATETIME	NOT NULL,
    status_modified_date	DATETIME	NOT NULL 		DEFAULT '0000-00-00 00:00:00',
    status_content			TEXT		NOT NULL,

    CONSTRAINT user_status_USERID_fk
		FOREIGN KEY (USERID)
        REFERENCES user(USERID)
);

CREATE TABLE user_status_comments
(
	COMMENTID				INT 		PRIMARY KEY		AUTO_INCREMENT,
    STATUSID				INT 		NOT NULL,
    PARENTID				INT			NOT NULL		DEFAULT 0,
    USERID					INT			NOT NULL,
    level                   TINYINT(1)  NOT NULL		DEFAULT 0,
    comment_date			DATETIME	NOT NULL,
    comment_modified_date	DATETIME	NULL,
    comment_content			TEXT		NOT NULL,

    CONSTRAINT user_status_comment_USERID_fk
		FOREIGN KEY (USERID)
        REFERENCES user(USERID),
	CONSTRAINT user_status_comment_STATUSID_fk
		FOREIGN KEY (STATUSID)
        REFERENCES user_status(STATUSID)
);

DROP DATABASE IF EXISTS kriekon_log;
CREATE DATABASE kriekon_log;
USE kriekon_log;
CREATE TABLE log
(
	LOGID			INT				PRIMARY KEY 	AUTO_INCREMENT,
	LOGTYPEID		TINYINT(4)		NOT NULL,
    level			TINYINT(1)		NOT NULL,
    date			DATETIME		NOT NULL,
    details			VARCHAR(255)	NOT NULL
);

CREATE TABLE log_USERID
(
	LOGID			INT				PRIMARY KEY 	AUTO_INCREMENT,
	LOGTYPEID		TINYINT(4)		NOT NULL,
    USERID			INT				NOT NULL,
    level			TINYINT(1)		NOT NULL,
    date			DATETIME		NOT NULL,
    details			VARCHAR(255)	NOT NULL
);