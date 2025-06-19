<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\DatabaseService;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    private $pdo;

    public function __construct(DatabaseService $databaseService)
    {
        $this->pdo = $databaseService->getConnection();
    }

    #[Route('/login', name: 'login')]
    public function login(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $username = $request->request->get('username');
            $password = $request->request->get('password');

            if (!$username || !$password) {
                return $this->render('home/login.html.twig', [
                    'error' => 'Vul gebruikersnaam en wachtwoord in.'
                ]);
            }

            // Gebruik juiste kolomnaam: username (niet naam)
            $stmt = $this->pdo->prepare('SELECT * FROM user WHERE naam = ?');
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // ✅ Start sessie en sla gebruiker op
                $session = $request->getSession();
                $session->start(); // meestal niet nodig, maar je kunt dit forceren
                $session->set('user', [
                    'id' => $user['id'],
                    'username' => $user['naam'],
                ]);

                return $this->redirectToRoute('home');
            }

            $error = $user ? 'Ongeldig wachtwoord.' : 'Gebruiker niet gevonden.';
            return $this->render('home/login.html.twig', ['error' => $error]);
        }

        return $this->render('home/login.html.twig');
    }
}
