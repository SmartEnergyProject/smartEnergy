<?php

namespace App\Controller;
require_once realpath(__DIR__ . '/../../config/db.php');



use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LoginController extends AbstractController{
    public function login(): Response {
        $contents = $this->renderView('home/login.html.twig');
        if (!$contents) {
            throw new \Exception('Template not found');
        }
        return new Response($contents);
    }
}