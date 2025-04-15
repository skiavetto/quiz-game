<?php
session_start();

require_once "Conn.php";
require_once "User.php";

$id = $_SESSION['userId'];
$formData = filter_input_array(INPUT_POST, FILTER_DEFAULT);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Parkinsans:wght@300..800&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Parkinsans:wght@300..800&family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Parkinsans:wght@300..800&family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cabin:ital,wght@0,400..700;1,400..700&family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Parkinsans:wght@300..800&family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">


    <link rel="stylesheet" href="css/configStyle.css">
    <link rel="stylesheet" href="css/home.css">

</head>
<body>

    <?php 
        $user = new User();
        $user -> id = $id;
        $userProfile = $user -> viewProfile();
        extract($userProfile);

        $countUserQuiz = $user -> countQuiz();
    ?>

    <!-- Navigator superior -->
    <nav>
        <div class="userContainer">
            <img src=<?php echo "profile_img/" . $id . "/" . $id . "_profilePhoto.jpg"; ?> > 
            <h1><?php echo "@" . $username; ?></h1>
        </div>

        <span class="userScore">Score: <?php echo $score; ?></span>

        <a href="createQuiz.php" class="createQuiz-btn">Criar Quiz</a>
    </nav>

    <!-- Section (main) -->
    <div class="container">

        <h1 class="title">Pesquisar Quiz</h1>
            <div class="searchQuiz">
                <form action="" method="POST" name="search_form">
                    <div class="searchQuizInput">
                        <input type="text" name="search_input" placeholder="Pesquise pelo código ou nome do quiz">
                        <input type="submit" name="send_search" class="search-btn">
                    </div>
                </form>

        <a href="createQuiz.php" class="createQuiz-btn-mobile">Criar Quiz</a>

                <?php
                    if (isset($_POST['send_search'])) {
                        // Recebe os dados do formulário
                        $formData = $_POST;
                        
                        // Passa os dados do formulário para o objeto $user
                        $user->formData = $formData;
                        
                        // Chama o método searchQuiz
                        $search = $user->searchQuiz();
                        
                        if ($search) {
                            // Cria a estrutura da tabela
                            echo "<table>";
                            echo "<thead>";
                            echo "<th class='th_id'>#Id</th>";
                            echo "<th class='th_title'>Quiz</th>";
                            echo "<th class='th_author'>Autor</th>";
                            echo "<th class='th_players'>Jogadores</th>";
                            echo "<th class='th_play-btn'>Jogar</th>";
                            echo "</thead>";
                            echo "<tbody>"; // Abre a tag tbody para os dados

                            foreach ($search as $row) {
                                extract($row);

                                // Cria as linhas da tabela
                                echo "<tr>";
                                echo "<td class='td_id'>$quiz_id</td>";
                                echo "<td class='td_title'>$quiz_title</td>";
                                echo "<td class='td_author'>$quiz_author</td>";
                                echo "<td class='td_players'>$quiz_players</td>";
                                echo "<td class='td_play-btn'><a href='playQuiz.php?quizId=$quiz_id' class='play-btn'>Jogar</a></td>";
                                echo "</tr>";
                            }
                            
                            echo "</tbody>"; // Fecha a tag tbody
                            echo "</table>";
                        } else {
                            echo "Nenhum quiz encontrado!";
                        }
                    }
                ?>

            </div>
        </div>

</body>
</html>