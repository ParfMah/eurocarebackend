<?php
/**
 * =====================================================
 * EUROCARE HUMANITAIRE - Helpers Globaux
 * =====================================================
 * Fichier : app/core/Helpers.php
 * Description : Fonctions utilitaires réutilisables
 *   dans tout le projet (formatage, dates, pagination,
 *   notifications, emails, vues, redirections).
 * =====================================================
 */

defined('BASEPATH') or die('Accès direct interdit.');

class Helpers
{
    // =====================================================
    // RENDU DES VUES
    // =====================================================

    /**
     * Charge et affiche une vue PHP avec ses données.
     *
     * @param string $view   Chemin de la vue (ex: 'public/accueil')
     * @param array  $data   Variables à injecter dans la vue
     * @param string $layout Layout à utiliser ('main' | 'admin' | 'auth' | 'none')
     */
    public static function view(string $view, array $data = [], string $layout = 'main'): void
    {
        // Extraction des variables pour les rendre disponibles dans la vue
        extract($data, EXTR_SKIP);

        // Génération du contenu de la vue en buffer
        $viewFile = VIEWS_PATH . '/' . str_replace('.', '/', $view) . '.php';

        if (!file_exists($viewFile)) {
            http_response_code(500);
            die("Vue introuvable : $viewFile");
        }

        // Capture du contenu de la vue
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        if ($layout === 'none') {
            echo $content;
            return;
        }

        // Chargement du layout
        $layoutFile = VIEWS_PATH . '/layouts/' . $layout . '.php';
        if (!file_exists($layoutFile)) {
            echo $content; // Fallback sans layout
            return;
        }

        require $layoutFile;
    }

    /**
     * Retourne le contenu HTML d'une vue (sans l'afficher).
     */
    public static function renderView(string $view, array $data = []): string
    {
        extract($data, EXTR_SKIP);
        $viewFile = VIEWS_PATH . '/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($viewFile)) return '';
        ob_start();
        require $viewFile;
        return ob_get_clean();
    }

    // =====================================================
    // REDIRECTIONS
    // =====================================================

    /**
     * Redirige vers une URL et termine le script.
     *
     * @param string $url     URL de destination (relative ou absolue)
     * @param int    $code    Code HTTP (301 ou 302)
     */
    public static function redirect(string $url, int $code = 302): never
    {
        $fullUrl = str_starts_with($url, 'http') ? $url : BASE_URL . $url;
        if (!headers_sent()) {
            header('Location: ' . $fullUrl, true, $code);
        }
        exit;
    }

    /**
     * Redirige avec un message flash.
     */
    public static function redirectWithFlash(string $url, string $type, string $message, int $code = 302): never
    {
        Session::flash($type, $message);
        self::redirect($url, $code);
    }

    // =====================================================
    // FORMATAGE DES DONNÉES
    // =====================================================

    /**
     * Formate un montant en devise européenne.
     *
     * @param  float  $amount  Montant
     * @param  string $devise  Code devise (EUR, CHF, GBP, USD)
     * @param  bool   $symbol  Afficher le symbole devant
     * @return string
     */
    public static function formatAmount(float $amount, string $devise = 'EUR', bool $symbol = true): string
    {
        $symbols = ['EUR' => '€', 'CHF' => 'CHF ', 'GBP' => '£', 'USD' => '$'];
        $formatted = number_format($amount, 2, ',', ' ');

        if (!$symbol) return $formatted;

        $sym = $symbols[$devise] ?? $devise . ' ';
        return in_array($devise, ['CHF', 'USD', 'GBP']) ? $sym . $formatted : $formatted . ' ' . $sym;
    }

    /**
     * Formate une date en français.
     *
     * @param  string $date   Date MySQL (Y-m-d H:i:s ou Y-m-d)
     * @param  bool   $time   Inclure l'heure
     * @param  bool   $full   Format long (ex: "12 janvier 2024")
     * @return string
     */
    public static function formatDate(string $date, bool $time = false, bool $full = false): string
    {
        if (empty($date) || $date === '0000-00-00') return '—';

        $timestamp = strtotime($date);
        if (!$timestamp) return $date;

        if ($full) {
            $mois = [
                1=>'janvier',2=>'février',3=>'mars',4=>'avril',5=>'mai',6=>'juin',
                7=>'juillet',8=>'août',9=>'septembre',10=>'octobre',11=>'novembre',12=>'décembre'
            ];
            $formatted = date('j', $timestamp) . ' ' . $mois[(int)date('n', $timestamp)] . ' ' . date('Y', $timestamp);
        } else {
            $formatted = date('d/m/Y', $timestamp);
        }

        if ($time) {
            $formatted .= ' à ' . date('H:i', $timestamp);
        }

        return $formatted;
    }

    /**
     * Retourne une date relative (ex: "il y a 3 heures").
     */
    public static function timeAgo(string $date): string
    {
        $timestamp = strtotime($date);
        $diff      = time() - $timestamp;

        if ($diff < 60)        return "il y a quelques secondes";
        if ($diff < 3600)      return "il y a " . floor($diff / 60) . " min";
        if ($diff < 86400)     return "il y a " . floor($diff / 3600) . "h";
        if ($diff < 604800)    return "il y a " . floor($diff / 86400) . " jour(s)";
        if ($diff < 2592000)   return "il y a " . floor($diff / 604800) . " semaine(s)";
        if ($diff < 31536000)  return "il y a " . floor($diff / 2592000) . " mois";
        return "il y a " . floor($diff / 31536000) . " an(s)";
    }

    /**
     * Tronque un texte avec ellipsis.
     */
    public static function truncate(string $text, int $length = 150, string $suffix = '...'): string
    {
        $text = strip_tags($text);
        if (mb_strlen($text) <= $length) return $text;
        return mb_substr($text, 0, $length) . $suffix;
    }

    /**
     * Formate une taille de fichier en unité lisible.
     */
    public static function formatFileSize(int $bytes): string
    {
        if ($bytes < 1024)        return $bytes . ' o';
        if ($bytes < 1048576)     return round($bytes / 1024, 1) . ' Ko';
        if ($bytes < 1073741824)  return round($bytes / 1048576, 1) . ' Mo';
        return round($bytes / 1073741824, 1) . ' Go';
    }

    /**
     * Génère l'URL d'une photo de profil ou l'avatar par défaut.
     */
    public static function avatarUrl(?string $photo, string $name = ''): string
    {
        if ($photo && file_exists(UPLOAD_PATH . '/profils/' . $photo)) {
            return UPLOAD_URL . '/profils/' . Security::e($photo);
        }
        // Avatar généré avec les initiales
        $initials = '';
        if ($name) {
            $parts = explode(' ', trim($name));
            foreach ($parts as $part) {
                $initials .= mb_strtoupper(mb_substr($part, 0, 1));
                if (strlen($initials) >= 2) break;
            }
        }
        return BASE_URL . '/assets/images/avatar-default.svg?initials=' . urlencode($initials);
    }

    // =====================================================
    // PAGINATION
    // =====================================================

    /**
     * Calcule les données de pagination.
     *
     * @param  int    $total    Total d'éléments
     * @param  int    $page     Page actuelle (1-based)
     * @param  int    $perPage  Éléments par page
     * @return array
     */
    public static function paginate(int $total, int $page = 1, int $perPage = ITEMS_PER_PAGE): array
    {
        $page     = max(1, $page);
        $pages    = max(1, (int)ceil($total / $perPage));
        $page     = min($page, $pages);
        $offset   = ($page - 1) * $perPage;

        return [
            'total'     => $total,
            'page'      => $page,
            'perPage'   => $perPage,
            'pages'     => $pages,
            'offset'    => $offset,
            'hasPrev'   => $page > 1,
            'hasNext'   => $page < $pages,
            'prevPage'  => $page - 1,
            'nextPage'  => $page + 1,
            'from'      => $total > 0 ? $offset + 1 : 0,
            'to'        => min($offset + $perPage, $total),
        ];
    }

    /**
     * Génère le HTML des boutons de pagination.
     *
     * @param  array  $pagination Retour de paginate()
     * @param  string $baseUrl    URL de base (sans ?page=)
     * @return string HTML
     */
    public static function paginationHtml(array $pagination, string $baseUrl): string
    {
        if ($pagination['pages'] <= 1) return '';

        $html    = '<nav class="pagination-nav" aria-label="Pagination"><ul class="pagination">';
        $current = $pagination['page'];
        $total   = $pagination['pages'];

        // Bouton précédent
        if ($pagination['hasPrev']) {
            $html .= '<li><a href="' . $baseUrl . '?page=' . $pagination['prevPage'] . '" class="page-btn prev" aria-label="Page précédente">‹</a></li>';
        }

        // Pages
        for ($i = 1; $i <= $total; $i++) {
            if ($i === 1 || $i === $total || abs($i - $current) <= 2) {
                $active = ($i === $current) ? ' active' : '';
                $html  .= "<li><a href=\"{$baseUrl}?page={$i}\" class=\"page-btn{$active}\">{$i}</a></li>";
            } elseif (abs($i - $current) === 3) {
                $html .= '<li><span class="page-dots">…</span></li>';
            }
        }

        // Bouton suivant
        if ($pagination['hasNext']) {
            $html .= '<li><a href="' . $baseUrl . '?page=' . $pagination['nextPage'] . '" class="page-btn next" aria-label="Page suivante">›</a></li>';
        }

        $html .= '</ul></nav>';
        return $html;
    }

    // =====================================================
    // NOTIFICATIONS IN-APP
    // =====================================================

    /**
     * Crée une notification pour un utilisateur.
     *
     * @param int    $userId  ID du destinataire
     * @param string $titre   Titre court
     * @param string $message Message complet
     * @param string $type    Type (NOTIF_*)
     * @param string $lien    URL associée (optionnel)
     */
    public static function createNotification(
        int    $userId,
        string $titre,
        string $message,
        string $type = NOTIF_INFO,
        string $lien = ''
    ): void {
        try {
            Database::getInstance()->insert('notifications', [
                'user_id' => $userId,
                'titre'   => mb_substr($titre, 0, 255),
                'message' => $message,
                'type'    => $type,
                'lien'    => $lien,
                'lu'      => 0,
                'cree_le' => date('Y-m-d H:i:s'),
            ]);
        } catch (Throwable) {
            // Non bloquant si la création de notification échoue
        }
    }

    /**
     * Récupère les notifications non lues d'un utilisateur.
     */
    public static function getUnreadNotifications(int $userId, int $limit = 10): array
    {
        return Database::getInstance()->query(
            'SELECT * FROM notifications WHERE user_id = ? AND lu = 0 ORDER BY cree_le DESC LIMIT ?',
            [$userId, $limit]
        )->fetchAll();
    }

    /**
     * Compte les notifications non lues.
     */
    public static function countUnreadNotifications(int $userId): int
    {
        return (int)Database::getInstance()->query(
            'SELECT COUNT(*) FROM notifications WHERE user_id = ? AND lu = 0',
            [$userId]
        )->fetchColumn();
    }

    // =====================================================
    // PARAMÈTRES DU SITE
    // =====================================================

    /**
     * Récupère un paramètre du site depuis la table `parametres`.
     *
     * @param  string $key     Clé du paramètre
     * @param  mixed  $default Valeur par défaut
     * @return mixed
     */
    public static function getSetting(string $key, mixed $default = ''): mixed
    {
        static $cache = [];

        if (isset($cache[$key])) {
            return $cache[$key];
        }

        try {
            $row = Database::getInstance()->findOne('parametres', ['cle' => $key], 'valeur, type');
            if ($row) {
                $value = match($row['type']) {
                    'nombre'  => is_numeric($row['valeur']) ? (float)$row['valeur'] : $default,
                    'booleen' => (bool)(int)$row['valeur'],
                    'json'    => json_decode($row['valeur'] ?? '', true) ?? $default,
                    default   => $row['valeur'] ?? $default,
                };
                $cache[$key] = $value;
                return $value;
            }
        } catch (Throwable) {}

        return $default;
    }

    /**
     * Récupère tous les paramètres d'un groupe.
     */
    public static function getSettingGroup(string $groupe): array
    {
        try {
            $rows = Database::getInstance()->findAll('parametres', ['groupe' => $groupe], 'cle, valeur, type');
            $settings = [];
            foreach ($rows as $row) {
                $settings[$row['cle']] = match($row['type']) {
                    'nombre'  => is_numeric($row['valeur']) ? (float)$row['valeur'] : $row['valeur'],
                    'booleen' => (bool)(int)$row['valeur'],
                    'json'    => json_decode($row['valeur'] ?? '', true),
                    default   => $row['valeur'],
                };
            }
            return $settings;
        } catch (Throwable) {
            return [];
        }
    }

    // =====================================================
    // RÉPONSES JSON (pour les requêtes AJAX)
    // =====================================================

    /**
     * Envoie une réponse JSON et termine le script.
     *
     * @param array $data    Données à encoder
     * @param int   $status  Code HTTP
     */
    public static function jsonResponse(array $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Réponse JSON de succès standardisée.
     */
    public static function jsonSuccess(string $message, array $data = [], int $status = 200): never
    {
        self::jsonResponse(['success' => true, 'message' => $message, 'data' => $data], $status);
    }

    /**
     * Réponse JSON d'erreur standardisée.
     */
    public static function jsonError(string $message, array $errors = [], int $status = 400): never
    {
        self::jsonResponse(['success' => false, 'message' => $message, 'errors' => $errors], $status);
    }

    // =====================================================
    // STATISTIQUES GLOBALES (pour la page transparence)
    // =====================================================

    /**
     * Calcule les statistiques globales de la plateforme.
     * Résultats mis en cache 10 minutes pour les performances.
     */
    public static function getGlobalStats(): array
    {
        $cacheKey  = 'global_stats';
        $cacheFile = (defined('ROOT_PATH') ? ROOT_PATH : sys_get_temp_dir())
            . '/storage/cache/' . $cacheKey . '.json';

        // Cache valide pendant 10 minutes
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < 600) {
            $cached = json_decode(file_get_contents($cacheFile), true);
            if ($cached) return $cached;
        }

        try {
            $db = Database::getInstance();

            $stats = [
                'total_dons'          => (float)$db->query("SELECT COALESCE(SUM(montant),0) FROM dons WHERE statut='valide'")->fetchColumn(),
                'nombre_dons'         => (int)$db->query("SELECT COUNT(*) FROM dons WHERE statut='valide'")->fetchColumn(),
                'nombre_beneficiaires'=> (int)$db->query("SELECT COUNT(*) FROM beneficiaires_profils WHERE statut_dossier='aide'")->fetchColumn(),
                'nombre_partenaires'  => (int)$db->query("SELECT COUNT(*) FROM partenaires_profils WHERE statut='valide'")->fetchColumn(),
                'total_aide_accordee' => (float)$db->query("SELECT COALESCE(SUM(montant),0) FROM aides_octroyees WHERE statut='complete'")->fetchColumn(),
                'projets_actifs'      => (int)$db->query("SELECT COUNT(*) FROM projets WHERE statut='actif'")->fetchColumn(),
                'donateurs_actifs'    => (int)$db->query("SELECT COUNT(*) FROM users WHERE role='donateur' AND statut='actif'")->fetchColumn(),
                'articles_publies'    => (int)$db->query("SELECT COUNT(*) FROM articles WHERE statut='publie'")->fetchColumn(),
                'taux_redistribution' => 92, // % configurable en paramètres
                'annee_fondation'     => 2010,
                'generated_at'        => date('Y-m-d H:i:s'),
            ];

            // Calcul du taux de redistribution réel
            if ($stats['total_dons'] > 0 && $stats['total_aide_accordee'] > 0) {
                $stats['taux_redistribution'] = round(($stats['total_aide_accordee'] / $stats['total_dons']) * 100, 1);
            }

            // Sauvegarde en cache
            $cacheDir = dirname($cacheFile);
            if (!is_dir($cacheDir)) @mkdir($cacheDir, 0755, true);
            @file_put_contents($cacheFile, json_encode($stats));

            return $stats;

        } catch (Throwable) {
            return [
                'total_dons' => 0, 'nombre_dons' => 0, 'nombre_beneficiaires' => 0,
                'nombre_partenaires' => 0, 'total_aide_accordee' => 0,
                'projets_actifs' => 0, 'donateurs_actifs' => 0,
                'taux_redistribution' => 92, 'annee_fondation' => 2010,
            ];
        }
    }
}
