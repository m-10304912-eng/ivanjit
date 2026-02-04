-- ========================================================
--  CREATE DATABASE
-- ========================================================
CREATE DATABASE IF NOT EXISTS bola_sepak;
USE bola_sepak;

-- ========================================================
--  TABLE: PENGGUNA_1
-- ========================================================
CREATE TABLE IF NOT EXISTS Pengguna_1 (
    idPengguna VARCHAR(10) PRIMARY KEY,
    kataLaluan VARCHAR(50) NOT NULL,
    namaPengguna VARCHAR(50) NOT NULL
);

INSERT IGNORE INTO Pengguna_1 VALUES
('D6290', 'Fil@090121', 'Lutfil'),
('D6295', 'Abc123456', 'Johnson'),
('D6301', 'Abu_0101', 'Abu');

-- ========================================================
--  TABLE: JAWATAN_1
-- ========================================================
CREATE TABLE IF NOT EXISTS Jawatan_1 (
    idJawatan VARCHAR(5) PRIMARY KEY,
    namaJawatan VARCHAR(50) NOT NULL
);

INSERT IGNORE INTO Jawatan_1 VALUES
('J1', 'Pengerusi'),
('J2', 'Setiausaha'),
('J3', 'Bendahari');

-- ========================================================
--  TABLE: CALON_1
-- ========================================================
CREATE TABLE IF NOT EXISTS Calon_1 (
    idCalon VARCHAR(5) PRIMARY KEY,
    namaCalon VARCHAR(50) NOT NULL,
    kelas VARCHAR(20) NOT NULL,
    gambar VARCHAR(255)
);

INSERT IGNORE INTO Calon_1 VALUES
('P1', 'Sivarama', '4 Cekal', 'Sivarama.png'),
('P2', 'Akif', '4 Bersih', 'Akif.png'),
('P3', 'Arid', '4 Aman', 'Arid.png'),
('S1', 'Jing Hang', '4 Cekal', 'Jing Hang.png'),
('S2', 'Aiman', '4 Aman', 'Aiman.png'),
('S3', 'Chin Hong', '4 Cekal', 'Chin Hong.png'),
('B1', 'Haavinesh', '4 Bersih', 'Haavinesh.png'),
('B2', 'Ian', '4 Aman', 'Ian.png'),
('B3', 'Danial', '4 Bersih', 'Danial.png');

-- ========================================================
--  TABLE: UNDIAN_1 (Fixed structure and constraints)
-- ========================================================
CREATE TABLE IF NOT EXISTS Undian_1 (
    idUndian INT PRIMARY KEY AUTO_INCREMENT,
    idPengguna VARCHAR(10),
    idJawatan VARCHAR(5),
    idCalon VARCHAR(5),
    ip_address VARCHAR(45) NULL, -- Allow null if not provided
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idPengguna) REFERENCES Pengguna_1(idPengguna),
    FOREIGN KEY (idJawatan) REFERENCES Jawatan_1(idJawatan),
    FOREIGN KEY (idCalon) REFERENCES Calon_1(idCalon),
    UNIQUE KEY unique_vote (idPengguna, idJawatan)
);

-- Fixed INSERT statements by specifying column names
INSERT IGNORE INTO Undian_1 (idPengguna, idJawatan, idCalon) VALUES
('D6290', 'J1', 'P1'),
('D6290', 'J2', 'S3'),
('D6290', 'J3', 'B1'),
('D6295', 'J1', 'P2'),
('D6295', 'J2', 'S3'),
('D6295', 'J3', 'B1'),
('D6301', 'J2', 'S2'),
('D6301', 'J3', 'B1'); -- Changed P1 to B1 to match logical candidate for Treasurer
