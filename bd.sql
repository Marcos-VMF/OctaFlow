-- Criando o banco de dados
CREATE DATABASE IF NOT EXISTS octaflow_bd;
USE octaflow_bd;

CREATE TABLE empresas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL
);

CREATE TABLE sistemas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    link_download VARCHAR(255)
);

CREATE TABLE empresa_sistemas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT,
    sistema_id INT,
    FOREIGN KEY (empresa_id) REFERENCES empresas(id) ON DELETE CASCADE,
    FOREIGN KEY (sistema_id) REFERENCES sistemas(id) ON DELETE CASCADE
);

CREATE TABLE checklists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT,
    data DATE,
    ticket VARCHAR(50),
    nome_pre_formatacao VARCHAR(255),
    nome_pos_formatacao VARCHAR(255),
    usuario_antigo VARCHAR(255),
    usuario_novo VARCHAR(255),
    procedimento_inicial TEXT,
    sistema_operacional TEXT,
    backup TEXT,
    local_salvamento VARCHAR(255),
    FOREIGN KEY (empresa_id) REFERENCES empresas(id) ON DELETE CASCADE
);

CREATE TABLE checklist_sistemas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    checklist_id INT,
    sistema_id INT,
    FOREIGN KEY (checklist_id) REFERENCES checklists(id) ON DELETE CASCADE,
    FOREIGN KEY (sistema_id) REFERENCES sistemas(id) ON DELETE CASCADE
);

-- Criando a tabela de checklists de manutenção
CREATE TABLE checklists_manutencao (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT NOT NULL,
    data DATE NOT NULL,
    ticket VARCHAR(50) NOT NULL,
    equipamento ENUM('computador', 'notebook', 'servidor') NOT NULL,
    modelo VARCHAR(255),
    acompanha_carregador BOOLEAN DEFAULT FALSE,
    nome_maquina VARCHAR(255),
    nao_tem_nome BOOLEAN DEFAULT FALSE,
    processador VARCHAR(255),
    memoria_ram INT,
    armazenamento_tipo ENUM('hd', 'ssd', 'nenhum'),
    capacidade_armazenamento INT,
    defeitos TEXT,
    servicos_realizados TEXT,
    observacoes TEXT,
    FOREIGN KEY (empresa_id) REFERENCES empresas(id) ON DELETE CASCADE
);