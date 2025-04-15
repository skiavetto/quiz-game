<?php

session_start();
require_once "Quiz.php"; 
require_once "Conn.php";

// Verifica Sessions
if(!isset($_SESSION['quizTitle'])){
    die("SESSION QUIZTITLE NÃO DEFINIDA");
}
if (!isset($_SESSION['userId'])) {
    die("SESSION USERID NÃO DEFINIDA");
}

// Recebe os dados via POST
$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
$arquivos = $_FILES["imgFile"]; // Array de arquivos enviados

$quiz = new Quiz();
$createQuiz = $quiz->createNewQuiz($_SESSION['quizTitle'], $_SESSION['userId']);

// Se o quiz foi criado, processa as perguntas
if ($createQuiz) {
    if ($dados) {
        $perguntas = [];
        
        // Processa os dados das perguntas
        foreach ($dados as $key => $value) {
            if (preg_match('/(\d+)$/', $key, $matches)) {
                $index = $matches[1];
                if (!isset($perguntas[$index])) {
                    $perguntas[$index] = [];
                }
                $campo = preg_replace('/\d+$/', '', $key);
                $perguntas[$index][$campo] = $value;
            }
        }

        // Diretório base para imagens do quiz
        $uploadDir = "img_quiz/" . $quiz->id . "/";

        // Cria o diretório se não existir
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Processa cada pergunta e sua respectiva imagem
        foreach ($perguntas as $index => $row) {
            $filePath = null; // Inicializa o caminho da imagem como nulo

            // Se existe um arquivo para essa pergunta, faz upload
            if (isset($arquivos['name'][$index]) && $arquivos['error'][$index] === 0) {
                
                $fileType = pathinfo($arquivos["name"][$index], PATHINFO_EXTENSION);
                $fileName = "q_" . $index + 1 . "." . $fileType;
                $filePath = $uploadDir . $fileName;

                // Valida o formato da imagem
                $allowedTypes = ["image/jpeg", "image/png", "image/gif"];
                if (!in_array($arquivos["type"][$index], $allowedTypes)) {
                    die(json_encode(["success" => false, "message" => "Formato de imagem inválido para a pergunta $index"]));
                }

                // Move o arquivo para o diretório
                if (!move_uploaded_file($arquivos["tmp_name"][$index], $filePath)) {
                    die(json_encode(["success" => false, "message" => "Erro ao mover a imagem da pergunta $index"]));
                }
            }

            // Adiciona a pergunta ao quiz, incluindo a imagem se existir
            $row['imagem'] = $filePath; // Adiciona o caminho da imagem à pergunta
            $quiz->formData = $row;
            $quiz->img = $fileName;
            $savedQuiz = $quiz->addNewQuestion();
        }

        if ($savedQuiz) {
            unset($_SESSION['quizTitle']);
            unset($_SESSION['quizId']);
            header("Location:success.php?quizId=$quiz->id");
            exit();
        }
    }
}

?>