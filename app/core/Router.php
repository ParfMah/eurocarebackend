<?php
/**
 * =====================================================
 * EUROCARE HUMANITAIRE - Routeur URL
 * =====================================================
 * Fichier : app/core/Router.php
 * Description : Système de routage URL propre.
 *               Parse l'URL, dispatche vers le bon
 *               contrôleur et la bonne méthode.
 *               Supporte les paramètres dynamiques.
 * =====================================================
 */

defined('BASEPATH') or die('Accès direct interdit.');

class Router
{
    /** @var array Routes enregistrées */
    private array $routes = [];

    /** @var array Middlewares globaux */
    private array $middlewares = [];

    // =====================================================
    // ENREGISTREMENT DES ROUTES
    // =====================================================

    /**
     * Enregistre une route GET.
     *
     * @param  string          $path       Chemin URL (ex: /blog/{slug})
     * @param  string|callable $handler    Contrôleur@méthode ou callable
     * @param  array           $middleware Middlewares à appliquer
     * @return self
     */
    public function get(string $path, string|callable $handler, array $middleware = []): self
    {
        return $this->addRoute('GET', $path, $handler, $middleware);
    }

    /**
     * Enregistre une route POST.
     */
    public function post(string $path, string|callable $handler, array $middleware = []): self
    {
        return $this->addRoute('POST', $path, $handler, $middleware);
    }

    /**
     * Enregistre une route pour GET ET POST.
     */
    public function any(string $path, string|callable $handler, array $middleware = []): self
    {
        $this->addRoute('GET',  $path, $handler, $middleware);
        $this->addRoute('POST', $path, $handler, $middleware);
        return $this;
    }

    /**
     * Ajoute une route dans le registre.
     */
    private function addRoute(
        string         $method,
        string         $path,
        string|callable $handler,
        array          $middleware
    ): self {
        // Conversion des paramètres dynamiques {param} en regex
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '([^/]+)', $path);
        $pattern = '#^' . $pattern . '$#';

        // Extraction des noms de paramètres dynamiques
        preg_match_all('/\{([a-zA-Z_]+)\}/', $path, $paramNames);

        $this->routes[] = [
            'method'      => $method,
            'path'        => $path,
            'pattern'     => $pattern,
            'handler'     => $handler,
            'middleware'  => $middleware,
            'paramNames'  => $paramNames[1] ?? [],
        ];

        return $this;
    }

    // =====================================================
    // RÉSOLUTION ET DISPATCH
    // =====================================================

    /**
     * Résout l'URL actuelle et dispatche vers le bon contrôleur.
     * Point d'entrée principal du routeur.
     */
    public function dispatch(): void
    {
        // Récupération de l'URI et de la méthode HTTP
        $requestUri    = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        // Nettoyage de l'URI
        $uri = $this->sanitizeUri($requestUri);

        // Recherche d'une route correspondante
        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }

            if (preg_match($route['pattern'], $uri, $matches)) {
                array_shift($matches); // Supprimer le match global

                // Reconstruction des paramètres dynamiques
                $params = [];
                foreach ($route['paramNames'] as $i => $name) {
                    $params[$name] = $matches[$i] ?? '';
                }

                // Exécution des middlewares
                if (!$this->runMiddlewares($route['middleware'])) {
                    return; // Middleware a bloqué la requête
                }

                // Dispatch vers le handler
                $this->callHandler($route['handler'], $params);
                return;
            }
        }

        // Aucune route trouvée → 404
        $this->handle404();
    }

    /**
     * Exécute les middlewares de la route.
     *
     * @param  array $middlewares Liste des middlewares
     * @return bool  true si tout passe, false si bloqué
     */
    private function runMiddlewares(array $middlewares): bool
    {
        foreach ($middlewares as $middleware) {
            $result = match($middleware) {
                'auth'       => $this->middlewareAuth(),
                'admin'      => $this->middlewareAdmin(),
                'moderateur' => $this->middlewareModerateur(),
                'donateur'   => $this->middlewareDonateur(),
                'beneficiaire'=> $this->middlewareBeneficiaire(),
                'partenaire' => $this->middlewarePartenaire(),
                'guest'      => $this->middlewareGuest(),
                default      => true,
            };

            if (!$result) {
                return false;
            }
        }
        return true;
    }

    /**
     * Appelle le handler (Contrôleur@méthode ou callable).
     *
     * @param  string|callable $handler
     * @param  array           $params  Paramètres URL
     */
    private function callHandler(string|callable $handler, array $params): void
    {
        if (is_callable($handler)) {
            call_user_func($handler, $params);
            return;
        }

        // Format attendu : "NomContrôleur@nomMéthode"
        [$controllerName, $method] = explode('@', $handler, 2);

        $controllerFile = APP_PATH . '/controllers/' . $controllerName . '.php';

        if (!file_exists($controllerFile)) {
            $this->handle404("Contrôleur introuvable : $controllerName");
            return;
        }

        require_once $controllerFile;

        if (!class_exists($controllerName)) {
            $this->handle500("Classe $controllerName introuvable dans $controllerFile");
            return;
        }

        $controller = new $controllerName();

        if (!method_exists($controller, $method)) {
            $this->handle404("Méthode $method introuvable dans $controllerName");
            return;
        }

        // Appel de la méthode avec les paramètres URL
        call_user_func_array([$controller, $method], [$params]);
    }

    // =====================================================
    // MIDDLEWARES
    // =====================================================

    /** Vérifie que l'utilisateur est connecté */
    private function middlewareAuth(): bool
    {
        if (!Auth::check()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            $this->redirect('/connexion');
            return false;
        }
        return true;
    }

    /** Vérifie le rôle administrateur */
    private function middlewareAdmin(): bool
    {
        if (!Auth::check()) {
            $this->redirect('/connexion');
            return false;
        }
        if (!Auth::hasRole([ROLE_ADMIN])) {
            $this->redirect('/tableau-de-bord?erreur=acces_refuse');
            return false;
        }
        return true;
    }

    /** Vérifie les rôles admin ou modérateur */
    private function middlewareModerateur(): bool
    {
        if (!Auth::check()) {
            $this->redirect('/connexion');
            return false;
        }
        if (!Auth::hasRole([ROLE_ADMIN, ROLE_MODERATEUR])) {
            $this->redirect('/tableau-de-bord?erreur=acces_refuse');
            return false;
        }
        return true;
    }

    /** Vérifie le rôle donateur */
    private function middlewareDonateur(): bool
    {
        if (!Auth::check()) {
            $this->redirect('/connexion');
            return false;
        }
        if (!Auth::hasRole([ROLE_ADMIN, ROLE_MODERATEUR, ROLE_DONATEUR])) {
            $this->redirect('/tableau-de-bord?erreur=acces_refuse');
            return false;
        }
        return true;
    }

    /** Vérifie le rôle bénéficiaire */
    private function middlewareBeneficiaire(): bool
    {
        if (!Auth::check()) {
            $this->redirect('/connexion');
            return false;
        }
        if (!Auth::hasRole([ROLE_ADMIN, ROLE_MODERATEUR, ROLE_BENEFICIAIRE])) {
            $this->redirect('/tableau-de-bord?erreur=acces_refuse');
            return false;
        }
        return true;
    }

    /** Vérifie le rôle partenaire */
    private function middlewarePartenaire(): bool
    {
        if (!Auth::check()) {
            $this->redirect('/connexion');
            return false;
        }
        if (!Auth::hasRole([ROLE_ADMIN, ROLE_MODERATEUR, ROLE_PARTENAIRE])) {
            $this->redirect('/tableau-de-bord?erreur=acces_refuse');
            return false;
        }
        return true;
    }

    /** Vérifie que l'utilisateur N'est PAS connecté (pages guest) */
    private function middlewareGuest(): bool
    {
        if (Auth::check()) {
            $this->redirect('/tableau-de-bord');
            return false;
        }
        return true;
    }

    // =====================================================
    // GESTION DES ERREURS HTTP
    // =====================================================

    /** Affiche une page 404 */
    private function handle404(string $message = 'Page non trouvée'): void
    {
        http_response_code(404);
        $viewFile = VIEWS_PATH . '/errors/404.php';
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            echo '<h1>404 - ' . htmlspecialchars($message) . '</h1>';
        }
    }

    /** Affiche une page 500 */
    private function handle500(string $message = 'Erreur serveur'): void
    {
        http_response_code(500);
        if (APP_DEBUG) {
            echo '<h1>500 - Erreur interne</h1><p>' . htmlspecialchars($message) . '</p>';
        } else {
            $viewFile = VIEWS_PATH . '/errors/500.php';
            if (file_exists($viewFile)) require $viewFile;
        }
    }

    // =====================================================
    // UTILITAIRES
    // =====================================================

    /**
     * Nettoie et normalise l'URI de la requête.
     */
    private function sanitizeUri(string $uri): string
    {
        // Suppression du slash final (sauf pour '/')
        $uri = rtrim($uri, '/');
        if (empty($uri)) {
            $uri = '/';
        }
        // Suppression des double-slashes
        $uri = preg_replace('#/+#', '/', $uri);
        return $uri;
    }

    /**
     * Redirige vers une URL.
     */
    private function redirect(string $url): void
    {
        if (!headers_sent()) {
            header('Location: ' . BASE_URL . $url, true, 302);
        }
        exit;
    }

    /**
     * Retourne la liste de toutes les routes enregistrées (debug).
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }
}
