<?php
declare(strict_types=1);

/**
 * Routeur unique de l'application.
 *
 * Déclaration (dans routes/*.php) :
 *   $router->get('/trajets/{id}', [TrajetController::class, 'show'], ['auth']);
 *   $router->post('/trajets', [TrajetController::class, 'store'], ['auth']);
 *
 * - {param} capture un entier, transmis en argument (int) à la méthode du contrôleur.
 * - Middlewares disponibles : 'auth' (connecté), 'guest' (non connecté), 'admin' (rôle admin).
 * - Toute requête POST est vérifiée contre le jeton CSRF.
 */
final class Router
{
    private array $routes = [];

    public function get(string $path, array $action, array $middleware = []): void
    {
        $this->add('GET', $path, $action, $middleware);
    }

    public function post(string $path, array $action, array $middleware = []): void
    {
        $this->add('POST', $path, $action, $middleware);
    }

    private function add(string $method, string $path, array $action, array $middleware): void
    {
        $path = '/' . trim($path, '/');
        $regex = preg_replace('#\{([a-zA-Z_]+)\}#', '(?P<$1>\d+)', $path);

        $this->routes[] = [
            'method'     => $method,
            'pattern'    => '#^' . $regex . '$#',
            'action'     => $action,
            'middleware' => $middleware,
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = rawurldecode(parse_url($uri, PHP_URL_PATH) ?? '/');

        // Retire le préfixe si l'app est installée dans un sous-dossier
        $base = base_path();
        if ($base !== '' && str_starts_with($path, $base)) {
            $path = substr($path, strlen($base));
        }
        $path = '/' . trim($path, '/');

        $methodNotAllowed = false;

        foreach ($this->routes as $route) {
            if (!preg_match($route['pattern'], $path, $matches)) {
                continue;
            }
            if ($route['method'] !== $method) {
                $methodNotAllowed = true;
                continue;
            }

            if ($method === 'POST' && !Csrf::isValid($_POST['_csrf'] ?? null)) {
                $this->abort(419, 'Le formulaire a expiré. Rechargez la page et réessayez.');
            }

            $this->runMiddleware($route['middleware']);

            $params = array_map('intval', array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY));
            [$class, $methodName] = $route['action'];
            (new $class())->$methodName(...array_values($params));
            return;
        }

        $methodNotAllowed
            ? $this->abort(405, 'Méthode non autorisée.')
            : $this->abort(404, 'Cette page n\'existe pas.');
    }

    private function runMiddleware(array $middleware): void
    {
        foreach ($middleware as $name) {
            switch ($name) {
                case 'auth':
                    if (!Auth::check()) {
                        Session::flash('info', 'Connectez-vous pour accéder à cette page.');
                        header('Location: ' . url('/connexion'));
                        exit;
                    }
                    break;
                case 'guest':
                    if (Auth::check()) {
                        header('Location: ' . url('/'));
                        exit;
                    }
                    break;
                case 'admin':
                    if (!Auth::isAdmin()) {
                        $this->abort(403, 'Accès réservé à l\'administration.');
                    }
                    break;
                default:
                    throw new InvalidArgumentException("Middleware inconnu : $name");
            }
        }
    }

    private function abort(int $code, string $message): never
    {
        (new ErrorController())->show($code, $message);
        exit;
    }
}
