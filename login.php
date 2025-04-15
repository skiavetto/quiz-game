<?php
// Erros a serem resolvidos: caso o usuário inserido não exista retornará NULL.  
// Autenticação está falhando. 

require_once "Conn.php";
require_once "User.php";
session_start();

// Armazena dados do formulário
$formData = filter_input_array(INPUT_POST, FILTER_DEFAULT);
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
    <link rel="stylesheet" href="css/login.css">

    <title>Autenticação do usuário</title>
</head>
<body>

<?php 
// Mensagem de feedback 
if (isset($_SESSION['feedback'])) {
    echo $_SESSION['feedback'];
    unset($_SESSION['feedback']);
}

?>

<div class="container">

    <h1>Login</h1>

        <form action="" method="POST" name="login_form">

        <label for="username">Usuário</label>
        <input type="text" name="username" id="username">

        <label for="pass">Senha</label>
        <input type="password" name="pass" id="pass">

        <input type="submit" name="sendLogin" class="login-btn" value="Entrar">
        <a href="#" class="forgetPass-link">Esqueci minha senha</a>
        <a href="register.php" class="register-link">Cadastre-se</a>

        </form>

</div>


<?php
if(!empty($formData)){

    // Verifica o campo de usuário ou senha estão vazios 
    if($formData['username'] == NULL || $formData['pass'] == NULL){
        echo "Preencha todos os campos.";
    }
    // Caso ambos forem preenchidos: 
    else {

        // Instancia método login();
        $user = new User();
        $user -> formData = $formData;

        // Faz o login chamando o método login 
        $row = $user -> login();

        
        // Verifica se usuário existe.
        if($row){
            
            // Verifica se a senha fornecida corresponde a hash armazenada no bd.
            if(password_verify($formData['pass'], $row['password'])){
                // Senha correta. Cria sessão e redireciona o usuário.
                $_SESSION['userId'] = $row['id'];
                $_SESSION['username'] = $row['username'];

                // Se houver um quiz em andamento redireciona o usuário para ele
                if(isset($_SESSION['quizOnHold'])){
                    $quizId = $_SESSION['quizOnHold'];
                    header("Location:playQuiz.php?quizId=$quizId");
                    exit;
                }
                // Se não, redireciona para página inicial. 
                else{
                    header("Location:home.php");
                    exit;
                } 
            }
            // Senha incorreta: 
            else{
                $_SESSION['feedback'] = "Senha incorreta ! <br>";
                var_dump($formData['pass']);
                var_dump($row['password']);
            }
        }
        // Usuário não foi encontrado no bd:
        else {
            $_SESSION['feedback'] = "O usuário informado não existe !";
        }
    }
}

?>
    
</body>
</html>