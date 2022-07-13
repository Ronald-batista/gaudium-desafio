CREATE TABLE tbl_passageiro (
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    nome VARCHAR(128) NOT NULL,
    email VARCHAR(128) NOT NULL,
    telefone VARCHAR(128) NOT NULL,
    status CHAR(1) NOT NULL,
    data DATE NOT NULL,
    observacao VARCHAR(200)
);

