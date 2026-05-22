<?php
/**
 * =====================================================
 * EUROCARE HUMANITAIRE - Gestionnaire de Sessions
 * =====================================================
 * Fichier : app/core/Session.php
 * Description : Gestion sécurisée des sessions PHP.
 *               Flash messages, CSRF, régénération
 *               d'ID de session et protection avancée.
 * =====================================================
 */

defined('BASEPATH') or die('Accès direct interdit.');

class Session
{
    /** @var bool Session déjà démarrée */
    private static bool $started = false;

    // =====================================================
    // DÉMARRAGE SÉCURISÉ DE LA SESSION
    // =====================================================

    /**
     * Démarre la session avec des paramètres de sécurité stricts.
     * À appeler une seule fois au début de chaque requête.
     */
    public static function start(): void
    {
        if (self::$started || session_status() === PHP_SESSION_ACTIVE) {
            self::$started = true;
            return;
        }

        // Configuration sécurisée des cookies de session
        session_set_cookie_params([
            'lifetime' => SESSION_LIFETIME,
            'path'     => '/',
            'domain'   => '',
            'secure'   => SESSION_SECURE,
            'httponly' => SESSION_HTTPONLY,
            'samesite' => SESSION_SAMESITE,
        ]);

        // Nom de session personnalisé (évite la détection du moteur)
        session_name(SESSION_NAME);

        // Démarrage
        session_start();
        self::$started = true;

        // Régénération périodique de l'ID de session (anti-fixation)
        if (!isset($_SESSION['_last_regeneration'])) {
            $_SESSION['_last_regeneration'] = time();
        } elseif (time() - $_SESSION['_last_regeneration'] > 1800) {
            // Régénère l'ID toutes les 30 minutes
            self::regenerate();
        }

        // Vérification de cohérence du User-Agent (anti-hijacking basique)
        $currentAgent = md5($_SERVER['HTTP_USER_AGENT'] ?? '');
        if (isset($_SESSION['_user_agent'])) {
            if ($_SESSION['_user_agent'] !== $currentAgent) {
                // Agent changé → session suspecte, on détruit
                self::destroy();
                return;
            }
        } else {
            $_SESSION['_user_agent'] = $currentAgent;
        }
    }

    // =====================================================
    // GESTION DES VALEURS DE SESSION
    // =====================================================

    /**
     * Définit une valeur en session.
     *
     * @param string $key   Clé de session
     * @param mixed  $value Valeur à stocker
     */
    public static function set(string $key, mixed $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    /**
     * Récupère une valeur de session.
     *
     * @param  string $key     Clé de session
     * @param  mixed  $default Valeur par défaut si absente
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Vérifie l'existence d'une clé en session.
     */
    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    /**
     * Supprime une clé de session.
     */
    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    /**
     * Supprime plusieurs clés de session.
     *
     * @param string[] $keys
     */
    public static function removeMany(array $keys): void
    {
        foreach ($keys as $key) {
            self::remove($key);
        }
    }

    // =====================================================
    // FLASH MESSAGES (messages one-shot)
    // =====================================================

    /**
     * Définit un flash message (affiché une seule fois).
     *
     * @param string $type    Type : 'succes'|'erreur'|'info'|'attention'
     * @param string $message Le message à afficher
     */
    public static function flash(string $type, string $message): void
    {
        self::start();
        $_SESSION['_flash'][$type][] = $message;
    }

    /**
     * Récupère et supprime les flash messages d'un type.
     *
     * @param  string|null $type  Type ou null pour tous
     * @return array
     */
    public static function getFlash(?string $type = null): array
    {
        self::start();
        $messages = [];

        if ($type !== null) {
            $messages = $_SESSION['_flash'][$type] ?? [];
            unset($_SESSION['_flash'][$type]);
        } else {
            $messages = $_SESSION['_flash'] ?? [];
            unset($_SESSION['_flash']);
        }

        return $messages;
    }

    /**
     * Vérifie s'il y a des flash messages.
     */
    public static function hasFlash(?string $type = null): bool
    {
        self::start();
        if ($type !== null) {
            return !empty($_SESSION['_flash'][$type]);
        }
        return !empty($_SESSION['_flash']);
    }

    // =====================================================
    // CSRF - PROTECTION CONTRE LES REQUÊTES FORGÉES
    // =====================================================

    /**
     * Génère un token CSRF unique et le stocke en session.
     * Si un token valide existe déjà, le retourne sans en créer un nouveau.
     *
     * @return string Token CSRF
     */
    public static function generateCsrfToken(): string
    {
        self::start();

        // Vérifier si le token existant est encore valide
        if (
            isset($_SESSION[CSRF_TOKEN_NAME], $_SESSION['_csrf_time']) &&
            (time() - $_SESSION['_csrf_time']) < CSRF_LIFETIME
        ) {
            return $_SESSION[CSRF_TOKEN_NAME];
        }

        // Génération d'un nouveau token aléatoire sécurisé
        $token = bin2hex(random_bytes(TOKEN_LENGTH / 2));
        $_SESSION[CSRF_TOKEN_NAME] = $token;
        $_SESSION['_csrf_time']    = time();

        return $token;
    }

    /**
     * Valide un token CSRF soumis dans un formulaire.
     *
     * @param  string $token Token à vérifier
     * @return bool
     */
    public static function validateCsrfToken(string $token): bool
    {
        self::start();

        if (
            empty($_SESSION[CSRF_TOKEN_NAME]) ||
            empty($_SESSION['_csrf_time'])
        ) {
            return false;
        }

        // Vérification de l'expiration
        if ((time() - $_SESSION['_csrf_time']) > CSRF_LIFETIME) {
            unset($_SESSION[CSRF_TOKEN_NAME], $_SESSION['_csrf_time']);
            return false;
        }

        // Comparaison en temps constant (protection timing attacks)
        return hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
    }

    /**
     * Génère le champ HTML caché pour CSRF.
     *
     * @return string HTML du champ input
     */
    public static function csrfField(): string
    {
        $token = self::generateCsrfToken();
        return sprintf(
            '<input type="hidden" name="%s" value="%s">',
            CSRF_TOKEN_NAME,
            htmlspecialchars($token, ENT_QUOTES, 'UTF-8')
        );
    }

    // =====================================================
    // GESTION DE LA SESSION
    // =====================================================

    /**
     * Régénère l'ID de session (anti-fixation de session).
     * Préserve les données de session.
     */
    public static function regenerate(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true); // true = supprime l'ancien fichier session
            $_SESSION['_last_regeneration'] = time();
        }
    }

    /**
     * Détruit complètement la session (déconnexion).
     */
    public static function destroy(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            // Vide toutes les variables de session
            $_SESSION = [];

            // Supprime le cookie de session
            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params['path'],
                    $params['domain'],
                    $params['secure'],
                    $params['httponly']
                );
            }

            session_destroy();
        }

        self::$started = false;
    }

    /**
     * Retourne l'ID de session actuel.
     */
    public static function getId(): string
    {
        return session_id() ?: '';
    }

    /**
     * Retourne toutes les variables de session (debug uniquement).
     */
    public static function all(): array
    {
        self::start();
        return $_SESSION;
    }
}
