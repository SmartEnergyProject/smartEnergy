<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\DatabaseService;

class RegisterController extends AbstractController
{
    private $pdo;

    public function __construct(DatabaseService $databaseService)
    {
        $this->pdo = $databaseService->getConnection();
    }

    public function register(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $username = $request->request->get('username');
            $password = $request->request->get('password');

            if (!$username || !$password) {
                return $this->render('home/register.html.twig', [
                    'error' => 'Vul gebruikersnaam en wachtwoord in.'
                ]);
            }

            // Zelfde queryvorm als login
            $stmt = $this->pdo->prepare('SELECT * FROM user WHERE naam = ?');
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user) {
                return $this->render('home/register.html.twig', [
                    'error' => 'Gebruikersnaam is al in gebruik.'
                ]);
            }

            // Hash wachtwoord, net zoals login het controleert met password_verify
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $klantnummer = uniqid();

            $stmt = $this->pdo->prepare('INSERT INTO user (naam, password, rol, klantnummer) VALUES (?, ?, ?, ?)');
            $stmt->execute([$username, $hashedPassword, 'bezoeker', $klantnummer]);

            // Zelfde sessie-opbouw als login
            $userId = $this->pdo->lastInsertId();
            $request->getSession()->set('user', [
                'id' => $userId,
                'username' => $username,
                'rol' => 'bezoeker',
                'klantnummer' => $klantnummer
            ]);

            return $this->redirectToRoute('home');
        }

        return $this->render('home/register.html.twig');
    }
}
