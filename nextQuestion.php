<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once "Conn.php";
require_once "Quiz.php";

// Verifica se há um usuário logado
if (!isset($_SESSION['userId'])) {
    echo json_encode(['error' => 'Usuário não autenticado']);
    exit;
}

header('Content-Type: application/json'); // Define o retorno como JSON

// Verifica se os dados foram enviados via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Ação de adicionar pergunta
    if ($_POST['action'] === 'add_question') {
        
        $question = $_POST['question'] ?? '';
        $options = json_decode($_POST['options'] ?? '[]', true);
        $correct_option = $_POST['correct_option'] ?? '';

        if (empty($question) || count($options) < 4 || empty($correct_option)) {
            echo json_encode(['error' => 'Preencha todos os campos corretamente']);
            exit;
        }

        // Adicionar a pergunta na sessão
        if (!isset($_SESSION['quiz_questions'])) {
            $_SESSION['quiz_questions'] = [];
        }

        $_SESSION['quiz_questions'][] = [
            'question' => $question,
            'options' => $options,
            'correct_option' => $correct_option
        ];

        echo json_encode(['success' => true, 'message' => 'Pergunta adicionada com sucesso']);
        exit;
    }

    // Ação de avançar para a próxima pergunta
    if ($_POST['action'] === 'next') {
        $_SESSION['nQuestion'] = ($_SESSION['nQuestion'] ?? 1) + 1;
        echo json_encode(['success' => true, 'message' => 'Próxima pergunta carregada', 'questionNumber' => $_SESSION['nQuestion']]);
        exit;
    }
}

echo json_encode(['error' => 'Ação inválida']);
exit;

?>
