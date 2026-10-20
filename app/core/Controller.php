<?php
declare(strict_types=1);

/**
 * Classe mère de tous les contrôleurs.
 */
abstract class Controller
{
    /**
     * Affiche une vue dans le gabarit principal.
     * $this->render('trajets/index', ['trajets' => $trajets], 'Mes trajets');
     * Dans la vue, chaque clé de $data devient une variable ($trajets).
     */
    protected function render(string $view, array $data = [], string $title = ''): void
    {
        $viewFile = ROOT . '/app/views/' . $view . '.php';
        if (!is_file($viewFile)) {
            throw new RuntimeException("Vue introuvable : $view");
        }

        extract($data, EXTR_SKIP);
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        $pageTitle = $title;
        require ROOT . '/app/views/layout/main.php';
    }

    protected function redirect(string $path): never
    {
        header('Location: ' . url($path));
        exit;
    }

    /** Arrête la requête avec une page d'erreur (403, 404…). */
    protected function abort(int $code, string $message = ''): never
    {
        (new ErrorController())->show($code, $message);
        exit;
    }

    /** Valeur POST nettoyée des espaces (toujours une chaîne). */
    protected function input(string $key, string $default = ''): string
    {
        $value = $_POST[$key] ?? $default;
        return is_string($value) ? trim($value) : $default;
    }

    /** Valeur GET nettoyée (pour les filtres de recherche). */
    protected function query(string $key, string $default = ''): string
    {
        $value = $_GET[$key] ?? $default;
        return is_string($value) ? trim($value) : $default;
    }

    /**
     * Écran temporaire « à construire » : permet de naviguer dans l'appli
     * avant que chaque module soit développé. À supprimer au fur et à mesure.
     */
    protected function todo(string $etudiant, string $ecran, string $consigne): void
    {
        $this->render('partials/todo', compact('etudiant', 'ecran', 'consigne'), $ecran);
    }
}
