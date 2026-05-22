<?php
/**
 * =====================================================
 * EUROCARE HUMANITAIRE - Classe Security
 * =====================================================
 * Fichier : app/core/Security.php
 * Description : Fonctions de sécurité globales :
 *   - Nettoyage des entrées (XSS)
 *   - Validation des données
 *   - Upload sécurisé
 *   - Génération de tokens
 *   - Détection IP
 * =====================================================
 */

defined('BASEPATH') or die('Accès direct interdit.');

class Security
{
    // =====================================================
    // NETTOYAGE ET PROTECTION XSS
    // =====================================================

    /**
     * Nettoie une chaîne pour affichage HTML sécurisé (anti-XSS).
     *
     * @param  mixed  $data   Donnée à nettoyer
     * @param  bool   $double Double-encodage ou non
     * @return string
     */
    public static function escape(mixed $data, bool $double = false): string
    {
        if ($data === null) return '';
        return htmlspecialchars((string)$data, ENT_QUOTES | ENT_HTML5, 'UTF-8', $double);
    }

    /**
     * Alias court de escape() pour les templates.
     */
    public static function e(mixed $data): string
    {
        return self::escape($data);
    }

    /**
     * Nettoie récursivement un tableau ou une chaîne.
     *
     * @param  mixed $data
     * @return mixed
     */
    public static function sanitize(mixed $data): mixed
    {
        if (is_array($data)) {
            return array_map([self::class, 'sanitize'], $data);
        }
        if (is_string($data)) {
            // Suppression des balises PHP et HTML dangereuses
            $data = strip_tags($data);
            // Suppression des caractères nuls
            $data = str_replace(chr(0), '', $data);
            // Normalisation des espaces
            $data = trim($data);
            return $data;
        }
        return $data;
    }

    /**
     * Nettoie une entrée POST ou GET spécifique.
     *
     * @param  string $key     Nom du champ
     * @param  string $source  'post' | 'get'
     * @param  mixed  $default Valeur par défaut
     * @return mixed
     */
    public static function input(string $key, string $source = 'post', mixed $default = ''): mixed
    {
        $data = match(strtolower($source)) {
            'get'  => $_GET[$key]  ?? $default,
            'post' => $_POST[$key] ?? $default,
            default => $default,
        };

        return is_string($data) ? trim(self::sanitize($data)) : $data;
    }

    /**
     * Nettoie une valeur HTML riche (pour éditeur WYSIWYG).
     * Autorise certaines balises sûres seulement.
     *
     * @param  string $html
     * @return string
     */
    public static function sanitizeHtml(string $html): string
    {
        // Liste blanche de balises autorisées
        $allowed = '<p><br><strong><em><u><h2><h3><h4><ul><ol><li><a><blockquote><img><table><thead><tbody><tr><th><td>';
        $clean   = strip_tags($html, $allowed);

        // Suppression des attributs dangereux (onclick, onerror, javascript:)
        $clean = preg_replace('/\s*on\w+\s*=\s*"[^"]*"/i',   '', $clean);
        $clean = preg_replace('/\s*on\w+\s*=\s*\'[^\']*\'/i', '', $clean);
        $clean = preg_replace('/javascript\s*:/i',             '', $clean);
        $clean = preg_replace('/vbscript\s*:/i',               '', $clean);
        $clean = preg_replace('/data\s*:/i',                   '', $clean);

        return $clean;
    }

    // =====================================================
    // VALIDATION DES DONNÉES
    // =====================================================

    /**
     * Valide une adresse email.
     *
     * @param  string $email
     * @return bool
     */
    public static function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false
            && mb_strlen($email) <= 255;
    }

    /**
     * Valide un mot de passe selon les règles de sécurité.
     *
     * @param  string $password
     * @return array ['valid' => bool, 'errors' => string[]]
     */
    public static function validatePassword(string $password): array
    {
        $errors = [];

        if (mb_strlen($password) < PASSWORD_MIN_LENGTH) {
            $errors[] = "Le mot de passe doit contenir au moins " . PASSWORD_MIN_LENGTH . " caractères.";
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins une majuscule.";
        }
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins une minuscule.";
        }
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins un chiffre.";
        }
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins un caractère spécial (!@#\$%...).";
        }

        return ['valid' => empty($errors), 'errors' => $errors];
    }

    /**
     * Valide un numéro de téléphone (format international).
     */
    public static function validatePhone(string $phone): bool
    {
        return preg_match('/^\+?[0-9\s\-\(\)]{7,20}$/', $phone) === 1;
    }

    /**
     * Valide une URL.
     */
    public static function validateUrl(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Valide un montant financier positif.
     */
    public static function validateAmount(mixed $amount, float $min = 0.01): bool
    {
        $amount = filter_var($amount, FILTER_VALIDATE_FLOAT);
        return $amount !== false && $amount >= $min;
    }

    /**
     * Valide une date au format Y-m-d.
     */
    public static function validateDate(string $date, string $format = 'Y-m-d'): bool
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    /**
     * Valide qu'une valeur est dans une liste autorisée (enum).
     *
     * @param  mixed $value
     * @param  array $allowed
     * @return bool
     */
    public static function validateEnum(mixed $value, array $allowed): bool
    {
        return in_array($value, $allowed, true);
    }

    /**
     * Valide et filtre un entier.
     */
    public static function validateInt(mixed $value, int $min = 0, int $max = PHP_INT_MAX): int|false
    {
        $int = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => $min, 'max_range' => $max]]);
        return $int;
    }

    // =====================================================
    // UPLOAD SÉCURISÉ
    // =====================================================

    /**
     * Traite et valide un fichier uploadé de manière sécurisée.
     *
     * @param  string $inputName    Nom du champ input file
     * @param  string $subfolder    Sous-dossier de destination
     * @param  array  $allowedTypes Types MIME autorisés
     * @param  int    $maxSize      Taille max en octets
     * @return array ['success' => bool, 'path' => string, 'filename' => string, 'error' => string]
     */
    public static function handleUpload(
        string $inputName,
        string $subfolder   = 'documents',
        array  $allowedTypes = [],
        int    $maxSize      = MAX_UPLOAD_SIZE
    ): array {
        if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] === UPLOAD_ERR_NO_FILE) {
            return ['success' => false, 'error' => 'Aucun fichier fourni.'];
        }

        $file = $_FILES[$inputName];

        // Vérification du code d'erreur PHP
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $phpErrors = [
                UPLOAD_ERR_INI_SIZE   => 'Fichier trop volumineux (limite serveur).',
                UPLOAD_ERR_FORM_SIZE  => 'Fichier trop volumineux (limite formulaire).',
                UPLOAD_ERR_PARTIAL    => 'Téléchargement partiel.',
                UPLOAD_ERR_NO_TMP_DIR => 'Dossier temporaire manquant.',
                UPLOAD_ERR_CANT_WRITE => 'Impossible d\'écrire sur le disque.',
                UPLOAD_ERR_EXTENSION  => 'Extension PHP bloquée.',
            ];
            return ['success' => false, 'error' => $phpErrors[$file['error']] ?? 'Erreur inconnue.'];
        }

        // Vérification que c'est un vrai upload (pas un fichier local)
        if (!is_uploaded_file($file['tmp_name'])) {
            return ['success' => false, 'error' => 'Fichier non valide.'];
        }

        // Vérification de la taille
        if ($file['size'] > $maxSize) {
            $maxMo = round($maxSize / 1024 / 1024, 1);
            return ['success' => false, 'error' => "Fichier trop volumineux. Maximum autorisé : {$maxMo} Mo."];
        }

        // Vérification du type MIME réel (pas celui déclaré par le navigateur)
        $finfo    = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if (!empty($allowedTypes) && !in_array($mimeType, $allowedTypes, true)) {
            return ['success' => false, 'error' => 'Type de fichier non autorisé.'];
        }

        // Vérification des images (intégrité)
        if (str_starts_with($mimeType, 'image/')) {
            $imgInfo = @getimagesize($file['tmp_name']);
            if ($imgInfo === false) {
                return ['success' => false, 'error' => 'Fichier image corrompu ou invalide.'];
            }
        }

        // Génération d'un nom de fichier sécurisé et unique
        $extension    = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $safeFilename = bin2hex(random_bytes(16)) . '_' . time() . '.' . $extension;

        // Création du dossier de destination si nécessaire
        $destDir = UPLOAD_PATH . '/' . $subfolder;
        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }

        // Déplacement du fichier vers sa destination finale
        $destPath = $destDir . '/' . $safeFilename;
        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            return ['success' => false, 'error' => 'Impossible de sauvegarder le fichier.'];
        }

        // Suppression des métadonnées EXIF pour les images (vie privée)
        if (in_array($mimeType, ['image/jpeg', 'image/jpg'], true) && function_exists('exif_read_data')) {
            self::stripExifData($destPath);
        }

        return [
            'success'   => true,
            'path'      => $destPath,
            'filename'  => $safeFilename,
            'subfolder' => $subfolder,
            'url'       => UPLOAD_URL . '/' . $subfolder . '/' . $safeFilename,
            'mime'      => $mimeType,
            'size'      => $file['size'],
            'original'  => $file['name'],
        ];
    }

    /**
     * Supprime les métadonnées EXIF d'une image JPEG.
     * Protection de la vie privée (pas de géolocalisation dans les photos).
     */
    private static function stripExifData(string $imagePath): void
    {
        try {
            if (!function_exists('imagecreatefromjpeg')) return;
            $img = @imagecreatefromjpeg($imagePath);
            if ($img) {
                imagejpeg($img, $imagePath, 90);
                imagedestroy($img);
            }
        } catch (Throwable) {
            // Non bloquant si la suppression EXIF échoue
        }
    }

    /**
     * Supprime un fichier uploadé de manière sécurisée.
     *
     * @param  string $filename Nom du fichier (sans chemin)
     * @param  string $subfolder Sous-dossier
     * @return bool
     */
    public static function deleteUpload(string $filename, string $subfolder): bool
    {
        // Sécurité : pas de traversée de répertoires
        $filename = basename($filename);
        $path     = UPLOAD_PATH . '/' . $subfolder . '/' . $filename;

        if (file_exists($path) && is_file($path)) {
            return unlink($path);
        }
        return false;
    }

    // =====================================================
    // GÉNÉRATION DE TOKENS SÉCURISÉS
    // =====================================================

    /**
     * Génère un token aléatoire sécurisé.
     *
     * @param  int  $length  Longueur du token en caractères (hex)
     * @return string
     */
    public static function generateToken(int $length = TOKEN_LENGTH): string
    {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Génère un UUID v4.
     */
    public static function generateUuid(): string
    {
        $data    = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // Version 4
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // Variante RFC 4122
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * Génère un numéro de dossier unique pour les bénéficiaires.
     * Format : EC-AAAA-XXXXX (ex: EC-2024-00042)
     */
    public static function generateDossierNumber(): string
    {
        $year = date('Y');
        $rand = strtoupper(bin2hex(random_bytes(3)));
        return "EC-{$year}-{$rand}";
    }

    // =====================================================
    // DÉTECTION D'IP ET PROTECTION CONTRE LE SPAM
    // =====================================================

    /**
     * Récupère l'adresse IP réelle du client.
     * Gère les proxies et load balancers.
     */
    public static function getIp(): string
    {
        $ipHeaders = [
            'HTTP_CF_CONNECTING_IP', // Cloudflare
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'HTTP_CLIENT_IP',
            'REMOTE_ADDR',
        ];

        foreach ($ipHeaders as $header) {
            if (!empty($_SERVER[$header])) {
                $ip = trim(explode(',', $_SERVER[$header])[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }

        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    /**
     * Hashage sécurisé d'un mot de passe avec bcrypt.
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);
    }

    /**
     * Vérifie si un re-hashage est nécessaire (si le coût change).
     */
    public static function needsRehash(string $hash): bool
    {
        return password_needs_rehash($hash, PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);
    }

    /**
     * Génère un slug URL sécurisé depuis un titre.
     *
     * @param  string $text   Texte source
     * @param  int    $maxLen Longueur max
     * @return string
     */
    public static function slugify(string $text, int $maxLen = 120): string
    {
        // Translittération des caractères accentués
        $text = transliterator_transliterate('Any-Latin; Latin-ASCII', $text) ?: $text;
        $text = mb_strtolower($text, 'UTF-8');
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
        $text = preg_replace('/[\s-]+/', '-', trim($text));
        return mb_substr($text, 0, $maxLen);
    }
}
