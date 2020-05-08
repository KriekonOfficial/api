DROP DATABASE IF EXISTS kriekon;
CREATE DATABASE kriekon;
USE kriekon;

CREATE TABLE account
(
	ACCTID				INT				PRIMARY KEY 	AUTO_INCREMENT,
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

CREATE TABLE account_auth
(
	ACCTID					INT				UNIQUE KEY 		NOT NULL,
    last_logged_in			DATETIME		NULL,
    ip_address				VARCHAR(255)	NULL,
    logged_in				INT				DEFAULT 0,
    
    CONSTRAINT account_account_auth_fk
		FOREIGN KEY (ACCTID)
        REFERENCES account(ACCTID)
);

CREATE TABLE account_api
(
	APIKEYID	INT				NOT NULL	PRIMARY KEY		AUTO_INCREMENT,
	ACCTID		INT 			NOT NULL,
    API_KEY		VARCHAR(255)	NOT NULL	UNIQUE KEY,
    
	CONSTRAINT account_api_account_ACCTID_fk
		FOREIGN KEY (ACCTID)
        REFERENCES account(ACCTID)
);

CREATE TABLE account_api_settings
(
	APISETTINGID	INT				NOT NULL	PRIMARY KEY		AUTO_INCREMENT,
    APIKEYID		INT				NOT NULL,
    ip_address		VARCHAR(255)	NULL,
    
    CONSTRAINT account_api_setting_API_KEY_fk
		FOREIGN KEY (APIKEYID)
        REFERENCES account_api(APIKEYID)
);

#CREATE TABLE account_permissions
#(
	
#);

CREATE TABLE oauth_server
(
	ACCTID						INT				NOT NULL,
    access_secret				BLOB			NULL,
    access_iv					BLOB			NULL,
    access_tag					BLOB			NULL,
    access_token				BLOB			NULL,
    refresh_token				BLOB			NULL,
    access_token_expiration		DATETIME		NULL,
    refresh_token_expiration	DATETIME		NULL,
    
    CONSTRAINT account_oauth_fk
		FOREIGN KEY (ACCTID)
        REFERENCES account(ACCTID)
);

CREATE TABLE account_status
(
	STATUSID				INT			PRIMARY KEY		AUTO_INCREMENT,
    ACCTID					INT			NOT NULL,
    status_date				DATETIME	NOT NULL,
    status_modified_date	DATETIME	NULL,
    status_content			TEXT		NOT NULL,
    
    CONSTRAINT account_status_ACCTID_fk
		FOREIGN KEY (ACCTID)
        REFERENCES account(ACCTID)
);

CREATE TABLE account_status_comments
(
	COMMENTID				INT 		PRIMARY KEY		AUTO_INCREMENT,
    STATUSID				INT 		NOT NULL,
    PARENTID				INT			NOT NULL		DEFAULT 0,
    ACCTID					INT			NOT NULL,
    comment_date			DATETIME	NOT NULL,
    comment_modified_date	DATETIME	NULL,
    comment_content			TEXT		NOT NULL,
    
    CONSTRAINT account_status_comment_ACCTID_fk
		FOREIGN KEY (ACCTID)
        REFERENCES account(ACCTID),
	CONSTRAINT account_status_comment_STATUSID_fk
		FOREIGN KEY (STATUSID)
        REFERENCES account_status(STATUSID)
);

CREATE TABLE account_friends
(
	FRIENDID			INT 		PRIMARY KEY			AUTO_INCREMENT,
    LINK_ACCTID1		INT			NOT NULL,
    LINK_ACCTID2		INT			NOT NULL,
    requested			INT			DEFAULT 0,
    accepted			INT			DEFAULT 0,
    request_date		DATETIME	NOT NULL,
    accepted_date		DATETIME	NULL,
    
    CONSTRAINT account_LINK_ACCTID1_fk
		FOREIGN KEY (LINK_ACCTID1)
        REFERENCES account(ACCTID),
	CONSTRAINT account_LINK_ACCTID2_fk
		FOREIGN KEY (LINK_ACCTID2)
        REFERENCES account(ACCTID)
);

CREATE TABLE account_followers
(
	FOLLOWID			INT			PRIMARY KEY		AUTO_INCREMENT,
    ACCTID				INT			NOT NULL,
    FOLLOWING_ACCTID	INT 		NOT NULL,
    following_date		DATETIME	NOT NULL,
    
    CONSTRAINT account_followers_ACCTID1_fk
		FOREIGN KEY (ACCTID)
        REFERENCES account(ACCTID),
    CONSTRAINT account_followers_FOLLOWING_ACCTID2_fk
		FOREIGN KEY (FOLLOWING_ACCTID)
        REFERENCES account(ACCTID)
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

CREATE TABLE log_ACCTID
(
	LOGID			INT				PRIMARY KEY 	AUTO_INCREMENT,
	LOGTYPEID		TINYINT(4)		NOT NULL,
    ACCTID			INT				NOT NULL,
    level			TINYINT(1)		NOT NULL,
    date			DATETIME		NOT NULL,
    details			VARCHAR(255)	NOT NULL
);