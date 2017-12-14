
CREATE SEQUENCE users_id_seq INCREMENT BY 1;

CREATE TABLE users (
    id INTEGER NOT NULL DEFAULT nextval('users_id_seq'::regclass),
    first_name CHARACTER VARYING(50) NOT NULL,
    last_name CHARACTER VARYING(50) NOT NULL,
    username CHARACTER VARYING(100) NOT NULL,
    email CHARACTER VARYING(100) NOT NULL,
    password CHARACTER VARYING(255) NOT NULL,
    created_at TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITHOUT TIME ZONE,
    active BOOLEAN DEFAULT true,
    CONSTRAINT pkusers PRIMARY KEY (id)
);

INSERT INTO users (
    first_name,
    last_name,
    username,
    email,
    password
) VALUES (
    'Firstname',
    'Lastname',
    'username',
    'username@application.com',
    '$2y$10$luAOyrmQrPn8UEHT/HnTVu/F6uvPGunL0vy7Bku.tvDXFoCrFwbAy'
);
