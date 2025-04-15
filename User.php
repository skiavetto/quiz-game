<?php
require_once "Conn.php";

class User extends Conn {
    public int $id;
    public int $quiz_id;
    public string $profilePhoto;
    public string $username;
    public object $conn;
    public array $formData;
    public string $feedback;

    // Métodos 

    // Construtor
    public function __construct(){
        $this -> conn = $this -> connectDb();
    }


    // Cadastra um novo usuário 
    public function create(): bool{
        // Select para validar se o usuário já existe no banco. 
        $select = $this -> conn -> prepare("SELECT COUNT(username) FROM quiz.usuarios WHERE username = :username");
        $select -> bindParam(":username" , $this -> formData['username']);

        
        if($select -> execute()){
            // Se usuário existir cadastro retorna erro. 
            if($select -> fetchColumn() > 0){
                $this -> feedback = "Usuário já existe";
                return false;
            }

            // Faz o insert
            else {
                $create = $this -> conn -> prepare("INSERT INTO quiz.usuarios 
                (username, password, profile_photo, score) 
                VALUES (:username, :password, :photo, 0 )");
                
                // Criptografa a senha 
                $hashedPassword = password_hash($this->formData['pass'], PASSWORD_DEFAULT);

                $create -> bindParam(":username" , $this -> formData['username']);
                $create -> bindParam(":password" , $hashedPassword);
                $create -> bindParam(":photo" , $this -> profilePhoto);
                $create -> execute();

                return $create -> rowCount() ? true : false;
            }
        }
    }


    // Método de login (autenticação do usuário)
    public function login(){
        $query = $this -> conn -> prepare("SELECT id, username, password 
        FROM quiz.usuarios 
        WHERE username = :username");

        $query -> bindParam(":username" , $this -> formData['username']);
        $query -> execute();

        if(($query) AND ($query -> rowCount() != 0)){
                return $query -> fetch();
        }
    }


    // Seleciona os dados do usuário para visualização do perfil 
    public function viewProfile(){
        $view = $this -> conn -> prepare("SELECT id, username, profile_photo, score
                                            FROM quiz.usuarios
                                            WHERE id = :id");
        $view -> bindParam(":id" , $this -> id);

        if($view -> execute()){
            return $view -> fetch();
        } 
    }


    // Seleciona todos os quiz que foram criados pelo usuário 
    public function viewCreatedQuiz(){
        $view = $this -> conn -> prepare("SELECT id AS cod, title AS quizName, players
                                                    FROM quiz.quiz
                                                    WHERE author = :id");

        $view -> bindParam(":id", $this -> id);
        $view -> execute();

        return $view -> fetchAll();;
        }   


    // Verifica quantos quiz foram criados pelo usuário    
    public function countQuiz(){
        $view = $this -> conn -> prepare("SELECT COUNT(author) AS countQuiz
                                            FROM quiz.quiz
                                            WHERE author = :id");

        $view -> bindParam(":id", $this -> id);
        
        if($view -> execute()){
            return $view -> fetch();
        }   
    }


    // Método de busca (pesquisar um quiz)
    public function searchQuiz(){
        $search = $this -> conn -> prepare("SELECT quiz.id AS quiz_id , quiz.title AS quiz_title , quiz.author AS quiz_author , quiz.players AS quiz_players
                                                FROM quiz.quiz AS quiz
                                                WHERE quiz.id LIKE :inputSearch OR quiz.title LIKE :inputSearch");
                                                
        $inputSearch = "%" . trim($this -> formData['search_input']) . "%";
        $search -> bindParam(":inputSearch" , $inputSearch);
        $search -> execute();

        return $search -> fetchAll();
    }

    public function quizHistory(): array{
        $history = $this -> conn -> prepare("SELECT quiz.id AS quiz_id, quiz.title AS quiz_title, 
                                                    players.match_date AS match_date
                                            FROM quiz.players, quiz.quiz 
                                            WHERE players.user_id = :userId");
        $history -> bindParam(":userId" , $this -> id);
        
        if($history-> execute()){
            return $history -> fetchAll();
        }    
    }
    

    // Registra na tabela players que o usuário já acessou o referido quiz. 
    public function finishMatch($newPoints , $quizId): bool{

        // Verifica se já existe registros do usuário nesse quiz 
        $consult = $this -> conn -> prepare("SELECT COUNT(user_id) 
                                            FROM quiz.players
                                            WHERE quiz_id = :quiz_id AND user_id = :user_id");
        $consult -> bindParam(":quiz_id" , $quizId);
        $consult -> bindParam(":user_id" , $this -> id);
        $consult -> execute();
        
        // Se usuário já houver jogado o quiz apenas atualiza a data da ultima partida. 
        if($consult -> fetchColumn() > 0){
            $updateMatchDate = $this -> conn -> prepare("UPDATE quiz.players 
                                                        SET match_date = NOW()
                                                        WHERE quiz_id = :quiz_id AND user_id = :user_id");
            $updateMatchDate -> bindParam(":quiz_id" , $quizId);
            $updateMatchDate -> bindParam(":user_id" , $this -> id);

            // Informa o usuário que o quiz não acumulará pontos no seu saldo. 
            $this -> feedback = "<span class='noScore-feedback'>Você não pode acumular novos pontos pois já recebeu pontos com este quiz anteriormente. Porém você pode continuar jogando-o por diversão sempre que quiser.</span>";
            
            return $updateMatchDate -> execute() ? true : false;
        }

        // Caso o usuário nunca tenha jogado, insere o registro no banco para não somar mais pontos. 
        else {

            // Insere a junção de id do usuário e do quiz na tabela players para registrar a partida.
            $addPlayer = $this -> conn -> prepare("INSERT INTO quiz.players (quiz_id, user_id, match_date) 
                                                VALUES(:quiz_id , :user_id, NOW())");
            $addPlayer -> bindParam(":user_id" , $this -> id);
            $addPlayer -> bindParam(":quiz_id" , $quizId);
            $addPlayer -> execute();


            // Atualiza a coluna score do usuário acrescentando os pontos obtidos na partida aos seus pontos totais.
            $increasePoints = $this -> conn -> prepare("UPDATE quiz.usuarios 
                                                SET score = score + :newPoints
                                                WHERE id = :id");

            $increasePoints -> bindParam(":newPoints" , $newPoints);
            $increasePoints -> bindParam(":id" , $this -> id);
            $increasePoints -> execute();

            // Mostra para o usuário os pontos adquiridos com o jogo 
            $this -> feedback = "<span class='score-feedback'>" . $_SESSION['score'] . "</span>";
           
            return $addPlayer -> rowCount() && $increasePoints -> rowCount() ? true : false;
        }
    }

    // Antes a função increase era o trecho de código de update, passando $newPoints como parâmetro.
}


?>