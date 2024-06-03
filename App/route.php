<?php

    namespace App;

    use MF\Init\Bootstrap;

    class route extends Bootstrap{

        protected function initRoutes() {

            $routes['home'] = array( // criando rota padrão que fará com que quando entrar na aplicação, seja redirecionado para o action index dentro de IndexController
                'route' => '/',
                'controller' => 'IndexController',
                'action' => 'index'
            );

            $routes['inscreverse'] = array( // criando rota inscreverse, para quando o usuario clicar no botão de registrar ele seja redirecionado para a action inscreverse do IndexController
                'route' => '/inscreverse',
                'controller' => 'IndexController',
                'action' => 'inscreverse'
            );

            $routes['registrar'] = array( // criando a rota registrar, para quando o usuario clicar do botão de envio do formulario na inscrição ele seja redicionado para o action registrar do indexController
                'route' => '/registrar',
                'controller' => 'IndexController',
                'action' => 'registrar'
            );

            $routes['autenticar'] = array( // criando a rota autenticar, para quando o usuario clicar do botão de entrar do formulario de login na index ele seja redicionado para o action autenticar do AuthController
                'route' => '/autenticar',
                'controller' => 'AuthController',
                'action' => 'autenticar'
            );

            $routes['timeline'] = array( // criando rota timeline, para quando o acesso do login for liberado ele seja redirecionado para o action timeline do AppController
                'route' => '/timeline',
                'controller' => 'AppController',
                'action' => 'timeline'
            );

            $routes['sair'] = array( // criando a rota sair, para quando o botão de sair for clicado ele seja redirecionado para o action sair do AuthController
                'route' => '/sair',
                'controller' => 'AuthController',
                'action' => 'sair'
            );

            $routes['tweet'] = array( // criando a rota tweet, para quando o botão de tweet for clicado ele seja redirecionado para o action tweet do AppController
                'route' => '/tweet',
                'controller' => 'AppController',
                'action' => 'tweet'
            );

            $routes['quem_seguir'] = array( // criando a rota quem_seguir, para quando o botão de procurar por pessoas conhecidas for clicado ele seja redirecionado para o action quem_seguir do AppController
                'route' => '/quem_seguir',
                'controller' => 'AppController',
                'action' => 'quem_seguir'
            );

            $routes['acao'] = array( // criando a rota acao, para quando o botão de seguir ou deixar de seguir for clicado ele seja redirecionado para a action acao no AppController 
                'route' => '/acao',
                'controller' => 'AppController',
                'action' => 'acao'
            );

            $routes['removerTweet'] = array( // criando a rota removerTweet, para quando o botão de remover for clicado ele seja redirecionado para a action removerTweet no AppController 
                'route' => '/removerTweet',
                'controller' => 'AppController',
                'action' => 'removerTweet'
            );

            $this->setRoutes($routes);
        }

    }

?>