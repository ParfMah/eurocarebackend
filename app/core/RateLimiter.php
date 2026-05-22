<?php
/**
 * =====================================================
 * EUROCARE HUMANITAIRE - Sécurité avancée
 * =====================================================
 * Fichier : app/core/Security.php (extension)
 * Classe additionnelle : RateLimiter
 * Description : Limitation de taux par IP/action,
 *   protection anti-spam, détection d'attaques.
 * =====================================================
 */

defined('BASEPATH') or die('Accès direct interdit.');

/**
 * Limiteur de taux d'actions (Rate Limiter)
 * Basé sur fichiers JSON — pas de dépendance Redis/Memcache
 */
class RateLimiter
{
    /** @var string Dossier de stockage des compteurs */
    private static string $storageDir = '';

    /**
     * Initialise le dossier de stockage
     */
    private static function getStorageDir(): string
    {
        if (empty(self::$storageDir)) {
            self::$storageDir = (defined('ROOT_PATH') ? ROOT_PATH : sys_get_temp_dir())
                . '/storage/cache/ratelimit';
            if (!is_dir(self::$storageDir)) {
                @mkdir(self::$storageDir, 0755, true);
            }
        }
        return self::$storageDir;
    }

    /**
     * Vérifie et incrémente le compteur pour une action.
     * Bloque si la limite est dépassée dans la fenêtre temporelle.
     *
     * @param  string $action     Identifiant de l'action (ex: 'login', 'contact')
     * @param  string $key        Clé unique (IP, email, etc.)
     * @param  int    $maxAttempts Nombre max de tentatives
     * @param  int    $windowSeconds Fenêtre en secondes
     * @return array ['allowed' => bool, 'remaining' => int, 'retry_after' => int]
     */
    public static function check(
        string $action,
        string $key,
        int    $maxAttempts  = 10,
        int    $windowSeconds = 3600
    ): array {
        $dir      = self::getStorageDir();
        $filename = $dir . '/' . hash('sha256', $action . '_' . $key) . '.json';
        $now      = time();
        $data     = ['attempts' => [], 'blocked_until' => 0];

        // Charger l'existant
        if (file_exists($filename)) {
            $loaded = json_decode(file_get_contents($filename), true);
            if (is_array($loaded)) $data = $loaded;
        }

        // Vérifier si encore bloqué
        if ($data['blocked_until'] > $now) {
            return [
                'allowed'     => false,
                'remaining'   => 0,
                'retry_after' => $data['blocked_until'] - $now,
            ];
        }

        // Nettoyer les tentatives expirées
        $data['attempts'] = array_filter(
            $data['attempts'],
            fn($ts) => ($now - $ts) < $windowSeconds
        );

        $count = count($data['attempts']);

        if ($count >= $maxAttempts) {
            // Bloquer pendant la fenêtre restante
            $data['blocked_until'] = $now + $windowSeconds;
            file_put_contents($filename, json_encode($data), LOCK_EX);
            return [
                'allowed'     => false,
                'remaining'   => 0,
                'retry_after' => $windowSeconds,
            ];
        }

        // Enregistrer cette tentative
        $data['attempts'][] = $now;
        $data['blocked_until'] = 0;
        file_put_contents($filename, json_encode($data), LOCK_EX);

        return [
            'allowed'   => true,
            'remaining' => $maxAttempts - $count - 1,
            'retry_after' => 0,
        ];
    }

    /**
     * Réinitialise le compteur pour une action/clé.
     */
    public static function reset(string $action, string $key): void
    {
        $dir      = self::getStorageDir();
        $filename = $dir . '/' . hash('sha256', $action . '_' . $key) . '.json';
        if (file_exists($filename)) @unlink($filename);
    }

    /**
     * Nettoie les fichiers de rate limit expirés (cron)
     */
    public static function cleanup(): int
    {
        $dir   = self::getStorageDir();
        $count = 0;
        foreach (glob($dir . '/*.json') as $file) {
            if (filemtime($file) < time() - 86400) {
                @unlink($file);
                $count++;
            }
        }
        return $count;
    }
}

/**
 * Validateur de formulaires centralisé
 */
class Validator
{
    /** @var array Erreurs de validation */
    private array $errors = [];

    /** @var array Données validées */
    private array $data = [];

    /**
     * Valide les données d'entrée selon des règles.
     *
     * @param  array $input  Données brutes ($_POST, etc.)
     * @param  array $rules  ['champ' => 'required|email|min:2|max:255']
     * @return self
     */
    public function validate(array $input, array $rules): self
    {
        foreach ($rules as $field => $ruleString) {
            $value      = $input[$field] ?? null;
            $rulesList  = explode('|', $ruleString);
            $fieldLabel = ucfirst(str_replace('_', ' ', $field));

            foreach ($rulesList as $rule) {
                [$ruleName, $ruleParam] = array_pad(explode(':', $rule, 2), 2, null);

                $error = match($ruleName) {
                    'required' => (empty($value) && $value !== '0')
                        ? "{$fieldLabel} est requis."
                        : null,

                    'email' => !empty($value) && !Security::validateEmail($value)
                        ? "{$fieldLabel} doit être une adresse email valide."
                        : null,

                    'min' => !empty($value) && mb_strlen((string)$value) < (int)$ruleParam
                        ? "{$fieldLabel} doit contenir au moins {$ruleParam} caractère(s)."
                        : null,

                    'max' => !empty($value) && mb_strlen((string)$value) > (int)$ruleParam
                        ? "{$fieldLabel} ne peut pas dépasser {$ruleParam} caractère(s)."
                        : null,

                    'numeric' => !empty($value) && !is_numeric($value)
                        ? "{$fieldLabel} doit être un nombre."
                        : null,

                    'min_val' => !empty($value) && (float)$value < (float)$ruleParam
                        ? "{$fieldLabel} doit être au moins {$ruleParam}."
                        : null,

                    'in' => !empty($value) && !in_array($value, explode(',', $ruleParam ?? ''), true)
                        ? "{$fieldLabel} contient une valeur non autorisée."
                        : null,

                    'url' => !empty($value) && !Security::validateUrl($value)
                        ? "{$fieldLabel} doit être une URL valide."
                        : null,

                    'date' => !empty($value) && !Security::validateDate($value)
                        ? "{$fieldLabel} doit être une date valide (AAAA-MM-JJ)."
                        : null,

                    'confirmed' => isset($input[$field . '_confirm'])
                        && $value !== $input[$field . '_confirm']
                        ? "{$fieldLabel} et sa confirmation ne correspondent pas."
                        : null,

                    'password' => !empty($value) && !Security::validatePassword($value)['valid']
                        ? implode(' ', Security::validatePassword($value)['errors'])
                        : null,

                    default => null,
                };

                if ($error !== null) {
                    $this->errors[$field][] = $error;
                    break; // Une seule erreur par champ à la fois
                }
            }

            // Nettoyer et stocker la valeur validée
            if (!isset($this->errors[$field])) {
                $this->data[$field] = is_string($value) ? Security::sanitize(trim($value)) : $value;
            }
        }

        return $this;
    }

    /** Vérifie si la validation a réussi */
    public function passes(): bool { return empty($this->errors); }

    /** Vérifie si la validation a échoué */
    public function fails(): bool { return !empty($this->errors); }

    /** Retourne toutes les erreurs */
    public function errors(): array { return $this->errors; }

    /** Retourne la première erreur de tous les champs */
    public function firstError(): string
    {
        foreach ($this->errors as $fieldErrors) {
            return $fieldErrors[0] ?? '';
        }
        return '';
    }

    /** Retourne les erreurs sous forme de liste HTML */
    public function errorsHtml(): string
    {
        $html = '<ul style="margin:0;padding-left:20px">';
        foreach ($this->errors as $fieldErrors) {
            foreach ($fieldErrors as $err) {
                $html .= '<li>' . Security::e($err) . '</li>';
            }
        }
        return $html . '</ul>';
    }

    /** Retourne les données nettoyées et validées */
    public function validated(): array { return $this->data; }

    /** Retourne une valeur validée spécifique */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }
}

/**
 * Protection CSRF vérifiée via middleware (appel centralisé)
 */
class CsrfGuard
{
    /**
     * Valide le token CSRF d'une requête POST.
     * Redirige avec erreur si invalide.
     *
     * @param  string $redirectUrl URL de redirection en cas d'échec
     */
    public static function verify(string $redirectUrl = '/'): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $token = $_POST[CSRF_TOKEN_NAME] ?? '';

        if (!Session::validateCsrfToken($token)) {
            Logger::warning('csrf_echec', 'security', null,
                'Token CSRF invalide — IP: ' . Security::getIp());

            Helpers::redirectWithFlash($redirectUrl, 'erreur',
                'Session expirée ou requête invalide. Veuillez réessayer.');
        }
    }

    /**
     * Vérifie le CSRF et retourne bool (pour les API AJAX)
     */
    public static function verifyAjax(): bool
    {
        $token = $_POST[CSRF_TOKEN_NAME]
            ?? $_SERVER['HTTP_X_CSRF_TOKEN']
            ?? '';
        return Session::validateCsrfToken($token);
    }
}
