CREATE TABLE tbl_corrida (
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
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

DELETE FROM tbl_corrida;

SELECT * FROM tbl_motorista as m LEFT JOIN tbl_corrida as c ON m.id = c.motorista_id WHERE c.motorista_id is null;

SELECT * FROM tbl_motorista m INNER JOIN tbl_corrida c ON m.status = 'A' WHERE c.status NOT IN ('Em andamento')

insert into tbl_corrida (passageiro_id, motorista_id, endereco_origem, endereco_destino, data_inicio, status, previsao_chegada, tarifa, data_finalizacao) values (1, 1, 'Rua 1', 'Rua 2', '2020-01-01', 'Finalizado', '2020-01-02', 10, '2020-01-03')