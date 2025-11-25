<?php
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
    <title>Cadastro - Game Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../JS/validaçãoCadastro.js" defer></script>
</head>
<body class="bg-slate-950 text-white flex items-center justify-center min-h-screen relative overflow-hidden">

    <div class="absolute inset-0">
        <div class="absolute bottom-[-10%] left-[-10%] w-96 h-96 bg-indigo-600/30 rounded-full blur-[100px]"></div>
        <div class="absolute top-[-10%] right-[-10%] w-96 h-96 bg-cyan-600/20 rounded-full blur-[100px]"></div>
    </div>

    <div class="w-full max-w-md relative z-10 px-4 py-10">
        
        <form id="cadastroForm" action="processarCadastro.php" method="POST" class="bg-slate-900/80 backdrop-blur-xl border border-slate-800 shadow-2xl rounded-2xl px-8 pt-10 pb-10">
            
            <div class="text-center mb-8">
                <h1 class="text-3xl font-black tracking-tighter mb-2">GAME<span class="text-indigo-500">STORE</span></h1>
                <h2 class="text-slate-400 text-sm font-medium uppercase tracking-widest">Crie sua nova conta</h2>
            </div>

            <?php
            if (isset($_GET['erro'])) {
                $erroMsg = "Ocorreu um erro. Tente novamente.";
                if ($_GET['erro'] == 'email_existente') {
                    $erroMsg = "Este email já está cadastrado.";
                } elseif ($_GET['erro'] == 'db_error') {
                    $erroMsg = "Erro ao conectar com o banco.";
                }
                echo '<div class="bg-red-500/10 border border-red-500/50 text-red-400 text-sm font-bold p-3 rounded-lg text-center mb-6">' . htmlspecialchars($erroMsg) . '</div>';
            }
            ?>

            <div class="mb-4">
                <label class="block text-slate-300 text-xs font-bold mb-2 uppercase tracking-wider" for="nome">Nome Completo</label>
                <input class="w-full bg-slate-950 border border-slate-700 text-white text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-3 outline-none transition-colors" 
                       id="nome" name="nome" type="text" placeholder="Seu nome" required>
            </div>
            
            <div class="mb-4">
                <label class="block text-slate-300 text-xs font-bold mb-2 uppercase tracking-wider" for="email">Email</label>
                <input class="w-full bg-slate-950 border border-slate-700 text-white text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-3 outline-none transition-colors" 
                       id="email" name="email" type="email" placeholder="seu@email.com" required>
            </div>

            <div class="mb-4">
                <label class="block text-slate-300 text-xs font-bold mb-2 uppercase tracking-wider" for="senha">Senha</label>
                <input class="w-full bg-slate-950 border border-slate-700 text-white text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-3 outline-none transition-colors" 
                       id="senha" name="senha" type="password" placeholder="Mínimo 5 caracteres" required>
            </div>

            <div class="mb-6">
                <label class="block text-slate-300 text-xs font-bold mb-2 uppercase tracking-wider" for="confirmar_senha">Confirmar Senha</label>
                <input class="w-full bg-slate-950 border border-slate-700 text-white text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-3 outline-none transition-colors" 
                       id="confirmar_senha" name="confirmar_senha" type="password" placeholder="Repita a senha" required>
            </div>

            <p id="js-mensagem" class="text-red-400 text-xs font-bold text-center mb-6"></p>

            <div class="flex flex-col gap-4">
                <button class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3 px-4 rounded-lg shadow-lg shadow-indigo-500/20 transition-all transform hover:scale-[1.02]" type="submit">
                    Criar Conta
                </button>
                
                <a class="text-center text-sm text-slate-400 hover:text-indigo-400 transition-colors" href="login.php">
                    Já tem uma conta? <span class="font-bold text-white">Faça login</span>
                </a>
            </div>
        </form>
        
        <p class="text-center text-slate-600 text-xs mt-8">
            &copy; 2025 Game Store.
        </p>
    </div>
</body>
</html>
