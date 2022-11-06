<?php

use Slim\Views\TwigExtension;

require __DIR__ . '/vendor/autoload.php';

$app = new Slim\App([
    'settings' => [
        'displayErrorDetails' => true,
        'db' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'database' => 'learn_slim',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
        ]
    ]
]);

$container = $app->getContainer();


// Pakai Templating Twig in container VIEW
$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig( __DIR__ . '/templates', [
        'cache' => false
    ]);

    // Instantiate and add Slim specific extension
    $router = $container->get('router');
    $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));

    return $view;
};

// Contaiener untuk Database
// $container['db'] = function(){
//     return new PDO('mysql:host=localhost; dbname=learn_slim', 'root', '');
// };

// Container untuk eloquent
$container['db'] = function($container){
    $capsule = new \Illuminate\Database\Capsule\Manager;
    $capsule->addConnection($container['settings']['db']);
    $capsule->setAsGlobal();
    $capsule->bootEloquent();
    return $capsule;
};

$container['notFoundHandler'] = function($container){
    return function($request, $response) use ($container){
        // return $container['response']
        // ->withStatus(404)
        // ->withHeader('Content-Type', 'text/html')
        // ->write('<h1>Halaman yang ada cari tidak di temukan</h1>');

        return $container->view->render($response, '404.twig');
    };
};


// $container['hello'] = function(){
//     echo 'hello drian';
// };

$app->get('/', function($request, $response){
    // return 'Hallo semuanya';
    // die(var_dump($request));
    // die(var_dump($request->getHeaders()));
    // return $request->getMethod();
    // return $request->getHeaders();

    // die(var_dump($response));

    // $data = array(
    //     'name' => 'Drian',
    //     'age' => 17
    // );

    // return $response->withJson($data, 200);

    // $this->hello;

    return $this->view->render($response, 'home.twig');
});

// parameter bisa pakai /{param} atau [/{param}] jika ingin opsional

$app->get('/forum[/{id}]', function($request, $response, $args){
    // die(var_dump($args));

    // die(var_dump($this->db->table('forum')->where('id', 1)->get()));

    // return $args['title'];

    $datas = $this->db->table('forum')->get();

    
    if(empty($args)){
        return $this->view->render($response, 'forum.twig', [
            'forum' => $datas
        ]);
    }else{
        $data = $this->db->table('forum')->where('id', $args['id'])->get();

        return $this->view->render($response, 'tanya.twig', [
            'tanya' => $data
        ]);
    }

    // $datas = $this->db->query("SELECT * FROM forum")->fetchAll(PDO::FETCH_OBJ);

    // die(var_dump($datas));
    

    // return $this->view->render($response, 'forum.twig', [
    //     'forum' => $datas
    // ]);


})->setName('single');

$app->run();
?>
