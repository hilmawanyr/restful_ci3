CREATE DATABASE restful_ci3;

CREATE TABLE medium_rest_ci3.mahasiswa (
	id INT primary key NOT NULL AUTO_INCREMENT,
	nim varchar(12) NOT NULL,
	nama varchar(100) NOT NULL,
	prodi varchar(100) NOT NULL
);

INSERT INTO restful_ci3.mahasiswa  (nim, nama, prodi) values ('201210101010','Kinanti','Industri'), ('201030303030','Damara','Informatika'),('201320202020','Kiki','Manajemen')
