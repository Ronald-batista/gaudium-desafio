CREATE TABLE tbl_corrida (
    passageiro_id INTEGER,
    motorista_id INTEGER,
    endereco_origem VARCHAR(256) NOT NULL,
    endereco_destino VARCHAR(256) NOT NULL,
    data_inicio DATE NOT NULL, 
    status VARCHAR(30) NOT NULL,
    previsao_chegada DATE NOT NULL,
    tarifa FLOAT NOT NULL,
    data_finalizacao DATE NOT NULL,
    FOREIGN KEY (passageiro_id) REFERENCES tbl_passageiro(id),
    FOREIGN KEY (motorista_id) REFERENCES tbl_motorista(id)
);