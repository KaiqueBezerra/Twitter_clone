<?php

    namespace App\Models;

    use MF\Model\Model; // sempre chamar isso quando criar um classe

    //classe de Tweet
    class Tweet extends Model { // sempre que criar classe usar o extends Model

        private $id;
        private $id_usuario;
        private $tweet;
        private $data;

        public function __get($atributo) {
            return $this->$atributo;
        }

        public function __set($atributo, $valor) {
            $this->$atributo = $valor;
        }

        //salvar
        public function salvar() { // metodo para salvar os tweets

            $query = "
                insert into tweets (id_usuario, tweet)
                values (:id_usuario, :tweet)
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindvalue(':id_usuario', $this->__get('id_usuario')); // manda o id do usuario e o conteudo do tweet para o banco de dados
            $stmt->bindvalue(':tweet', $this->__get('tweet'));
            $stmt->execute();

            return $this;

        }

        //recuperar
        public function getAll() { // metodo para retornar as informações da tabela tweet e o nome da tabela usuario

            $query = "
                select
                    t.id,
                    t.id_usuario,
                    u.nome,
                    t.tweet,
                    DATE_FORMAT(t.data, '%d/%m/%Y %h:%i') as data
                from 
                    tweets as t
                    left join usuarios as u on (t.id_usuario = u.id)
                where
                    t.id_usuario = :id_usuario 
                    or t.id_usuario in (select id_usuario_seguindo from usuarios_seguidores 
                    where id_usuario = :id_usuario)
                order by
                    t.data desc
            ";

            // DATE_FORMAT(t.data, '%d/%m/%Y %h:%i') // é uma função que espera dois parametros, uma data, e o segundo como a data será formatada // o 'as data' é para que a data formata retorne dentro do indice data no array que será retornado
            // o t.id_usuario = :id_usuario or t.id_usuario in (...) serve para que seja retornado o proprio tweet ou o tweet de quem o usuario esteja seguindo o select dentro de () vai retornar o id do usuario que está sendo seguido

            $stmt = $this->db->prepare($query);
            $stmt->bindvalue(':id_usuario', $this->__get('id_usuario'));
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC); // retorna um array contendo as informações do tweet e do usuario

        }

        public function getPorPagina($limit, $offset) { // vai retornar o total de tweets a partir do ponto passado por offset e vai limitar a 10

            $query = "
                select 
                    t.id, 
                    t.id_usuario, 
                    u.nome, 
                    t.tweet, 
                    DATE_FORMAT(t.data, '%d/%m/%Y %H:%i') as data
                from 
                    tweets as t
                    left join usuarios as u on (t.id_usuario = u.id)
                where 
                    t.id_usuario = :id_usuario
                    or t.id_usuario in (select id_usuario_seguindo from usuarios_seguidores where id_usuario = :id_usuario)
                order by
                    t.data desc
                limit
                    $limit
                offset
                    $offset
            ";
    
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
            $stmt->execute();
    
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
    
        public function getTotalRegistros() { // vai retornar o total de tweets do usuario e dos usuarios que ele segue
    
            $query = "
                select 
                    count(*) as total
                from 
                    tweets as t
                    left join usuarios as u on (t.id_usuario = u.id)
                where 
                    t.id_usuario = :id_usuario
                    or t.id_usuario in (select id_usuario_seguindo from usuarios_seguidores where id_usuario = :id_usuario)
            ";
    
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
            $stmt->execute();
    
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }

        public function removerTweet() { // metodo para excluir o tweet (feito por mim)

            $query = "
                delete
                from
                    tweets
                where
                    id_usuario = :id_usuario and id = :id_tweet
            ";

            $stmt = $this->db->prepare($query);
            $stmt->bindvalue(':id_usuario', $this->__get('id_usuario'));
            $stmt->bindvalue(':id_tweet', $this->__get('id'));
            $stmt->execute();

            return $this;

        }

    }

?>