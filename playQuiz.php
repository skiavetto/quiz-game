<?php
session_start();

require_once "Conn.php";
require_once "User.php";
require_once "Quiz.php";

// captura o id do quiz digitado na url
$quizId = filter_input(INPUT_GET, 'quizId', FILTER_SANITIZE_NUMBER_INT);

// Verifica se o usuário está logado para jogar, caso contrário é redirecionado a página de login.
if(!isset($_SESSION['userId'])){
    // armazena um quiz na "sala de espera" para que quando o login for efetuado, o usuário seja redirecionado novamente para o quiz
    $_SESSION['quizOnHold'] = $quizId; 
    // feedback para o usuário
    $_SESSION['feedback'] = "É necessário estar logado para poder jogar. Efetue o login com o seu usuário e senha";
    header("Location:login.php");
}

// Instancia a classe quiz e chama o método playQuiz que fará o SELECT do quiz e das perguntas no banco
$quiz = new Quiz();
$quiz -> id = $quizId;
$play = $quiz -> playQuiz();

// Armazena a quantidade de acertos do usuário
if(!isset($_SESSION['user_successes'])){
    $_SESSION['user_successes'] = 0; 
}

// Armazena a quantidade de erros do usuário
if(!isset($_SESSION['user_errors'])){
$_SESSION['user_errors'] = 0; 
}

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
    <link rel="stylesheet" href="css/playQuiz.css">

    <title>Quiz - #<?php echo $quizId; ?></title>
</head>
<body>

<?php
$questions = []; // Armazena as perguntas
$img = []; // Armazena as imagens 
$alternatives = []; // Armazena as 4 alternativas de cada pergunta

// Organiza os dados recebidos pelo banco em vetores de pergunta e alternativas para as perguntas
foreach($play as $row){
    extract($row);
    $array = []; // Armazena 4 alternativas (de a até d) em cada espaço de vetor identificados pela chave (A até D)

    array_push($questions, $question); // Armazena as perguntas da tabela no array $questions
    array_push($img, $quizImg); // Armazena as imagens da tabela no array $img 

    // Para cada pergunta ($questions) haverá 4 alternativas de a até d em $alternatives
    $array['A'] = $A;
    $array['B'] = $B;
    $array['C'] = $C;
    $array['D'] = $D;
    $array['rightAlt'] = $rightAlt;
    array_push($alternatives, $array);
}



// Number question - O numero da pergunta que será resolvida pelo usuário
if(!isset($_SESSION['nq'])) {   
    $_SESSION['nq'] = 0;    
    $_SESSION['user_answer'] = []; // Armazena as respostas do usuário
}

// Armazena a alternativa que foi escolhida pelo usuário.
$btnAction = filter_input_array(INPUT_POST, FILTER_DEFAULT);



// Verifica se botão "próximo" foi clicado
if (isset($btnAction['next'])) {
    // Verifica se a resposta foi selecionada
    if (isset($btnAction['radio_input'])) {
        $_SESSION['user_answer'][$_SESSION['nq']] = $btnAction['radio_input'];

        // Verifica se a resposta do usuário está correta e armazena resultado na respectiva session.
        if($btnAction['radio_input'] == $alternatives[$_SESSION['nq']]['rightAlt']){
            echo "<script> alert('Acertou!')</script>";
            $_SESSION['user_successes']++;
        }
        // Resposta errada 
        else {
            echo "<script> alert('Errou!')</script>";
            $_SESSION['user_errors']++;
        }

    // Usuário não selecionou uma resposta 
    } else {
        echo "Nenhuma opção foi selecionada.";
    }

    // Verifica se ainda existem perguntas no quiz e incrementa o contador responsável por indicar o número da pergunta.
    if($_SESSION['nq'] < count($questions) -1) {
        $_SESSION['nq']++;
    // fim de jogo, redireciona o usuário para página scoreQuiz.php mostrando as estatísticas da partida.
    } else {
        
        echo "<script>alert('Acabou!')</script>";
        $_SESSION['score'] = (100 / count($questions)) * $_SESSION['user_successes'];

        header("Location:scoreQuiz.php?quizId=$quizId");
    }
}

// Define valores que serão exibidos no conteiner
$numberQuestion = $_SESSION['nq'] +1; //Número da pergunta 
$dirName = "img_quiz/" . $quizId . "/"; // Diretório da imagem
$fileName = $img[$_SESSION['nq']]; // Nome do arquivo
$imgName = ""; // Imagem que será mostrada 

// Verifica se a pergunta possui uma imagem cadastrada no bd e define a imagem que será exibida. 
if($img[$_SESSION['nq']] == NULL){
    $imgName = "img_quiz/default/default_img.jpg";
}
else{
    $imgName = $dirName . $fileName;
}


// Usuário clica em "desistir"
if(isset($btnAction['surrender_btn'])){

}

?>

<script>
    function confirmSurrender(){
        var confirmLocation = confirm("ATENÇÃO!! \nSe você desistir, não poderá mais obter pontos com este quiz !");

        if(confirmLocation){
            window.location.href = "playQuiz.php?surrender=yes&quizId=$quizId";
        }
    }
</script>

<!-- Container  -->
<div class="play_container">

    <!-- Cabeçalho do container -->
    <div class="play_container_header">
        <h1>Pergunta <?php  echo "{$numberQuestion} / " . count($questions);  ?> </h1>
        <p>Acertos: <?php echo $_SESSION['user_successes'] ?> </p>
    </div>

    <!-- Pergunta do quiz  -->
    <div class="play_container_question">
        <p>P: <?php echo $questions[$_SESSION['nq']]  ?> </p>
    </div>

    <!-- Imagem do quiz (se não houver foto cadastrada, deve mostrar uma imagem padrão) -->
    <div class="question_img">
        <img src=<?php echo $imgName ?> alt="" width="300">
    </div>

    <!-- Formulário / alternativas para a pergunta  -->
    <div class="play_container_form">
        <form action='' method='POST'>
            <ul>
                <li>
                    <input type='radio' value='a' name='radio_input'>
                    <label for='a'><?php echo $alternatives[$_SESSION['nq']]['A'] ;?></label>
                </li>

                <li>
                    <input type='radio' value='b' name='radio_input'>
                    <label for='b'><?php echo $alternatives[$_SESSION['nq']]['B'] ;?></label>
                </li>

                <li>
                    <input type='radio' value='c' name='radio_input'>
                    <label for='c'><?php echo $alternatives[$_SESSION['nq']]['C'] ;?></label>
                </li>

                <li>
                    <input type='radio' value='d' name='radio_input'>
                    <label for='d'><?php echo $alternatives[$_SESSION['nq']]['D'] ;?></label>
                </li>

                <input type='submit' name='next' value='Próxima' class="next_btn">

            </ul>
        </form>
    </div>
</div>

</body>
</html>