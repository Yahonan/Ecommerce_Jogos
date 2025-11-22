<?php

session_start();

if (isset($_SESSION['usuario_logado']) && $_SESSION['usuario_logado'] === true) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - E-commerce de Jogos</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white flex items-center justify-center min-h-screen">

    <div class="w-full max-w-sm">
        
        <form action="validar.php" method="POST" class="bg-gray-800 shadow-md rounded-lg px-8 pt-6 pb-8 mb-4">
            
            <h1 class="text-3xl font-bold text-center mb-6 text-indigo-400">GAME STORE</h1>
            <h2 class="text-xl text-center mb-8">Faça seu Login</h2>

            <?php
            // 5. Verificador de Erros
            // Se 'validar.php' falhar, ele redireciona para 'login.php?erro=1'
            // Este 'if' verifica se esse '?erro=1' existe na URL.
            if (isset($_GET['erro']) && $_GET['erro'] == 1) {
                echo '<p class="bg-red-500 text-white text-sm font-bold p-3 rounded-md text-center mb-4">Email ou senha inválidos!</p>';
            }

            // Verificador de Sucesso no Cadastro
            // Se 'processar_cadastro.php' funcionar, ele redireciona para 'login.php?sucesso=cadastro_ok'
            if (isset($_GET['sucesso']) && $_GET['sucesso'] == 'cadastro_ok') {
                echo '<p class="bg-green-500 text-white text-sm font-bold p-3 rounded-md text-center mb-4">Cadastro realizado com sucesso! Faça o login.</p>';
            }
            ?>

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

            <div class="mb-6">
                <label class="block text-gray-400 text-sm font-bold mb-2" for="senha">
                    Senha
                </label>
                <input class="shadow appearance-none border border-gray-700 rounded w-full py-2 px-3 bg-gray-700 text-white mb-3 leading-tight focus:outline-none focus:shadow-outline" 
                       id="senha" 
                       name="senha" 
                       type="password" 
                       placeholder="********" 
                       required>
            </div>

            <div class="flex flex-col items-center justify-between">
                <button class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                    Entrar
                </button>
                
                <a class="inline-block align-baseline font-bold text-sm text-indigo-400 hover:text-indigo-300 mt-4" href="cadastro.php">
                    Ainda não tem uma conta? Cadastre-se
                </a>
            </div>
        </form>
        
        <p class="text-center text-gray-600 text-xs">
            &copy;2025 E-commerce de Jogos.
        </p>
    </div>

</body>
</html>