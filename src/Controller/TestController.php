<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;


class TestController{
    /**
     * @Route("/", name="index")
     */
    public function index(Environment $twig){
        $html= $twig->render('hello.html.twig',[
            'prenom' => 'lucie'
        ]);
        return new Response($html);
    }
    /**
     * @Route("/test/{age<\d+>?0}", name="test", methods={"GET","POST"}, schemes={"http","https"})
     */
    public function test(Request $request, $age){
        return new Response("vous avez $age ans");
    }
}