<?php

abstract class Conn
{
    public string $db = "mysql";
    public string $host = "localhost";
    public string $dbUser = "root";
    public string $dbPassword = "";
    public string $dbname = "quiz";
    public int $port = 3306;
    public object $connect;

    public function connectDb()
    {
        try{
            //Conexao com a porta
            //$this->connect = new PDO($this->db . ':host=' . $this->host . ';port=' . $this->port . ';dbname='. $this->dbname, $this->dbUser, $this->dbPassword);
            
            //Conexao sem a porta
            $this->connect = new PDO($this->db . ':host=' . $this->host . ';dbname='. $this->dbname, $this->dbUser, $this->dbPassword);
            
            //echo "Conexão com banco de dados realizado com sucesso!<br>";
            return $this->connect;
        }catch (Exception $err){
            die('Erro' . $err);
            //echo "Erro: Conexão com banco de dados não realizado com sucesso! Erro gerado: " . $err->getMessage();
        }
    }
}

    

?>