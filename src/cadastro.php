<?php
// Inicia a sessão
session_start();

if (isset($_SESSION['usuario_logado']) && $_SESSION['usuario_logado'] === true) {
    header("Location: home.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - E-commerce de Jogos</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../JS/validaçãoCadastro.js" defer></script>
</head>
<body class="bg-gray-900 text-white flex items-center justify-center min-h-screen">

    <div class="w-full max-w-sm">
        
        <form id="cadastroForm" action="processarCadastro.php" method="POST" class="bg-gray-800 shadow-md rounded-lg px-8 pt-6 pb-8 mb-4">
            
            <h1 class="text-3xl font-bold text-center mb-6 text-indigo-400">GAME STORE</h1>
            <h2 class="text-xl text-center mb-8">Crie sua Conta</h2>

            <?php
    
            if (isset($_GET['erro'])) {
                $erroMsg = "Ocorreu um erro. Tente novamente.";
                if ($_GET['erro'] == 'email_existente') {
                    $erroMsg = "Este email já está cadastrado.";
                } elseif ($_GET['erro'] == 'db_error') {
                    $erroMsg = "Erro ao se conectar com o banco de dados.";
                }
                echo '<p class="bg-red-500 text-white text-sm font-bold p-3 rounded-md text-center mb-4">' . htmlspecialchars($erroMsg) . '</p>';
            }
            ?>

            <div class="mb-4">
                <label class="block text-gray-400 text-sm font-bold mb-2" for="nome">
                    Nome Completo
                </label>
                <input class="shadow appearance-none border border-gray-700 rounded w-full py-2 px-3 bg-gray-700 text-white leading-tight focus:outline-none focus:shadow-outline" 
                       id="nome" 
                       name="nome" 
                       type="text" 
                       placeholder="Seu nome completo" 
                       required>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-400 text-sm font-bold mb-2" for="email">
                    Email
                </label>
                <input class="shadow appearance-none border border-gray-700 rounded w-full py-2 px-3 bg-gray-700 text-white leading-tight focus:outline-none focus:shadow-outline" 
                       id="email" 
                       name="email" 
                       type="email" 
                       placeholder="seu@email.com" 
                       required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-400 text-sm font-bold mb-2" for="senha">
                    Senha
                </label>
                <input class="shadow appearance-none border border-gray-700 rounded w-full py-2 px-3 bg-gray-700 text-white leading-tight focus:outline-none focus:shadow-outline" 
                       id="senha" 
                       name="senha" 
                       type="password" 
                       placeholder="Mínimo 5 caracteres" 
                       required>
            </div>

            <div class="mb-6">
                <label class="block text-gray-400 text-sm font-bold mb-2" for="confirmar_senha">
                    Confirmar Senha
                </label>
                <input class="shadow appearance-none border border-gray-700 rounded w-full py-2 px-3 bg-gray-700 text-white mb-3 leading-tight focus:outline-none focus:shadow-outline" 
                       id="confirmar_senha" 
                       name="confirmar_senha" 
                       type="password" 
                       placeholder="Repita a senha" 
                       required>
            </div>

            <p id="js-mensagem" class="text-red-500 text-xs italic text-center mb-4"></p>

            <div class="flex flex-col items-center justify-between">
                <button class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                    Cadastrar
                </button>
                
                <a class="inline-block align-baseline font-bold text-sm text-indigo-400 hover:text-indigo-300 mt-4" href="login.php">
                    Já tem uma conta? Faça login
                </a>
            </div>
        </form>
        
        <p class="text-center text-gray-600 text-xs">
            &copy;2025 E-commerce de Jogos.
        </p>
    </div>
</body>
</html>