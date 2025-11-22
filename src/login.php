<?php
session_start();

if (isset($_SESSION['usuario_logado']) && $_SESSION['usuario_logado'] === true) {
    header("Location: home.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Game Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 text-white flex items-center justify-center min-h-screen relative overflow-hidden">

    <div class="absolute inset-0">
        <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-indigo-600/30 rounded-full blur-[100px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-purple-600/20 rounded-full blur-[100px]"></div>
    </div>

    <div class="w-full max-w-md relative z-10 px-4">
        
        <form action="validar.php" method="POST" class="bg-slate-900/80 backdrop-blur-xl border border-slate-800 shadow-2xl rounded-2xl px-8 pt-10 pb-10">
            
            <div class="text-center mb-10">
                <h1 class="text-3xl font-black tracking-tighter mb-2">GAME<span class="text-indigo-500">STORE</span></h1>
                <h2 class="text-slate-400 text-sm font-medium uppercase tracking-widest">Acesse sua conta</h2>
            </div>

            <?php
            if (isset($_GET['erro']) && $_GET['erro'] == 1) {
                echo '<div class="bg-red-500/10 border border-red-500/50 text-red-400 text-sm font-bold p-3 rounded-lg text-center mb-6">Email ou senha inválidos!</div>';
            }

            if (isset($_GET['sucesso']) && $_GET['sucesso'] == 'cadastro_ok') {
                echo '<div class="bg-green-500/10 border border-green-500/50 text-green-400 text-sm font-bold p-3 rounded-lg text-center mb-6">Cadastro realizado! Faça login.</div>';
            }
            ?>

            <div class="mb-5">
                <label class="block text-slate-300 text-xs font-bold mb-2 uppercase tracking-wider" for="email">Email</label>
                <input class="w-full bg-slate-950 border border-slate-700 text-white text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-3 outline-none transition-colors" 
                       id="email" name="email" type="email" placeholder="seu@email.com" required>
            </div>

            <div class="mb-8">
                <label class="block text-slate-300 text-xs font-bold mb-2 uppercase tracking-wider" for="senha">Senha</label>
                <input class="w-full bg-slate-950 border border-slate-700 text-white text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-3 outline-none transition-colors" 
                       id="senha" name="senha" type="password" placeholder="••••••••" required>
            </div>

            <div class="flex flex-col gap-4">
                <button class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3 px-4 rounded-lg shadow-lg shadow-indigo-500/20 transition-all transform hover:scale-[1.02]" type="submit">
                    Entrar na Plataforma
                </button>
                
                <a class="text-center text-sm text-slate-400 hover:text-indigo-400 transition-colors" href="cadastro.php">
                    Não tem conta? <span class="font-bold text-white">Cadastre-se agora</span>
                </a>
            </div>
        </form>
        
        <p class="text-center text-slate-600 text-xs mt-8">
            &copy; 2025 Game Store. Secure Login.
        </p>
    </div>

</body>
</html>