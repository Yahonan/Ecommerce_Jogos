<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['jogo_id'])) {
    
    $jogo_id = filter_var($_POST['jogo_id'], FILTER_VALIDATE_INT);
    $acao = $_POST['acao'] ?? 'adicionar'; 

    if ($jogo_id) {
        
        if ($acao === 'adicionar' || $acao === 'comprar_agora' || $acao === 'aumentar') {
            $_SESSION['carrinho'][] = $jogo_id;

        } 
        elseif ($acao === 'reduzir') {
            
            $key = array_search($jogo_id, $_SESSION['carrinho']);
            
            if ($key !== false) {
                unset($_SESSION['carrinho'][$key]);
                $_SESSION['carrinho'] = array_values($_SESSION['carrinho']);
            }
        }

        if (empty($_SESSION['carrinho'])) {
            header("Location: home.php?alerta=carrinho_vazio");
            exit();
        }

        if ($acao === 'comprar_agora') {
            header("Location: carrinho_view.php");
        } elseif ($acao === 'adicionar') {
            header("Location: home.php?sucesso=adicionado");
        } else {
            header("Location: carrinho_view.php");
        }
        exit();
    }
} 

header("Location: carrinho_view.php");
exit();
?>