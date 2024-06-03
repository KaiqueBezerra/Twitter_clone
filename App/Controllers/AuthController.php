<?php

    namespace App\Controllers;

    //recursos do miniframework
    use MF\Controller\Action;
    use MF\Model\Container;

    // AuthController controla os usuarios

    class AuthController extends Action {

        public function autenticar() { // action que será ativado ao apertar em entrar na index
             
            $usuario = Container::getModel('Usuario'); // instanciando o model Usuario

            $usuario->__set('email', $_POST['email']);
            $usuario->__set('senha', md5($_POST['senha'])); // o md5 serve para criptografar a senha em uma string de 32 caracteres

            $usuario->autenticar(); // caso o usuario tenha colocado um email e senha válido, vai retornar a classe usuario com os atributos preenchidos, caso seja invalido, retornará vazio

            // echo '<pre>';
            // print_r($usuario);
            // echo '</pre>';

            if ($usuario->id != '' && $usuario->nome != '') { // verificando se o id e o nome que chegou no metodo autenticar é diferente de vazio, se estiver vazio cai no else

                session_start(); // iniciando session

                $_SESSION['id'] = $usuario->id; // criando dois atributos na session e passando o id e o nome que veio do metodo autenticar para ele
                $_SESSION['nome'] = $usuario->nome;

                header('location: /timeline'); // redirecionado para a página protegida

            } else {

                header('location: /?login=erro'); // redirecionado para a página inicial com erro

            }

        }

        public function sair() { // action que será ativado ao apertar o botão de sair

            session_start(); // inicia a session

            session_destroy(); // destroi a session

            header('location: /'); // redireciona para a página principal

        }

    }

?>