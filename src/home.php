<?php

session_start();

require_once 'conexao.php';

$sql = "SELECT * FROM jogos WHERE disponivel = TRUE ORDER BY titulo ASC";
$resultado = $conn->query($sql);

$jogos = [];
if ($resultado) {
    $jogos = $resultado -> fetch_all(MYSQLI_ASSOC);

} else {
    echo "Erro ao buscar jogos: " . $conn->error;

};

$nome_usuario = null;

if (isset($_SESSION['usuario_logado']) && $_SESSION['usuario_logado'] === true){
    $nome_usuario = $_SESSION['usuario_nome'];

} 

$conn->close();

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - E-commerce de Jogos</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white">

    <nav class="bg-gray-800 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                
                <div class="flex-shrink-0">
                    <a href="home.php" class="text-2xl font-bold text-indigo-400">GAME STORE</a>
                </div>
                
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a href="home.php" class="bg-gray-900 text-white px-3 py-2 rounded-md text-sm font-medium">Início</a>
                        </div>
                </div>

                <div class="ml-auto">
                    <?php if ($nome_usuario):  ?>
                        
                        <div class="flex items-center">
                            <span class="text-gray-300 text-sm mr-4">
                                Olá, <?php echo htmlspecialchars($nome_usuario); ?>!
                            </span>
                            <a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-md text-sm font-medium">Sair</a>
                        </div>

                    <?php else:  ?>
                        
                        <div class="flex items-center space-x-2">
                            <a href="login.php" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-2 rounded-md text-sm font-medium">Login</a>
                            <a href="cadastro.php" class="text-gray-300 hover:bg-gray-700 px-3 py-2 rounded-md text-sm font-medium">Cadastre-se</a>
                        </div>

                    <?php endif; ?>
                </div>

            </div>
        </div>
    </nav>
    
    <main class="max-w-7xl mx-auto py-12 sm:px-6 lg:px-8">
        
        <h1 class="text-3xl font-bold text-indigo-400 mb-8 px-4 sm:px-0">Nossos Jogos</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 px-4 sm:px-0">

            <?php if (empty($jogos)): ?>
                
                <p class="text-gray-400 col-span-full text-center">Nenhum jogo cadastrado no momento. Volte em breve!</p>

            <?php else: ?>
                
                <?php foreach ($jogos as $jogo): ?>
                    
                    <div class="bg-gray-800 rounded-lg shadow-md overflow-hidden transform hover:scale-105 transition-transform duration-300 ease-in-out">
                        
                        <img class="w-full h-56 object-cover" 
                             src="img/<?php echo htmlspecialchars($jogo['imagem_capa']); ?>" 
                             alt="Capa do jogo <?php echo htmlspecialchars($jogo['titulo']); ?>">
                        
                        <div class="p-4">
                            <h3 class="text-xl font-bold text-indigo-300 mb-2 truncate" title="<?php echo htmlspecialchars($jogo['titulo']); ?>">
                                <?php echo htmlspecialchars($jogo['titulo']); ?>
                            </h3>
                            
                            <p class="text-sm text-gray-400 mb-3">
                                <?php echo htmlspecialchars($jogo['plataforma']); ?> | <?php echo htmlspecialchars($jogo['genero']); ?>
                            </p>
                            
                            <p class="text-2xl font-semibold text-green-400 mb-4">
                                R$ <?php echo number_format($jogo['preco'], 2, ',', '.'); ?>
                            </p>

                            <a href="detalhe.php?id=<?php echo $jogo['id']; ?>" 
                                class="w-full text-center block bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                    Ver Detalhes
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </main>
    
    <footer class="bg-gray-800 mt-12">
        <div class="max-w-7xl mx-auto py-6 px-4 text-center text-gray-500">
            <p>&copy;2025 E-commerce de Jogos</p>
        </div>
    </footer>

</body>
</html> 