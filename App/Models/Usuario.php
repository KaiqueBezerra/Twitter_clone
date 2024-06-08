<?php

    namespace App\Models;

    use MF\Model\Model; // sempre chamar isso quando criar um classe

    //classe de usuario
    class Usuario extends Model { // sempre que criar classe usar o extends Model

        private $id;
        private $nome;
        private $email;
        private $senha;

        public function __get($atributo) {
            return $this->$atributo;
        }

        public function __set($atributo, $valor) {
            $this->$atributo = $valor;
        }

        //salvar
        public function salvar() {
            $query = "
                insert into usuarios(nome, email, senha)
                values(:nome, :email, :senha)
            ";

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':nome', $this->__get('nome'));
            $stmt->bindValue(':email', $this->__get('email'));
            $stmt->bindValue(':senha', $this->__get('senha')); //md5() -> hash 32 caracteres
            $stmt->execute();

            return $this;

        }

        //validar se um cadastro pode ser feito
        public function validarCadastro() { // metodo para validar cadastro

            $valido = true;  // valido já vem true

            if (strlen($this->__get('nome') < 3)) { // mas caso o nome, email ou senha tiver menos do que 3 caracteres ele se tornará false
                $valido = false;
            }

            if (strlen($this->__get('email') < 3)) { // poderiamos usar outros meios para validar o usuario, mas para essa aplicação isso já basta
                $valido = false;
            }

            if (strlen($this->__get('senha') < 3)) {
                $valido = false;
            }

            return $valido; // retorna true se tiver passado pela validação e false se não

        }

        //recuperar um usuario por e-mail
        public function getUsuarioPorEmail() // metodo para ver se o usuario já existe no banco 
        {

            $query = "
                select 
                    nome, email 
                from 
                    usuarios
                where 
                    email = :email
            ";

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':email', $this->__get('email')); // vai pegar o email passado pelo usuario no formulario e dar um select com base no email
            $stmt->execute();
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC); // vai retornar um array associativo do select, caso o email exista retorno um array com o nome do usuario e o email, caso não exista retorna um array vazio

        }

        public function autenticar() { // metodo para logar o usuario

            $query = "
                select 
                    id, nome, email
                from
                    usuarios
                where
                    email = :email and senha = :senha
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindvalue(':email', $this->__get('email'));
            $stmt->bindvalue(':senha', $this->__get('senha'));
            $stmt->execute();

            $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($usuario['id'] != '' && $usuario['nome'] != '') { // depois do usuario informar o email e senha, o metodo ira fazer o select, se ele retornar um email e senha que existam no banco

                $this->__set('id', $usuario['id']); // ele vai setar o id e a senha do usuario retornado nos atributos da classe usuario
                $this->__set('nome', $usuario['nome']);

            }

            return $this; // vai retornar a classe para o AuthController

        }

        public function getAll() { // metodo de pesquisa para retornar os nomes do banco

            $query = "
                select 
                    u.id,
                    u.nome,
                    u.email,
                    (
                        select
                            count(*)
                        from
                            usuarios_seguidores as us
                        where
                            us.id_usuario = :id_usuario and us.id_usuario_seguindo = u.id
                        
                    ) as seguindo_sn
                from
                    usuarios as u
                where
                    u.nome like :nome and u.id != :id_usuario
            ";

            // no where o id != :id_usuario serve para que não seja retornado o proprio usuario quando for pesquisar
            // o sub select vai retornar da tabela usuarios_seguidores 1 ou 0, se o usuario estiver sendo seguido vai retornar 1 e se não vai retornar 0 e ele vai aparecer no retorno como seguindo_sn
            // o seguindo_sn vai ser usado no quemSeguir.phtml para aparecer o botão de adicionar apenas se o seguindo_sn for igual a 0 e o botão remover se seguindo_sn for igual a 1

            $stmt = $this->db->prepare($query);
            $stmt->bindvalue(':nome', '%'.$this->__get('nome').'%'); // por exemplo caso no get seja passado a letra 'a' por causa dos coringas % antes e depois do get ele retornará qualquer nome que tenha o a no nome // se eu pesquisar o nome kaique ele retornará qualquer usuario que tenha kaique no nome, seja o primeiro nome ou segundo
            $stmt->bindvalue(':id_usuario', $this->__get('id'));
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);

        }

        public function seguirUsuario($id_usuario_seguindo) { // metodo que vai inserir o id do usuario que está seguindo, e o id do usuario que está sendo seguido // parametro do id do usuario que vai ser seguido passado quando o metodo for ativado no AppController

            $query = "
                insert into usuarios_seguidores(id_usuario, id_usuario_seguindo)
                values(:id_usuario, :id_usuario_seguindo)
            ";

            $stmt = $this->db->prepare($query);
            $stmt->bindvalue(':id_usuario', $this->__get('id'));
            $stmt->bindvalue(':id_usuario_seguindo', $id_usuario_seguindo);
            $stmt->execute();

            return true;

        }

        public function deixarSeguirUsuario($id_usuario_seguindo) { // metodo que vai excluir o dado que foi passado no seguirUsuario // parametro do id do usuario que vai ser seguido passado quando o metodo for ativado no AppController

            $query = "
                delete
                from
                    usuarios_seguidores
                where
                    id_usuario = :id_usuario and id_usuario_seguindo = :id_usuario_seguindo
            ";

            $stmt = $this->db->prepare($query);
            $stmt->bindvalue(':id_usuario', $this->__get('id'));
            $stmt->bindvalue(':id_usuario_seguindo', $id_usuario_seguindo);
            $stmt->execute();

            return true;

        }

        // informações do usúario
        public function getInfoUsuarios() { // metodo retorna o nome do usuario
            $query = "
                select 
                    nome 
                from
                    usuarios
                where
                    id = :id_usuario
            ";

            $stmt = $this->db->prepare($query);
            $stmt->bindvalue(':id_usuario', $this->__get('id'));
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }

        // total de tweets
        public function getTotalTweets() { // metodo retorna o total de tweets do usuario
            $query = "
                select 
                    count(*) as total_tweet 
                from
                    tweets
                where
                    id_usuario = :id_usuario
            ";

            $stmt = $this->db->prepare($query);
            $stmt->bindvalue(':id_usuario', $this->__get('id'));
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }


        // total de usuarios que estamos seguindo
        public function getTotalSeguindo() { // metodo retorna o total de pessoas que o usuario está seguindo
            $query = "
                select 
                    count(*) as total_seguindo
                from
                    usuarios_seguidores
                where
                    id_usuario = :id_usuario
            ";

            $stmt = $this->db->prepare($query);
            $stmt->bindvalue(':id_usuario', $this->__get('id'));
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }


        // total de seguidores
        public function getTotalSeguidores() { // metodo retorna o total de pessoas que seguem o usuario
            $query = "
                select 
                    count(*) as total_seguidores
                from
                    usuarios_seguidores
                where
                    id_usuario_seguindo = :id_usuario
            ";

            $stmt = $this->db->prepare($query);
            $stmt->bindvalue(':id_usuario', $this->__get('id'));
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }

		public function tema() { // retornando o tema do usuario logado, por padrão o tema vem como 'tema'
			$query = "select tema from usuarios where id = :id_usuario";
			
			$stmt = $this->db->prepare($query);
			$stmt->bindValue(':id_usuario', $this->__get('id'));
			$stmt->execute();
			
			return $stmt->fetch(\PDO::FETCH_ASSOC);
		}

		public function alterarTema() { // ao clicar no botão de tema escuro, o 'tema' será alterado para 'temaEscuro'
			$query = "update usuarios set tema = 'temaEscuro' where id = :id_usuario";
			
			$stmt = $this->db->prepare($query);
			$stmt->bindValue(':id_usuario', $this->__get('id'));
			$stmt->execute();
			
			return $this;
		}
		
		public function alterarTemaEscuro() { // ao clicar no botão de tema claro, o 'temaEscuro' será alterado para 'tema'
			$query = "update usuarios set tema = 'tema' where id = :id_usuario";
			
			$stmt = $this->db->prepare($query);
			$stmt->bindValue(':id_usuario', $this->__get('id'));
			$stmt->execute();
			
			return $this;
		}

    }

?>