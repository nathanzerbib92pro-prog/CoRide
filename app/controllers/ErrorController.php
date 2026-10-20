<?php
declare(strict_types=1);

final class ErrorController extends Controller
{
    public function show(int $code, string $message = ''): void
    {
        http_response_code($code);
        $titles = [403 => 'Accès refusé', 404 => 'Page introuvable', 405 => 'Action impossible', 419 => 'Formulaire expiré'];
        $title = $titles[$code] ?? 'Erreur';
        $this->render('errors/error', compact('code', 'title', 'message'), $title);
    }
}
