<?php

session_start();
require_once "Conn.php";
require_once "User.php";
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
    <link rel="stylesheet" href="css/register.css">


    <title>Cadastro</title>
</head>
<body>
<?php
if(isset($_SESSION['feedback'])){
    echo $_SESSION['feedback'];
    unset($_SESSION['feedback']);
}

$formData = filter_input_array(INPUT_POST, FILTER_DEFAULT);
?>


<div class="container">

    <div class="header">
        <h1>Cadastro</h1>
    </div>

    <form action="" method="POST" enctype="multipart/form-data">
        <label>Nome de usuário</label>
        <input type="text" name="username">

        <label>Senha</label>
        <input type="password" name="pass">

        <label>Confirme a senha</label>
        <input type="password" name="confirmPass">

        <label for="profile_submit" class="profilePhoto-submit">Selecionar foto de perfil</label>
        <input type="file" name="profilePhoto" id="profile_submit">

        <input type="submit" value="Cadastrar" class="register-btn">
        <a href="login.php">Já sou cadastrado - Fazer login</a>
    </form>

</div>


<?php

if (isset($formData)) {

    if(!empty($formData['username'] && !empty($formData['pass']) && !empty($formData['confirmPass']))){
        $user = new User();
        $user -> formData = $formData;
        $user -> profilePhoto = $_FILES['profilePhoto']['name'];

        if($formData['pass'] == $formData['confirmPass']){
            $img = $_FILES['profilePhoto']['name'];
            $insert = $user -> create();
            if($insert){
                if($img){
                    $user_insert_id = $user -> conn -> lastInsertId();
                    $file_type = $_FILES['profilePhoto']['type'];

                    // Define diretório
                    $uploadDir = 'profile_img/' . $user_insert_id . '/';
                    $uploadFile = $user_insert_id . '_profilePhoto.jpg';
                    $fullPath = $uploadDir . $uploadFile;
                    
                    // Verifica e cria o diretório, se não existir
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true); 
                    }

                    // Move arquivo temporário para o diretório criado renomeado com o nome padrão.
                    if(move_uploaded_file($_FILES['profilePhoto']['tmp_name'] , $fullPath)){
                        $_SESSION['feedback'] = "Usuário cadastrado com sucesso !<br>";
                        header("Location:login.php");
                        exit;
                    }
                    // Erro ao salvar a foto no diretório correto.
                    else {
                        $_SESSION['feedback'] = "Erro ao salvar a foto de perfil.";
                    }
                }
                // $img não foi submetido com sucesso
                else {
                    $_SESSION['feedback'] = "Erro ao realizar o upload da foto de perfil.";
                }   
            }
            // Erro ao executar o método create(); 
            else {
                $_SESSION['feedback'] = $user -> feedback;
            }   
        }
        // Senhas não coincidem. 
        else {
            $_SESSION['feedback'] = "As senhas não coincidem";
        }
    }
    // Algum campo não foi preenchido. 
    else {
        $_SESSION['feedback'] = "Preencha todos os campos do formulário.";
    }

    // Se o cadastro não for concluído, a página será atualizada para mostrar o feedback. Do contrário redireciona para login.php
    session_write_close();  // Garante que a sessão foi salva
    header("Location: register.php");  // Redireciona de volta para a página de cadastro
    exit; // Sempre finalize após o header
}

?>
</body>
</html>