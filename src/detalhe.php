<?php
session_start();
require_once 'conexao.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: home.php?erro=id_invalido"); 
    exit();
}

$jogo_id = $_GET['id'];
$jogo = null;

$sql = "SELECT * FROM jogos WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) { 
    header("Location: home.php?erro=db_error_jogo"); 
    exit();
} 
$stmt->bind_param("i", $jogo_id);
$stmt->execute();
$resultado = $stmt->get_result();
if ($resultado->num_rows === 1) {
    $jogo = $resultado->fetch_assoc();
}
$stmt->close();



$avaliacoes_reais = [];
$nota_media = 0.0;
$total_avaliacoes = 0;

$sql_avaliacoes = "SELECT a.nota, a.comentario, a.data_avaliacao, u.nome
                   FROM avaliacoes a
                   JOIN usuarios u ON a.usuario_id = u.id
                   WHERE a.jogo_id = ?
                   ORDER BY a.data_avaliacao DESC";

$stmt_avaliacoes = $conn->prepare($sql_avaliacoes);
if ($stmt_avaliacoes) {
    $stmt_avaliacoes->bind_param("i", $jogo_id);
    $stmt_avaliacoes->execute();
    $resultado_avaliacoes = $stmt_avaliacoes->get_result();
    $avaliacoes_reais = $resultado_avaliacoes->fetch_all(MYSQLI_ASSOC);
    $total_avaliacoes = count($avaliacoes_reais);

    if ($total_avaliacoes > 0) {
        $soma_notas = array_sum(array_column($avaliacoes_reais, 'nota'));
        $nota_media = number_format($soma_notas / $total_avaliacoes, 1, '.', '');
    }
    $stmt_avaliacoes->close();
}


$fake_reviews_pool = [
    ['nome' => 'ShadowBlade', 'comentario' => 'Gráficos de cair o queixo e jogabilidade perfeita! Um verdadeiro GOTY.'],
    ['nome' => 'PixelMaster', 'comentario' => 'Absolutamente viciante! O enredo é profundo e as missões são incríveis.'],
    ['nome' => 'LadyVortex', 'comentario' => 'Compre sem medo. Horas e horas de conteúdo pelo preço. O melhor que joguei este ano.'],
    ['nome' => 'IronGeek', 'comentario' => 'Design de som e trilha sonora imersivos. A atenção aos detalhes é impressionante.'],
    ['nome' => 'AlphaZero', 'comentario' => 'Excelente otimização e zero bugs. Uma experiência fluida do começo ao fim.'],
];

$num_fake_reviews = 3;
$fake_reviews = [];

if (count($fake_reviews_pool) > 0) {
    $num_a_selecionar = min(count($fake_reviews_pool), $num_fake_reviews);
    
    $chaves_aleatorias = array_rand($fake_reviews_pool, $num_a_selecionar);

    if (!is_array($chaves_aleatorias)) {
        $chaves_aleatorias = [$chaves_aleatorias];
    }
    
    foreach ($chaves_aleatorias as $chave) {
        $fake_reviews[] = $fake_reviews_pool[$chave];
    }
}


$reviews_a_exibir = array_merge($avaliacoes_reais, $fake_reviews);
shuffle($reviews_a_exibir); 


$esta_na_wishlist = false;
if (isset($_SESSION['usuario_logado'])) {
    $usuario_id = $_SESSION['usuario_id'];
    $sql_check_wishlist = "SELECT id FROM wishlist WHERE jogo_id = ? AND usuario_id = ?";
    $stmt_check = $conn->prepare($sql_check_wishlist);
    if ($stmt_check) {
        $stmt_check->bind_param("ii", $jogo_id, $usuario_id);
        $stmt_check->execute();
        $resultado_check = $stmt_check->get_result();
        $esta_na_wishlist = ($resultado_check->num_rows > 0);
        $stmt_check->close();
    }
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
$imagem_url = !empty($jogo['imagem_capa']) ? 'img/' . htmlspecialchars($jogo['imagem_capa']) : 'img/default.jpg';
$descricao_jogo = nl2br(htmlspecialchars($jogo['descricao']));
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?> - Detalhes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    </head>
<body class="bg-slate-950 text-white min-h-screen flex flex-col overflow-x-hidden">

    <nav class="absolute top-0 w-full z-30 bg-transparent p-6">
        <div class="max-w-7xl mx-auto">
            <a href="home.php" class="inline-flex items-center text-white/80 hover:text-white transition font-medium gap-2 bg-black/20 hover:bg-black/40 px-5 py-2.5 rounded-full backdrop-blur-md border border-white/10">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Voltar para Loja
            </a>
        </div>
    </nav>

    <div class="fixed inset-0 z-0">
        <div class="absolute inset-0 bg-slate-950/60 z-10"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/80 to-transparent z-10"></div>
        <img src="<?php echo $imagem_url; ?>" class="w-full h-full object-cover filter blur-2xl opacity-60 scale-110" alt="Background">
    </div>

    <main class="relative z-10 flex-grow flex items-center justify-center py-24 px-4 sm:px-6 lg:px-8 flex-col">

        <div class="max-w-6xl w-full bg-slate-900/70 backdrop-blur-xl rounded-3xl shadow-2xl shadow-black/50 border border-white/10 overflow-hidden flex flex-col md:flex-row">

            <div class="md:w-5/12 relative group">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent to-transparent opacity-60 md:hidden"></div>
                <img class="w-full h-full object-cover min-h-[400px] md:min-h-[600px] transition-transform duration-700"
                    src="<?php echo $imagem_url; ?>"
                    alt="<?php echo $titulo; ?>">
            </div>

            <div class="md:w-7/12 p-8 md:p-12 flex flex-col justify-center relative">
                <div class="absolute top-0 right-0 p-6 opacity-10">
                    <svg class="w-64 h-64 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M21 6H3c-1.1 0-2 .9-2 2v8c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-10 7H8v3H6v-3H3v-2h3V8h2v3h3v2zm4.5 2c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm4 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/></svg>
                </div>

                <div class="relative z-10">
                    <div class="flex flex-wrap gap-3 mb-6">
                        <span class="px-4 py-1.5 rounded-lg text-xs font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 uppercase tracking-widest">
                            <?php echo $genero; ?>
                        </span>
                        <span class="px-4 py-1.5 rounded-lg text-xs font-bold bg-slate-700/50 text-slate-300 border border-white/10 uppercase tracking-widest flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-green-500"></span>
                            <?php echo $plataforma; ?>
                        </span>
                    </div>

                    <h1 class="text-4xl md:text-6xl font-black text-white mb-8 leading-none tracking-tight drop-shadow-lg">
                        <?php echo $titulo; ?>
                    </h1>
                    
                    <div class="prose prose-lg prose-invert mb-10 text-slate-300 leading-relaxed max-w-2xl">
                        <p><?php echo $descricao_jogo; ?></p>
                    </div>

                    <div class="mt-auto pt-8 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-6">

                        <div class="flex flex-col">
                            <span class="text-sm text-slate-400 uppercase font-bold tracking-wider">Preço</span>
                            <span class="text-5xl font-black text-white tracking-tight flex items-start gap-1">
                                <span class="text-2xl mt-2 text-indigo-400">R$</span><?php echo $preco; ?>
                            </span>
                        </div>

                        <div class="flex flex-row items-center gap-4 w-full sm:w-auto flex-1">
                            
                            <?php if (isset($_SESSION['usuario_logado'])): 
                                $icone = $esta_na_wishlist ? '❤️' : '🤍'; 
                                $title_text = $esta_na_wishlist ? 'Remover da Lista de Desejos' : 'Adicionar à Lista de Desejos';
                                ?>
                                <form action="processar_wishlist.php" method="POST">
                                    <input type="hidden" name="jogo_id" value="<?php echo $jogo_id; ?>">
                                    <input type="hidden" name="acao" value="<?php echo $esta_na_wishlist ? 'remover' : 'adicionar'; ?>">
                                    <input type="hidden" name="redirect" value="detalhe.php?id=<?php echo $jogo_id; ?>">
                                    <button type="submit" title="<?php echo $title_text; ?>" 
                                            class="text-3xl text-red-500 hover:scale-110 transition bg-slate-700/50 hover:bg-slate-600/50 p-3 rounded-xl shadow-lg shadow-black/20">
                                        <?php echo $icone; ?>
                                    </button>
                                </form>
                            <?php endif; ?>

                            <form action="carrinho.php" method="POST" class="w-full flex-1">
                                <input type="hidden" name="jogo_id" value="<?php echo $jogo_id; ?>">
                                <button type="submit" name="acao" value="adicionar" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-4 px-8 rounded-xl shadow-lg shadow-indigo-500/30 transition-all transform hover:scale-105 active:scale-95 flex items-center justify-center gap-3 group">
                                    <span>Adicionar ao Carrinho</span>
                                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                    </div>
            </div>
        </div>
        <div class="max-w-6xl w-full relative z-10 mt-16 px-4 sm:px-6 lg:px-8">
            <div class="bg-slate-900/70 backdrop-blur-xl shadow-2xl shadow-black/50 rounded-3xl border border-white/10 p-8">
                <h2 class="text-3xl font-bold text-indigo-400 mb-6">Avaliações dos Usuários</h2>

                <div class="flex items-center space-x-4 mb-8 pb-4 border-b border-white/10">
                    <span class="text-6xl font-extrabold text-green-400">
                        <?php echo $nota_media > 0 ? $nota_media : '5.0'; ?>
                    </span>
                    <div>
                        <p class="text-2xl font-bold text-white flex gap-1">
                            <?php
                            $estrelas_a_mostrar = $nota_media > 0 ? floor($nota_media) : 5;
                            $estrelas_cheias = floor($estrelas_a_mostrar);
                            
                            for ($i = 0; $i < 5; $i++) {
                                echo $i < $estrelas_cheias ? '<span class="text-amber-400">★</span>' : '<span class="text-slate-600">★</span>';
                            }
                            ?>
                        </p>
                        </div>
                </div>

                <?php if (isset($_SESSION['usuario_logado'])): ?>
                    <h3 class="text-xl font-semibold mb-4 text-white">Deixe sua Avaliação</h3>
                    <form action="processar_avaliacao.php" method="POST" class="space-y-4 mb-10">
                        <input type="hidden" name="jogo_id" value="<?php echo $jogo['id']; ?>">
                        
                        <input type="hidden" id="avaliacao-estrelas" name="nota" value="0"> 

                        <div class="mb-6">
                            <label class="block text-gray-400 text-sm font-bold mb-2">Sua Avaliação (Estrelas)</label>
                            
                            <div id="star-rating-container" class="flex space-x-1 cursor-pointer">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <button type="button" data-rating="<?php echo $i; ?>" 
                                            class="text-gray-500 transition focus:outline-none">
                                        <svg class="w-8 h-8 transition-colors fill-current" viewBox="0 0 24 24">
                                            <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                        </svg>
                                    </button>
                                <?php endfor; ?>
                            </div>
                            <p id="rating-error" class="text-red-500 text-sm mt-2 hidden">Por favor, clique em uma estrela para selecionar uma nota.</p>
                        </div>
                                                
                        <div>
                            <label for="comentario" class="block text-gray-400 text-sm font-bold mb-2">Comentário</label>
                            <textarea id="comentario" name="comentario" rows="3" required
                                class="w-full bg-slate-800 text-white p-3 rounded-lg border border-slate-700 focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="O que você achou do jogo?"></textarea>
                        </div>
                        
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3 px-6 rounded-lg shadow-md shadow-indigo-500/20 transition-all transform hover:scale-[1.01] active:scale-[0.99] w-full">
                            Enviar Avaliação
                        </button>
                    </form>
                <?php else: ?>
                    <p class="text-gray-400 mb-8 p-4 bg-slate-800/50 border border-slate-700 rounded-lg">
                        <a href="login.php" class="text-indigo-400 font-bold hover:text-indigo-300 transition">Faça login</a> para deixar sua avaliação.
                    </p>
                <?php endif; ?>

                <h3 class="text-2xl font-semibold mb-6 text-white pt-4 border-t border-white/10">Reviews em Destaque</h3>
                
                <?php if (count($reviews_a_exibir) > 0): ?>
                    <div class="space-y-6">
                        <?php foreach ($reviews_a_exibir as $review):
                            $is_real = isset($review['nota']) && isset($review['data_avaliacao']);
                            $nota = $is_real ? $review['nota'] : 5;
                            $nome = $review['nome'];
                            $comentario = $review['comentario'];
                            $data_exibicao = $is_real ? date('d/m/Y', strtotime($review['data_avaliacao'])) : 'Recente';
                        ?>
                            <div class="bg-slate-900/50 p-5 rounded-xl border border-white/10 shadow-lg <?php echo !$is_real ? 'border-indigo-500/50 shadow-indigo-500/10' : ''; ?>">
                                <div class="flex items-start justify-between mb-3">
                                    <p class="font-bold text-lg text-white flex items-center gap-2">
                                        <span class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center text-sm font-bold uppercase"><?php echo substr($nome, 0, 1); ?></span>
                                        <?php echo htmlspecialchars($nome); ?>
                                        <?php echo !$is_real ? '<span class="text-indigo-400 text-xs font-semibold px-2 py-0.5 rounded-full bg-indigo-900/50 border border-indigo-700/50">Destaque</span>' : ''; ?>
                                    </p>
                                    <div class="text-xl text-yellow-400 flex gap-0.5">
                                        <?php
                                        for ($i = 0; $i < 5; $i++) {
                                            echo $i < $nota ? '★' : '<span class="text-slate-600">★</span>';
                                        }
                                        ?>
                                    </div>
                                </div>
                                <p class="text-slate-300 italic mb-3 ml-10 border-l-2 border-slate-700 pl-3 leading-relaxed">
                                    "<?php echo nl2br(htmlspecialchars($comentario)); ?>"
                                </p>
                                <p class="text-xs text-gray-500 text-right">
                                    <?php echo $data_exibicao; ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if ($total_avaliacoes > count($reviews_a_exibir)): ?>
                        <div class="text-center mt-8 pt-4 border-t border-white/10">
                            <p class="text-indigo-400 font-medium hover:text-indigo-300 transition cursor-pointer">
                                Ver todas as <?php echo $total_avaliacoes; ?> avaliações completas...
                            </p>
                        </div>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <p class="text-gray-400">Nenhuma avaliação encontrada ainda. Seja o primeiro a avaliar!</p>
                <?php endif; ?>

            </div>
        </div>
        </main>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const starButtons = document.querySelectorAll('#star-rating-container button');
        const ratingInput = document.getElementById('avaliacao-estrelas');
        const ratingError = document.getElementById('rating-error');
        const ratingContainer = document.getElementById('star-rating-container');
        const reviewForm = document.querySelector('form[action="processar_avaliacao.php"]');

        function updateStars(rating) {
            starButtons.forEach(button => {
                const buttonRating = parseInt(button.getAttribute('data-rating'));
                
                if (buttonRating <= rating) {
                    button.classList.remove('text-gray-500');
                    button.classList.add('text-yellow-400'); 
                } else {
                    button.classList.remove('text-yellow-400');
                    button.classList.add('text-gray-500');
                }
            });
        }

        starButtons.forEach(button => {
            button.addEventListener('click', function() {
                const selectedRating = parseInt(this.getAttribute('data-rating'));
                ratingInput.value = selectedRating;
                updateStars(selectedRating);
                ratingError.classList.add('hidden'); 
            });
        });

        ratingContainer.addEventListener('mouseover', function(e) {
            let hoverRating = 0;
            let target = e.target;
            
            while (target && target !== ratingContainer) {
                if (target.tagName === 'BUTTON' && target.hasAttribute('data-rating')) {
                    hoverRating = parseInt(target.getAttribute('data-rating'));
                    break;
                }
                if (target.tagName === 'SVG' || target.tagName === 'PATH') {
                    target = target.closest('button');
                    if (target && target.hasAttribute('data-rating')) {
                        hoverRating = parseInt(target.getAttribute('data-rating'));
                        break;
                    }
                }
                target = target.parentNode;
            }

            if (hoverRating > 0) {
                updateStars(hoverRating);
            }
        });

        ratingContainer.addEventListener('mouseout', function() {
            const currentRating = parseInt(ratingInput.value);
            updateStars(currentRating);
        });

        if (reviewForm) {
            reviewForm.addEventListener('submit', function(e) {
                if (parseInt(ratingInput.value) === 0) {
                    e.preventDefault();
                    ratingError.classList.remove('hidden');
                }
            });
        }

        updateStars(parseInt(ratingInput.value));
    });
    </script>

</body>
</html>
