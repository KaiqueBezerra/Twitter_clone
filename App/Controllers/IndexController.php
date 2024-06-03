<?php

    namespace App\Controllers;

    //recursos do miniframework
    use MF\Controller\Action;
    use MF\Model\Container;

    // indexController controla as páginas

    class IndexController extends Action { // sempre usar extend Action

        public function index() {

            $this->view->login = isset($_GET['login']) ? $_GET['login'] : ''; // se o parametro login existir (login=erro), será colocado dentro do atributo login, se não o atributo fica vazio // provavelmente nem precisaria fazer isso, pois se o header for utilizado no else do metodo registrar, ele já enviaria o parametro que seria verificado no index.phtml

            $this->render('index','layout'); // o segundo parametro é para renderizar o layout, porém, ele já é passado por default, caso não seja passado nenhum parametro, o default será 'layout'

        }

        public function inscreverse() { // action que será ativado ao clicar no botão de inscrever-se

            $this->view->usuario = array( // cria o atributo usuario que conterá um array vazio para deixar os values do input vazio e não dar erro ao preencher o input no else do registrar

                'nome' => '',
                'email' => '',
                'senha' => ''

            );

            $this->view->erroCadastro = false; // cria o atributo erroCadastro como false para não ativar a mensagem de erro quando entrar na página de inscrição

            $this->render('inscreverse','layout'); // renderiza a página de inscricao
            

        }

        public function registrar() { // action que será ativado ao enviar o formulario de inscrição

            //receber os dados do formulario

            $usuario = Container::getModel('Usuario'); // instanciando o model Usuario

            $usuario->__set('nome', $_POST['nome']); // setando os valores recebidos no formulario para os atributos
            $usuario->__set('email', $_POST['email']);
            $usuario->__set('senha', $_POST['senha']);

            if ($usuario->validarCadastro() && count($usuario->getUsuarioPorEmail()) == 0) { // verificando se o metodo validaCadastro do Usuario retornou true e se o metodo getUsuarioPorEmail retornou um array com o usuario
                
                    $usuario->__set('senha', md5($_POST['senha']));

                    $usuario->salvar(); // cadastrando o usuario no banco

                    //sucesso
                    $this->render('cadastro'); // renderizando a página de sucesso no cadastro              
                
            } else { // caso o metodo getUsuarioPorEmail retorne algum usuario ou o metodo ValidaCadastro retorne false ele entra no else

                //erro

                $this->view->usuario = array( // cria o atributo usuario que conterá um array com o nome, email e senha colocado pelo usuario, para que quando de erro, volta para a tela de inscrição com os campos preenchidos

                    'nome' => $_POST['nome'],
                    'email' => $_POST['email'],
                    'senha' => $_POST['senha']

                );

                $this->view->erroCadastro = true; // cria o atributo erroCadastro como true para ativar a mensagem de erro quando entrar na página de inscrição novamente 

                $this->render('inscreverse'); // renderiza a página de inscrição novamente // provavelmente tbm funcionaria com o header, ao inves de usar o atributo erroCadastro, é só passar um parametro ?cadastro=erro e verificar se o cadastro=erro existe no inscreverse.phtml

            }        

        }

    }

?>