# 🎮 GAME STORE: E-commerce de Jogos



Uma plataforma completa de e-commerce construída em PHP para a venda de jogos digitais. O projeto abrange desde o catálogo de produtos e autenticação segura até a gestão de carrinho, lista de desejos e um sistema de avaliação robusto.

---

## ✨ Funcionalidades Principais

* **Catálogo Interativo:** Visualização de todos os jogos com busca e filtros.
* **Autenticação Segura:** Login e cadastro com `password_hash()` e `password_verify()`.
* **Carrinho de Compras:** Gestão de itens em sessão (`$_SESSION`).
* **Wishlist:** Adicionar/remover jogos da lista de desejos (armazenado no DB).
* **Reviews de Produtos:** Sistema de avaliação com notas (estrelas) e comentários.
* **Gestão de Pedidos:** Finalização de compra e histórico de pedidos detalhado.

---

## 🚀 Tecnologias Utilizadas

| Categoria | Tecnologia | Detalhe |
| :--- | :--- | :--- |
| **Backend** | **PHP** | Lógica de negócio, processamento e interação com o DB (estilo Procedural com MySQLi). |
| **Banco de Dados** | **MySQL** | Armazenamento de usuários, jogos, reviews e pedidos. |
| **Frontend** | **HTML5 / CSS** | Estrutura e Estilização (utilizando **Tailwind CSS** para agilidade). |
| **Interatividade** | **JavaScript** | Validação de formulários no cliente e interatividade do sistema de avaliação (estrelas). |
| **Segurança** | **PHP Built-in** | Uso de `password_hash()` para armazenamento de senhas e `password_verify()` para validação. |

---

## ⚙️ Instalação e Configuração

### Pré-requisitos
Certifique-se de ter um ambiente de desenvolvimento web configurado:
* Ambiente **LAMP/XAMPP/MAMP** instalado e funcionando.
* Servidor **MySQL** ativo.

### 1. Configuração do Banco de Dados

1.  Crie um banco de dados MySQL chamado **`ecommerce_jogos`**.
2.  Crie as seguintes tabelas (estrutura mínima inferida pelo projeto):

    ```sql
    -- Estrutura básica das tabelas
    CREATE TABLE usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        senha VARCHAR(255) NOT NULL -- password_hash() armazena 255+ caracteres
    );

    CREATE TABLE jogos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titulo VARCHAR(255) NOT NULL,
        preco DECIMAL(10, 2) NOT NULL,
        imagem VARCHAR(255) 
        -- outros campos de produto (descricao, categoria, etc.)
    );

    CREATE TABLE wishlist (
        jogo_id INT NOT NULL,
        usuario_id INT NOT NULL,
        PRIMARY KEY (jogo_id, usuario_id),
        FOREIGN KEY (jogo_id) REFERENCES jogos(id),
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
    );

    CREATE TABLE avaliacoes (
        jogo_id INT NOT NULL,
        usuario_id INT NOT NULL,
        nota TINYINT NOT NULL CHECK (nota BETWEEN 1 AND 5),
        comentario TEXT,
        data_avaliacao DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (jogo_id, usuario_id), -- Garante que um usuário só pode avaliar um jogo uma vez
        FOREIGN KEY (jogo_id) REFERENCES jogos(id),
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
    );

    CREATE TABLE pedidos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT NOT NULL,
        valor_total DECIMAL(10, 2) NOT NULL,
        data_pedido DATETIME DEFAULT CURRENT_TIMESTAMP,
        status VARCHAR(50) DEFAULT 'Aprovado',
        metodo_pagamento VARCHAR(50),
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
    );

    CREATE TABLE itens_pedido (
        pedido_id INT NOT NULL,
        jogo_id INT NOT NULL,
        quantidade INT NOT NULL,
        preco_unitario DECIMAL(10, 2) NOT NULL,
        PRIMARY KEY (pedido_id, jogo_id),
        FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
        FOREIGN KEY (jogo_id) REFERENCES jogos(id)
    );
    ```

3.  Ajuste as credenciais de conexão no arquivo **`conexao.php`** (se necessário):
    ```php
    $conn = new mysqli("localhost", "USUARIO_DB", "SENHA_DB", "ecommerce_jogos");
    ```

### 2. Estrutura de Arquivos
* Copie todos os arquivos `.php` e `.js` para o diretório raiz do seu projeto no servidor web (ex: `htdocs/game-store/`).

---

## 📁 Estrutura e Funcionalidades (17 Arquivos)

| Arquivo | Categoria | Função Principal |
| :--- | :--- | :--- |
| **`conexao.php`** | **Config** | Gerencia a conexão com o banco de dados MySQL (MySQLi). |
| **`home.php`** | **Frontend** | Página inicial. Exibe o catálogo de jogos, barra de busca e integração com a Wishlist. |
| **`detalhe.php`** | **Frontend** | Página de produto. Exibe informações detalhadas do jogo, a média de notas e o formulário/lista de avaliações. |
| **`wishlist.php`** | **Frontend** | Página dedicada que lista todos os jogos salvos pelo usuário logado na lista de desejos. |
| **`carrinho_view.php`** | **Frontend** | Visualização do carrinho. Lista itens, calcula total e possui a área de "checkout" (simulação). |
| **`meus_pedidos.php`** | **Frontend** | Exibe o histórico de pedidos do usuário com detalhes dos itens comprados. |
| **`login.php`** | **Autenticação** | Formulário de login de usuário. |
| **`cadastro.php`** | **Autenticação** | Formulário para criação de novas contas. |
| **`validar.php`** | **Processamento** | Recebe dados do `login.php`. Valida credenciais (com `password_verify`) e inicia a sessão. |
| **`processarCadastro.php`** | **Processamento** | Recebe dados do `cadastro.php`. Verifica e-mail duplicado e insere novo usuário no banco (com `password_hash`). |
| **`logout.php`** | **Processamento** | Encerra a sessão do usuário e redireciona para a tela de login. |
| **`carrinho.php`** | **Processamento** | Lógica para gerenciar o carrinho de compras (adicionar, remover, aumentar/reduzir quantidade) utilizando a `$_SESSION`. |
| **`processar_wishlist.php`** | **Processamento** | Lógica para adicionar ou remover jogos da lista de desejos (`wishlist` no DB). |
| **`processar_avaliacao.php`**| **Processamento** | Lógica para registrar ou atualizar a avaliação (nota/comentário) do usuário no banco de dados. Utiliza `ON DUPLICATE KEY UPDATE`. |
| **`processar_pagamento.php`** | **Processamento** | Finaliza a compra. Calcula o total, insere o Pedido e os Itens do Pedido no DB e limpa o carrinho (`$_SESSION`). |
| **`validaçãoCadastro.js`** | **JavaScript** | Validação de Formulário no cliente para `cadastro.php`. (E-mail válido, senhas coincidentes, mínimo de 5 caracteres). |
| **`rating.js`** | **JavaScript** | Sistema Interativo de Avaliação (estrelas) para `detalhe.php`. |

---
