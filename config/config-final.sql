-- Criação do banco de dados
CREATE DATABASE geometria2;
USE geometria2;

-- Tabela Unidades
CREATE TABLE unidades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo VARCHAR(50) NOT NULL, 
    unidade VARCHAR(10) NOT NULL 
);

-- Tabela Forma
CREATE TABLE formas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cor VARCHAR(20) NOT NULL,
    fundo VARCHAR(20),
    id_unidade INT NOT NULL,
    FOREIGN KEY (id_unidade) REFERENCES unidades(id)
);

-- Tabela Círculo
CREATE TABLE circulos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_forma INT NOT NULL, 
    raio DOUBLE NOT NULL,
    FOREIGN KEY (id_forma) REFERENCES formas(id)
);

-- Tabela Quadrado
CREATE TABLE quadrados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_forma INT NOT NULL,
    lado INT NOT NULL,
    FOREIGN KEY (id_forma) REFERENCES formas(id)
);

-- Tabela Triângulo
CREATE TABLE triangulos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_forma INT NOT NULL, 
    lado1 INT NOT NULL,
    lado2 INT NOT NULL,
    lado3 INT NOT NULL,
    FOREIGN KEY (id_forma) REFERENCES formas(id)
);

-- Tabela Triângulo Isósceles
CREATE TABLE triangulos_isosceles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_triangulo INT NOT NULL,
      lado1 DOUBLE NOT NULL,
    lado2 double not null,
    FOREIGN KEY (id_triangulo) REFERENCES triangulos(id)
);
ALTER TABLE triangulos_isosceles ADD COLUMN cor VARCHAR(7);

-- Tabela Triângulo Equilátero
CREATE TABLE triangulos_equilateros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_triangulo INT NOT NULL,
    altura DOUBLE NOT NULL,
    FOREIGN KEY (id_triangulo) REFERENCES triangulos(id)
);

-- Tabela Triângulo Escaleno
CREATE TABLE triangulos_escalenos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_triangulo INT NOT NULL,
    lado1 INT NOT NULL,
    lado2 INT NOT NULL,
    lado3 INT NOT NULL,
    FOREIGN KEY (id_triangulo) REFERENCES triangulos(id)
);
ALTER TABLE triangulos_escalenos ADD COLUMN cor VARCHAR(7);
SELECT te.*, u.id as id_unidade, u.tipo 
FROM triangulos_escalenos te 
JOIN unidades u ON te.id_triangulo = u.id;

ALTER TABLE triangulos_escalenos ADD COLUMN cor VARCHAR(7);	
SELECT te.*, u.id as id_unidade, u.tipo 
FROM triangulos_escalenos te JOIN unidades u ON te.id_triangulo = u.id;

CREATE TABLE unidades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    unidade VARCHAR(255) NOT NULL,
    tipo VARCHAR(255) DEFAULT 'indefinido'
);

INSERT INTO unidades (tipo, unidade) VALUES ('px', 'px');

ALTER TABLE triangulos_equilateros
DROP FOREIGN KEY triangulos_equilateros_ibfk_1;

ALTER TABLE triangulos_equilateros
ADD CONSTRAINT triangulos_equilateros_ibfk_1
FOREIGN KEY (id_triangulo) REFERENCES triangulos(id)
ON DELETE CASCADE;

ALTER TABLE circulos ADD COLUMN cor VARCHAR(7) NOT NULL AFTER raio;

-- Remover a constraint existente
ALTER TABLE circulos DROP FOREIGN KEY circulos_ibfk_1;

-- Renomear a coluna 'id_forma' para 'id_unidade'
ALTER TABLE circulos CHANGE id_forma id_unidade INT NOT NULL;

-- Criar nova constraint para referenciar a tabela 'unidades'
ALTER TABLE circulos ADD CONSTRAINT fk_circulos_unidades FOREIGN KEY (id_unidade) REFERENCES unidades(id);

ALTER TABLE triangulos_isosceles ADD COLUMN cor VARCHAR(7);

-- Remover a constraint existente
ALTER TABLE circulos DROP FOREIGN KEY circulos_ibfk_1;

-- Renomear a coluna 'id_forma' para 'id_unidade'
ALTER TABLE circulos CHANGE id_forma id_unidade INT NOT NULL;

-- Criar nova constraint para referenciar a tabela 'unidades'
ALTER TABLE circulos ADD CONSTRAINT fk_circulos_unidades FOREIGN KEY (id_unidade) REFERENCES unidades(id);

ALTER TABLE triangulos_equilateros ADD COLUMN lado INT NOT NULL;
ALTER TABLE triangulos_equilateros CHANGE altura cor VARCHAR(7);