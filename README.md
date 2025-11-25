# 🎮 GAME STORE: E-commerce de Jogos

Plataforma de e-commerce construída em PHP para venda de jogos. Inclui catálogo, autenticação completa, carrinho de compras, wishlist e sistema de avaliação de produtos (reviews).

---

## 🚀 Tecnologias

* **Backend:** PHP (Procedural / MySQLi)
* **Banco de Dados:** MySQL
* **Frontend:** HTML5, CSS (Tailwind CSS), **JavaScript (Validação e Interatividade)**
* **Segurança:** Utilização de `password_hash()` e `password_verify()` para senhas.

---

## ⚙️ Instalação e Configuração

### Pré-requisitos
* Ambiente LAMP/XAMPP/MAMP.
* Servidor MySQL.

### Configuração do Banco de Dados

1.  Crie um banco de dados MySQL chamado **`ecommerce_jogos`**.
2.  As tabelas mínimas necessárias (inferidas pelos arquivos) são:
    * `usuarios` (id, nome, email, senha)
    * `jogos` (id, titulo, preco, imagem, etc.)
    * `wishlist` (jogo_id, usuario_id)
    * `avaliacoes` (jogo_id, usuario_id, nota, comentario)
3.  Ajuste as credenciais de conexão no arquivo **`conexao.php`** se necessário.

### Estrutura Base
Copie todos os arquivos `.php` para o diretório raiz do seu projeto no servidor web (ex: `htdocs/game-store/`).

---

## 📁 Estrutura e Funcionalidades (15 Arquivos)

| Arquivo | Categoria | Função Principal |
| :--- | :--- | :--- |
| **`conexao.php`** | **Config** | Gerencia a conexão com o banco de dados MySQL (MySQLi). |
| **`home.php`** | **Frontend** | Página inicial. Exibe o catálogo de jogos, barra de busca e integração com a Wishlist. |
| **`detalhe.php`** | **Frontend** | Página de produto. Exibe informações detalhadas do jogo, a média de notas e o formulário/lista de avaliações. |
| **`wishlist.php`** | **Frontend** | Página dedicada que lista todos os jogos salvos pelo usuário logado na lista de desejos. |
| **`carrinho_view.php`** | **Frontend** | Visualização do carrinho. Lista itens, calcula total e possui a área de "checkout" (simulação). |
| **`login.php`** | **Autenticação** | Formulário de login de usuário. |
| **`cadastro.php`** | **Autenticação** | Formulário para criação de novas contas. |
| **`validar.php`** | **Processamento** | Recebe dados do `login.php`. Valida credenciais (com `password_verify`) e inicia a sessão. |
| **`processarCadastro.php`** | **Processamento** | Recebe dados do `cadastro.php`. Verifica e-mail duplicado e insere novo usuário no banco (com `password_hash`). |
| **`logout.php`** | **Processamento** | Encerra a sessão do usuário e redireciona para a tela de login. |
| **`carrinho.php`** | **Processamento** | Lógica para gerenciar o carrinho de compras (adicionar, remover, aumentar/reduzir quantidade) utilizando a `$_SESSION`. |
| **`processar_wishlist.php`** | **Processamento** | Lógica para adicionar ou remover jogos da lista de desejos (`wishlist` no DB). |
| **`processar_avaliacao.php`**| **Processamento** | Lógica para registrar ou atualizar a avaliação (nota/comentário) do usuário no banco de dados. Utiliza `ON DUPLICATE KEY UPDATE`. |
| **`validaçãoCadastro.js`** | **JavaScript** | **Validação de Formulário Completa** no cliente para o `cadastro.php`. Garante que o **e-mail seja válido**, as senhas **coincidam** e tenham **mínimo de 5 caracteres** antes do envio. |
| **`rating.js`** | **JavaScript** | Sistema Interativo de Avaliação (estrelas) para a página `detalhe.php`. Gerencia a visualização de estrelas (preenchidas/vazias) ao clicar ou passar o mouse. |

---
