<?php
namespace App\Controller;

use http\Env\Request;
use http\Env\Response;
use Symfony\Component\Routing\Annotation\Route;


class TestController{
    public function index(){
        dd("youpi");
    }
    /**
     * Class TestController
     * @package App\Controller
     * @Route("/test/{age<\d+>?0}", name="test", methods={"GET","POST"}, schemes={"http","https"})
     */
    public function test(Request $request, $age){
        return new Response("vous avez $age ans");
    }
}