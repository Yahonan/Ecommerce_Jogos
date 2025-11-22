<?php
session_start();
require_once 'conexao.php'; 

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: home.php");
    exit();
}

$jogo_id = $_GET['id'];
$jogo = null; 

$sql = "SELECT id, titulo, preco, plataforma, genero, imagem_capa, descricao FROM jogos WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $jogo_id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $jogo = $resultado->fetch_assoc();
    }
    $stmt->close();
}

$conn->close();

if (!$jogo) {
    header("Location: home.php?erro=jogo_nao_encontrado");
    exit();
}

$titulo = htmlspecialchars($jogo['titulo']);
$preco = number_format($jogo['preco'], 2, ',', '.');
$plataforma = htmlspecialchars($jogo['plataforma']);
$genero = htmlspecialchars($jogo['genero']);
$imagem_url = isset($jogo['imagem_capa']) && !empty($jogo['imagem_capa']) ? 'img/' . htmlspecialchars($jogo['imagem_capa']) : 'img/default.jpg';
$descricao_jogo = htmlspecialchars($jogo['descricao']);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes - <?php echo $titulo; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white">
    
    <nav class="bg-gray-800 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="home.php" class="text-2xl font-bold text-indigo-400">GAME STORE</a>
                </div>
        </div>
    </nav>
    
    <main class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        
        <div class="bg-gray-800 shadow-xl rounded-lg overflow-hidden md:flex">
            
            <div class="md:w-2/5 flex-shrink-0">
                <img class="w-full h-full object-cover" src="<?php echo $imagem_url; ?>" alt="Capa do Jogo <?php echo $titulo; ?>">
            </div>
            
            <div class="md:w-3/5 p-8 flex flex-col justify-between">
                
                <div>
                    <h1 class="text-4xl font-extrabold text-indigo-400 mb-4"><?php echo $titulo; ?></h1>
                    
                    <p class="text-gray-400 text-lg mb-6">
                        <span class="font-semibold text-white">Plataforma:</span> <?php echo $plataforma; ?>
                    </p>
                    <p class="text-gray-400 text-lg mb-6">
                        <span class="font-semibold text-white">Gênero:</span> <?php echo $genero; ?>
                    </p>
                    
                    <h2 class="text-2xl font-bold text-white mt-8 mb-4">Descrição</h2>
                    <p class="text-gray-300 leading-relaxed">
                        <?php echo $descricao_jogo; ?>
                    </p>
                </div>
                
                <div class="mt-8 pt-6 border-t border-gray-700">
                    <p class="text-4xl font-bold text-green-400 mb-6">
                        R$ <?php echo $preco; ?>
                    </p>
                    
                    <button class="w-full md:w-auto bg-green-600 hover:bg-green-700 text-white font-extrabold py-3 px-8 rounded-lg text-lg transition duration-150">
                        Comprar Agora
                    </button>
                    
                    <a href="home.php" class="inline-block mt-4 text-indigo-400 hover:text-indigo-300 transition duration-150">
                         &larr;  Voltar à Lista de Jogos
                    </a>
                </div>
            </div>
            
        </div>
        
    </main>

</body>
</html>