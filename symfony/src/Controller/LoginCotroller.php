<?php

namespace App\Controller;
require_once realpath(__DIR__ . '/../../config/db.php');



use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LoginCotroller extends AbstractController{
    public function index(): Response {
        $contents = $this->renderView('home/index.html.twig');
        if (!$contents) {
            throw new \Exception('Template not found');
        }
        return new Response($contents);
    }
}