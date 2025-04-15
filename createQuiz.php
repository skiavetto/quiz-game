<?php

session_start();
require_once "Conn.php";
require_once "Quiz.php";


// Verifica se existe um usuário logado. 
if (!isset($_SESSION['userId'])) {
    header("Location: login.php"); // Redireciona para a página de login
    exit;
}

$author = $_SESSION['userId'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar novo QUIZ</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Parkinsans:wght@300..800&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Parkinsans:wght@300..800&family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Parkinsans:wght@300..800&family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cabin:ital,wght@0,400..700;1,400..700&family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Parkinsans:wght@300..800&family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">


    <link rel="stylesheet" href="css/configStyle.css">
    <link rel="stylesheet" href="css/createQuiz.css">

</head>
<body>

<?php

    $form_data = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    if(!empty($form_data["submit"])){
        if (!empty($form_data["title"])) {

            $_SESSION['quizTitle'] = $form_data['title'];

            header("Location:addQuestion.php");
            exit;
        }
    }
?>

    <div class="container">
        <div class="header">
            <h1>Criar Quiz</h1>    
        </div>

        <form action="" method="POST">
            <label>Escolha o título do Quiz</label>
            <input type="text" name="title">

            <input type="submit" name="submit" class="createQuiz-btn" value="Avançar">
        </form>
    </div>

</body>
</html>