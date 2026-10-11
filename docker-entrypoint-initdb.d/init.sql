CREATE TABLE "user" (
                        id INT PRIMARY KEY,
                        description TEXT DEFAULT NULL,
                        status INTEGER DEFAULT NULL,
                        is_verified BOOLEAN NOT NULL DEFAULT FALSE,
                        email VARCHAR(180) NOT NULL,
                        password VARCHAR(255) NOT NULL,
                        plain_password VARCHAR(255) DEFAULT NULL,
                        username VARCHAR(180) NOT NULL,
                        email_canonical VARCHAR(180) NOT NULL,
                        username_canonical VARCHAR(180) NOT NULL,
                        roles JSON NOT NULL,
                        enabled BOOLEAN DEFAULT NULL,
                        confirmation_token VARCHAR(180) DEFAULT NULL,
                        last_login TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
                        salt VARCHAR(255) DEFAULT NULL,
                        password_requested_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL
);

CREATE UNIQUE INDEX uniq_user_email ON "user" (email);
CREATE UNIQUE INDEX uniq_user_username ON "user" (username);
CREATE UNIQUE INDEX uniq_user_email_canonical ON "user" (email_canonical);
CREATE UNIQUE INDEX uniq_user_username_canonical ON "user" (username_canonical);

INSERT INTO "user" (
    id, description, status, is_verified, email, password, plain_password,
    username, email_canonical, username_canonical, roles, enabled,
    confirmation_token, last_login, salt, password_requested_at
) VALUES (
             1, NULL, NULL, false, 'admin@admin.fr',
             '$2y$13$bk6qRHllaNuaoqvOxfAEZO14mIwdXYZEPGzBZ75NA7MQ3mZw09wHm',
             NULL, 'admin', 'admin@admin.fr', 'admin',
             '["ROLE_ADMIN", "ROLE_USER"]', true,
             NULL, '2026-10-11 13:13:22', NULL, NULL
         );

SELECT setval(pg_get_serial_sequence('"user"', 'id'), (SELECT MAX(id) FROM "user"));
CREATE TABLE sessions (
    sess_id VARCHAR(128) NOT NULL PRIMARY KEY,
    sess_data BYTEA NOT NULL,
    sess_lifetime INTEGER NOT NULL,
    sess_time INTEGER NOT NULL
);