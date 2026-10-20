<?php
declare(strict_types=1);

/**
 * Inscription / connexion / déconnexion (SOCLE commun).
 */
final class AuthController extends Controller
{
    public function showLogin(): void
    {
        $this->render('auth/login', ['errors' => [], 'old' => []], 'Connexion');
    }

    public function login(): void
    {
        $email = $this->input('email');
        $password = $_POST['password'] ?? '';

        $user = Utilisateur::findByEmail($email);

        // Même message dans tous les cas : on ne révèle pas si l'email existe.
        if ($user === null || !is_string($password) || !$user->verifyPassword($password)) {
            $this->render('auth/login', [
                'errors' => ['global' => 'Email ou mot de passe incorrect.'],
                'old'    => ['email' => $email],
            ], 'Connexion');
            return;
        }

        if (!$user->isActif()) {
            $this->render('auth/login', [
                'errors' => ['global' => 'Ce compte a été désactivé. Contactez l\'administration.'],
                'old'    => ['email' => $email],
            ], 'Connexion');
            return;
        }

        $user->rehashIfNeeded($password);
        Auth::login($user);
        Session::flash('success', 'Bonjour ' . $user->getNom() . ' !');
        $this->redirect('/');
    }

    public function showRegister(): void
    {
        $this->render('auth/register', ['errors' => [], 'old' => []], 'Créer un compte');
    }

    public function register(): void
    {
        $nom = $this->input('nom');
        $email = $this->input('email');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['password_confirm'] ?? '';
        $errors = [];

        if (mb_strlen($nom) < 2 || mb_strlen($nom) > 100) {
            $errors['nom'] = 'Indiquez votre prénom et votre nom (2 à 100 caractères).';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Cette adresse email n\'est pas valide.';
        } elseif (Utilisateur::emailExists($email)) {
            $errors['email'] = 'Un compte existe déjà avec cet email.';
        }
        if (!is_string($password) || mb_strlen($password) < 8) {
            $errors['password'] = 'Le mot de passe doit contenir au moins 8 caractères.';
        } elseif ($password !== $confirm) {
            $errors['password_confirm'] = 'Les deux mots de passe ne correspondent pas.';
        }

        if ($errors) {
            $this->render('auth/register', [
                'errors' => $errors,
                'old'    => ['nom' => $nom, 'email' => $email],
            ], 'Créer un compte');
            return;
        }

        $user = Utilisateur::create($nom, $email, $password);
        Auth::login($user);
        Session::flash('success', 'Compte créé. Bienvenue sur CoRide !');
        $this->redirect('/');
    }

    public function logout(): void
    {
        Auth::logout();
        Session::start();
        Session::flash('info', 'Vous êtes déconnecté.');
        $this->redirect('/connexion');
    }
}
