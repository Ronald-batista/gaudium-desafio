CREATE TABLE tbl_motorista (
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    nome VARCHAR(128) NOT NULL,
    email VARCHAR(128) NOT NULL,
    telefone VARCHAR(128) NOT NULL,
    status CHAR(1) NOT NULL,
    data DATE NOT NULL,
    placa VARCHAR(8) NOT NULL,
    observacao VARCHAR(200)
);