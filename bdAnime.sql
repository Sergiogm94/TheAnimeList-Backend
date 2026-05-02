CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usuario VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    contraseña_hash VARCHAR(255) NOT NULL
);

CREATE TABLE animes (
    id_anime INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    tipo VARCHAR(50),
    sipnosis TEXT,
    imagen VARCHAR(255)
);

CREATE TABLE personajes (
    id_personaje INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    imagen VARCHAR(255)
);

CREATE TABLE anime_personaje (
    id_anime INT NOT NULL,
    id_personaje INT NOT NULL,
    rol VARCHAR(50),
    PRIMARY KEY (id_anime, id_personaje),
    CONSTRAINT fk_ap_anime
        FOREIGN KEY (id_anime) REFERENCES animes(id_anime)
        ON DELETE CASCADE,
    CONSTRAINT fk_ap_personaje
        FOREIGN KEY (id_personaje) REFERENCES personajes(id_personaje)
        ON DELETE CASCADE
);

CREATE TABLE usuario_anime (
    id_usuario INT NOT NULL,
    id_anime INT NOT NULL,
    puntuacion TINYINT,
    estado ENUM('viendo', 'completado', 'abandonado', 'planificado', 'favorito'),
    fecha DATE,
    PRIMARY KEY (id_usuario, id_anime),
    CONSTRAINT fk_ua_usuario
        FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
        ON DELETE CASCADE,
    CONSTRAINT fk_ua_anime
        FOREIGN KEY (id_anime) REFERENCES animes(id_anime)
        ON DELETE CASCADE
);

CREATE TABLE comentarios (
    id_comentario INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_foro INT,
    contenido TEXT NOT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_com_usuario
        FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
        ON DELETE CASCADE
);