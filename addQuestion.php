<?php
session_start();

require_once "Conn.php";
require_once "Quiz.php";


// Verifica se há um usuário logado
if (!isset($_SESSION['userId'])) {
    header("Location: login.php");
    exit;
}
// Obtém o ID do usuário logado
$author = $_SESSION['userId'];

if(!isset($_SESSION['quizTitle'])){
    header("Location: createQuiz.php");
    exit;
}
// armazena o título do quiz definido na página anterior.
$quizTitle = $_SESSION['quizTitle'];

?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar quiz</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Parkinsans:wght@300..800&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Parkinsans:wght@300..800&family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Parkinsans:wght@300..800&family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cabin:ital,wght@0,400..700;1,400..700&family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Parkinsans:wght@300..800&family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">


    <link rel="stylesheet" href="css/configStyle.css">
    <link rel="stylesheet" href="css/addQuestion.css">

    <script src="beforeUnload.js"></script>
    <script defer src="requestNewQuestion.js"></script>

</head>
<body>
    

    <div class="container">

        <h1 class="quizTitle"><?php echo $quizTitle; ?></h1>

        <div class="header">
            <h1 class="title">Adicionar perguntas</h1>
            <h1 class="titleQuestion"></h1>
        </div>


        <form action="" method="POST" id="newQuestionForm" enctype="multipart/form-data">
            
            <div class="headerQuizContainer">
                <label>Digite a pergunta</label>
                <input type="text" name="question" placeholder="Ex: Qual a capital do brazil ?" id="question">

                <label for="imgFile" class="imgQuestion-submit">Selecionar imagem</label>
                <input type="file" id="imgFile">
                <span class="label-description">*É possível utilizar imagens para complementar as suas perguntas. Exemplo: "Quem é o personagem da imagem" ? </span>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <td class="td_alternative">Alternativas</td>
                        <td class="td_value">Correta</td>
                    </tr> 
                </thead>

                <div class="description_table">
                    <span class="label-description">*Digite abaixo quais alternativas poderão ser escolhidas para a pergunta e marque ao lado qual é a correta.</span>
                </div>


                <tr>
                    <td class="td_alternative"><label>A )</label> <input type="text" name="altA" class="alternativeInput" placeholder="Ex: Brazília" id="altA"></td>
                    <td class="td_value"><input type="radio" name="rightAlt" value="a" class="rightAlt"></td>
                </tr>

                <tr>
                    <td class="td_alternative"><label>B )</label> <input type="text" name="altB" class="alternativeInput" placeholder="Ex: São Paulo" id="altB"></td>
                    <td class="td_value"><input type="radio" name="rightAlt" value="b" class="rightAlt"> </td>
                </tr>

                <tr>
                    <td class="td_alternative"><label>C )</label> <input type="text" name="altC" class="alternativeInput" placeholder="Ex: Rio de Janeiro" id="altC"></td>
                    <td class="td_value"><input type="radio" name="rightAlt" value="c" class="rightAlt"></td>
                </tr>

                <tr>
                    <td class="td_alternative"><label>D )</label> <input type="text" name="altD" class="alternativeInput" Placeholder="Ex: Minas Gerais" id="altD"></td>
                    <td class="td_value"><input type="radio" name="rightAlt" value="d" class="rightAlt"></td>
                </tr>
            </table>

            <div class="btnContainer">
                <!-- Armazena os dados recebidos em um objeto js e limpa o formulário para o usuário incluir a proxima pergunta -->
                <button type="button" id="nextQuestion" class="nextBtn">Próxima</button>

                <!-- Finalizar quiz e enviar dados via fetchAPI para saveQuiz.php  -->
                <button type="button" id="finishQuiz" name="action" class="finishBtn">Finalizar Quiz</button>
            </div>

        </form>

    </div>

</body>
</html>