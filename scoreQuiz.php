<?php
session_start();
require_once "User.php";
$quizId = filter_input(INPUT_GET, 'quizId', FILTER_SANITIZE_NUMBER_INT);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Parkinsans:wght@300..800&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Parkinsans:wght@300..800&family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Parkinsans:wght@300..800&family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cabin:ital,wght@0,400..700;1,400..700&family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Parkinsans:wght@300..800&family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">


    <link rel="stylesheet" href="css/configStyle.css">
    <link rel="stylesheet" href="css/scoreQuiz.css">
    <title>Quizz</title>
</head>
<body>
    
<div class="container">

    <h1 class="concradulations">Parabéns, você finalizou o quiz !</h1>


    <div class="score_container">
    <?php
        $user = new User();
        $user -> id = $_SESSION['userId'];
        $user -> finishMatch($_SESSION['score'] , $quizId);


        $qtd = $_SESSION['user_successes'] + $_SESSION['user_errors'];
        echo "<p>Quantidade de acertos: <span class='user_success'>" . $_SESSION['user_successes'] . "/" . $qtd . "</span></p>";
        echo "<p class='score_txt'>Score da partida/ pontos acumulados: " . $user -> feedback . "</p><br>";

        

        $_SESSION['user_successes'] = 0;
        $_SESSION['user_errors'] = 0;
        $_SESSION['score'] = 0;
        $_SESSION['nq'] = 0;    
    ?>
    </div>

    <div class="btnContainer">
        <a href="home.php" class="home-btn">Voltar para página inicial</a><br>
        <a href="createQuiz.php" class="createQuiz-btn">Criar meu próprio quiz</a><br>
        <a href="#" class="share-btn">Compartilhar quiz</a><br>
    </div>


</div>


</body>
</html>