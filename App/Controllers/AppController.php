<?php

namespace App\Controllers;

//recursos do miniframework
use MF\Controller\Action;
use MF\Model\Container;

// controla o conteudo das páginas

class AppController extends Action {

    public function timeline() {

		$this->validaAutenticacao();
			
		//recuperação dos tweets
		$tweet = Container::getModel('Tweet');

		$tweet->__set('id_usuario', $_SESSION['id']);

		//variaveis de paginação
		$total_registros_pagina = 10;
		$pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 1;
		$deslocamento = ($pagina - 1) * $total_registros_pagina;

		$tweets = $tweet->getPorPagina($total_registros_pagina, $deslocamento);
		$total_tweets = $tweet->getTotalRegistros();
		$this->view->total_de_paginas = ceil($total_tweets['total'] / $total_registros_pagina);
		$this->view->pagina_ativa = $pagina;

		$this->view->tweets = $tweets;

		$usuario = Container::getModel('Usuario');
		$usuario->__set('id', $_SESSION['id']);

		$this->view->info_usuario = $usuario->getInfoUsuarios();
		$this->view->total_tweets = $usuario->getTotalTweets();
		$this->view->total_seguindo = $usuario->getTotalSeguindo();
		$this->view->total_seguidores = $usuario->getTotalSeguidores();

        $this->view->tema = $usuario->tema();

		$this->render('timeline');
		
		
	}

    public function tweet() { // action tweet que será ativado após o usuario enviar o formulario de envio de tweets

            $this->validaAutenticacao(); // metodo para verificar se o usuario está realmente logado

            $tweet = Container::getModel('Tweet'); // instanciando o obj Tweet

            $tweet->__set('tweet', $_POST['tweet']); // passando o conteudo do tweet para o atributo tweet
            $tweet->__set('id_usuario', $_SESSION['id']); // passando o id do usuario recebido por session para o atributo id_usuario

            $tweet->salvar(); // executando o metodo salvar que salva o tweet e o id do usuario que fez o tweet

            header('location: /timeline'); // retorna para timeline

    }

    public function validaAutenticacao() { // metodo para verificar se o usuario está realmente logado

        session_start(); // iniciando a session

        if(!isset($_SESSION['id']) || $_SESSION['id'] == '' || !$_SESSION['nome'] || $_SESSION['nome'] == '') { // verificando se o id e nome da session estão vazios, e se o id e nome da session não estão setados

            header('location: /?login=erro'); // volta a página inicial com erro

        }

    }

    public function quem_seguir() { // action que será ativado quando o usuario clicar no botão de procurar pessoas conhecidas

        $this->validaAutenticacao(); // metodo para verificar se o usuario está realmente logado

        $pesquisarPor = isset($_GET['pesquisarPor']) ? $_GET['pesquisarPor'] : ''; // quando o usuario enviar o formulario de pesquisa contendo o nome da pessoa que ele quer seguir, ele será redirecionado
        // para a a mesma pagina quem_seguir porém o nome digitado será passado por get, a variavel $pesquisarPor verificara se o $_GET existe, se existir ele passará o conteudo dele para a variavel, no caso, o nome passado pelo usuario
        // mas se não existir ele passara um valor vazio

        $usuarios = array(); // criando a variavel usuarios e dizendo que ela sera um array

        $usuario = Container::getModel('Usuario'); // estanciando o obj usuario
        $usuario->__set('nome', $pesquisarPor); // passando para o atributo nome, o valor de $pesquisarPor que conterá o nome ou letra digitado pelo usuario
        $usuario->__set('id', $_SESSION['id']);

        if ($pesquisarPor != '') { // se a variavel $pesquisarPor for diferente de vazia ele entra no if

            $usuarios = $usuario->getAll(); // executando o metodo getAll que retorna um array dos nomes retornados com base no que foi escrito pelo usuario

        }

        //criando atributos e passando arrays que foram retornados atraves dos metodos para ser implementados na view timeline.phtml e quemSeguir.phtml
        $this->view->info_usuario = $usuario->getInfoUsuarios();
        $this->view->total_tweets = $usuario->getTotalTweets();
        $this->view->total_seguindo = $usuario->getTotalSeguindo();
        $this->view->total_seguidores = $usuario->getTotalSeguidores();

        $this->view->usuarios = $usuarios; // cria o atributo usuarios e coloca o array que está na variavel $usuarios dentro para que seja criados os cards com cada usuario retornado a partir de um foreach com base no atributo usuarios
        // provavelmente não precisaria disso, pois a variavel $usuarios que contem o array já está no contexto da página quemSeguir, então é só fazer um foreach com a variavel $usuarios ao invés do atributo usuarios 

        $this->view->tema = $usuario->tema();

        $this->render('quemSeguir'); // renderiza a página quemSeguir

    }

    public function acao() { // action que será ativado quando o usuario clicar no botão de seguir ou deixar de seguir

        $this->validaAutenticacao(); // metodo para verificar se o usuario está realmente logado

        // o $_GET vai ser enviado por parametro quando o usuario clicar em seguir ou deixar de seguir, depedendo do botão que clicar o get vira com um paramtro diferente
        $acao = isset($_GET['acao']) ? $_GET['acao'] : ''; // criando a variavel acao e passando para ela o valor do $_GET['acao'] caso ele exista, se não existir, passa um valor vazio
        $id_usuario_seguindo = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : ''; // criando a variavel id_usuario_seguindo e passando para ela o valor do $_GET['id_usuario'] caso ele exista, se não existir, passa um valor vazio

        $usuario = Container::getModel('Usuario'); // estanciando o obj usuario
        $usuario->__set('id', $_SESSION['id']); // setando o id do obj Usuario com o valor do session que contém o id do usuario logado

        if ($acao == 'seguir') { // se clicar no botão seguir ele entra no if, caso seja deixar de seguir entra no else if
            $usuario->seguirUsuario($id_usuario_seguindo); // ativa o metodo seguirUsuario do obj Usuario // passando por parametro o id do usuario que vai ser seguido
        } else if ($acao == 'deixar_de_seguir') {
            $usuario->deixarSeguirUsuario($id_usuario_seguindo); // ativa o metodo deixarSeguirUsuario do obj Usuario // passando por parametro o id do usuario que vai ser seguido
        }

        header('location: /quem_seguir');

    }

    public function removerTweet() { // action que será ativada após o usuario clicar no botão de remover

        $this->validaAutenticacao(); // metodo para verificar se o usuario está realmente logado

        $tweet = Container::getModel('Tweet'); // estanciando o obj Tweet

        $tweet->__set('id', $_POST['tweetValue']); // setando o id do obj Tweet com o valor do $_POST('tweetValue') que foi recebido ao clicar no botão de remover, ele contém o id do tweet
        $tweet->__set('id_usuario', $_SESSION['id']); // setando o id_usuatio do obj Tweet com o valor do session que contém o id do usuario logado
        
        $tweet->removerTweet(); // ativa o metodo removerTweet do ob Tweet

        header('location: /timeline');

    }

    public function alterarTema() {
        
        $this->validaAutenticacao();
        
        $usuario = Container::getModel('Usuario');
        $usuario->__set('id', $_SESSION['id']);
        
        if ($_GET['tema'] == 'claro') {
             $usuario->alterarTema();
        } else if ($_GET['tema'] == 'escuro') {
             $usuario->alterarTemaEscuro();
        }
        
        header('location: /timeline');
         
     }

}

?>