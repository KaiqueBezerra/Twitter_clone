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
		$total_registros_pagina = 10; // total de registros por página que será passado para o metodo getPorPagina como o limit
		$pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 1; // quando clicar no botão de pagina 1 ou 2 (por exemplo), ele passara por get a página que estamos // pagina=1 | pagina=2
        // se o valor for encontrado ele será passado para a $pagina, se não ele ficara com o valor 1 
		$deslocamento = ($pagina - 1) * $total_registros_pagina; // $deslocamento que será passado para o getPorPagina como o offset // a conta é simples vai pegar a página que está//
        //por exemplo a 1, depois vai fazer -1 q vai ser igual a 0 e depois * o total de registros por pagina que vai ser sempre 10, no caso vai ser zero, então o offset vai ser 0
        // logo ele vai começar a pegar 10 tweets a partir do 0 // se a pagina fosse 2, a conta daria 10, e ele pegaria 10 tweets a partir do tweet 10 

		$tweets = $tweet->getPorPagina($total_registros_pagina, $deslocamento); // passando os parametros para o metodo getPorPagina que vai retornar um array com 10 registros e colocando dentro de $tweets
		$total_tweets = $tweet->getTotalRegistros(); // usando o metodo que retorna o total de tweets do usuario e dos usuarios que ele segue e colocando dentro de $total_tweets
		$this->view->total_de_paginas = ceil($total_tweets['total'] / $total_registros_pagina); // dividindo o total de tweets pelo total de tweets por pagina que no caso é 10 para descobrir o total de páginas
        // por exemplo, se forem retornados 20 tweets ele será dividido por 10 e ficará 2 que será o total de paginas, o ceil serve para arrendondar, para o resultado nunca ser 2,1 por exemplo
        // o resultado dessa divisão será passado para $this->view->total_de_paginas para que seja usado na timeline
		$this->view->pagina_ativa = $pagina; // passando a $pagina que conterá a página atual para $this->view->pagina_ativa

		$this->view->tweets = $tweets; // passando os 10 tweets que foram retornados para $this->view->tweets

		$usuario = Container::getModel('Usuario');
		$usuario->__set('id', $_SESSION['id']);

		$this->view->info_usuario = $usuario->getInfoUsuarios(); // retorna o nome do usuario e passa para $this->view->info_usuario para que sejá usado na timeline para cruação dinamica do perfil
		$this->view->total_tweets = $usuario->getTotalTweets(); // retorna o total de tweets do usuario e passa para $this->view->total_tweets para que sejá usado na timeline para cruação dinamica do perfil
		$this->view->total_seguindo = $usuario->getTotalSeguindo(); // retorna o total de usuarios que ele segue e passa para $this->view->total_seguindo para que sejá usado na timeline para cruação dinamica do perfil
		$this->view->total_seguidores = $usuario->getTotalSeguidores(); // retorna o total de usuario que seguem ele e passa para $this->view->total_seguidores para que sejá usado na timeline para cruação dinamica do perfil

        $this->view->tema = $usuario->tema(); // coloca o metodo tema do obj usuario que retorna o tema do usuario logado dentro de $this->view->tema para que seja usado
        // na página timeline para modificação do estilo

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
        $this->view->info_usuario = $usuario->getInfoUsuarios(); // retorna o nome do usuario e passa para $this->view->info_usuario para que sejá usado na quemSeguir para cruação dinamica do perfil
		$this->view->total_tweets = $usuario->getTotalTweets(); // retorna o total de tweets do usuario e passa para $this->view->total_tweets para que sejá usado na quemSeguir para cruação dinamica do perfil
		$this->view->total_seguindo = $usuario->getTotalSeguindo(); // retorna o total de usuarios que ele segue e passa para $this->view->total_seguindo para que sejá usado na quemSeguir para cruação dinamica do perfil
		$this->view->total_seguidores = $usuario->getTotalSeguidores(); // retorna o total de usuario que seguem ele e passa para $this->view->total_seguidores para que sejá usado na quemSeguir para cruação dinamica do perfil

        $this->view->usuarios = $usuarios; // cria o atributo usuarios e coloca o array que está na variavel $usuarios dentro para que seja criados os cards com cada usuario retornado a partir de um foreach com base no atributo usuarios
        // provavelmente não precisaria disso, pois a variavel $usuarios que contem o array já está no contexto da página quemSeguir, então é só fazer um foreach com a variavel $usuarios ao invés do atributo usuarios 

        $this->view->tema = $usuario->tema(); // coloca o metodo tema do obj usuario que retorna o tema do usuario logado dentro de $this->view->tema para que seja usado
        // na página quemSeguir para modificação do estilo

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

    public function alterarTema() { // action ativada quando o usuario clicar no botão para alterar tema
        
        $this->validaAutenticacao();
        
        $usuario = Container::getModel('Usuario');
        $usuario->__set('id', $_SESSION['id']);
        
        if ($_GET['tema'] == 'claro') { // se o tema estiver no padrão altera para o escuro
             $usuario->alterarTema();
        } else if ($_GET['tema'] == 'escuro') { // se o tema estiver no escuro altera para o padrão
             $usuario->alterarTemaEscuro();
        }
        
        header('location: /timeline');
         
     }

}

?>