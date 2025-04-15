<?php
require_once "Conn.php";

class Quiz extends Conn{
    public object $conn;
    public array $formData; // Dados do formulário (respostas do usuário)
    public int $id; // id do quiz
    public int $numberQuestion; // Numero da pergunta
    public string $img; // Imagem de fundo da pergunta do quiz
    public int $player; // id do usuário 


    // Construct
    public function __construct(){
        $this -> conn = $this -> connectDb();
    }

    // Criar um novo QUIZ 
    public function createNewQuiz($title, $author) : bool{
        $add_quiz = $this -> conn -> prepare("INSERT INTO quiz.quiz (title, author) VALUES(:title , :author)");
        $add_quiz -> bindParam(":title" , $title);
        $add_quiz -> bindParam(":author" , $author);

        $add_quiz -> execute();
        $this -> id = $this -> conn -> lastInsertId();

        return $add_quiz -> rowCount() ? true : false;
    }

    // Insere as perguntas no QUIZ 
    public function addNewQuestion() : bool{
        $add_question = $this -> conn -> prepare("INSERT INTO quiz.questions 
                                                  (n_question, quiz_id, question, alt_a, alt_b, alt_c, alt_d, right_alt, img)
                                                   VALUES(:nQuestion, :cod, :question, :altA, :altB, :altC, :altD, :rightAlt, :img)");

        $add_question -> bindParam(":nQuestion", $this -> formData['numero']);
        $add_question -> bindParam(":cod", $this -> id); // Código do quiz - De preferência mudar para idQuiz 
        $add_question -> bindParam(":question", $this -> formData['pergunta']);
        $add_question -> bindParam(":altA", $this -> formData['alternativaA']);
        $add_question -> bindParam(":altB", $this -> formData['alternativaB']);
        $add_question -> bindParam(":altC", $this -> formData['alternativaC']);
        $add_question -> bindParam(":altD", $this -> formData['alternativaD']);
        $add_question -> bindParam(":rightAlt", $this -> formData['rightAlt']);
        $add_question -> bindParam(":img" , $this -> img);

        $add_question -> execute();
        
        return $add_question -> rowCount() ? true : false;
    }

    public function playQuiz(){
        $getQuiz = $this -> conn -> prepare("SELECT quizInfo.id AS id,
                                                    quizInfo.title AS quizTitle,
                                                    quizInfo.author AS quizAuthor, 
                                                    quizInfo.players AS quizPlayers, 

                                                    quizQuestion.n_question AS nQuestion,
                                                    quizQuestion.quiz_id AS quizCod,
                                                    quizQuestion.question AS question,
                                                    quizQuestion.alt_a AS A,
                                                    quizQuestion.alt_b AS B,
                                                    quizQuestion.alt_c AS C,
                                                    quizQuestion.alt_d AS D,
                                                    quizQuestion.right_alt AS rightAlt,
                                                    quizQuestion.img AS quizImg
                                             FROM quiz.quiz AS quizInfo, quiz.questions AS quizQuestion
                                             WHERE quizInfo.id = :quizId AND quizInfo.id = quizQuestion.quiz_id
                                             ORDER BY quizQuestion.n_question ASC");

        $getQuiz -> bindParam(":quizId" , $this -> id);
        $getQuiz -> execute();

        return $getQuiz -> fetchAll();
    }
}




?>

