<?php
/**
 * =====================================================
 * EUROCARE HUMANITAIRE - Classe Logger (Audit)
 * =====================================================
 * Fichier : app/core/Logger.php
 * Description : Journalisation complète de toutes les
 *   actions importantes (journal_audit MySQL + fichiers).
 *   Traçabilité totale conforme aux exigences RGPD.
 * =====================================================
 */

defined('BASEPATH') or die('Accès direct interdit.');

class Logger
{
    // =====================================================
    // JOURNALISATION EN BASE DE DONNÉES
    // =====================================================

    /**
     * Enregistre une action dans le journal d'audit.
     *
     * @param string     $action          Code de l'action (constante ACTION_*)
     * @param string     $module          Module concerné (ex: 'users', 'dons')
     * @param int|null   $enregistrementId ID de l'enregistrement concerné
     * @param array|null $anciennesValeurs Valeurs avant modification
     * @param array|null $nouvellesValeurs Valeurs après modification
     * @param string     $severite        'info' | 'attention' | 'critique'
     * @param string     $details         Détails libres
     */
    public static function log(
        string  $action,
        string  $module          = '',
        ?int    $enregistrementId = null,
        ?array  $anciennesValeurs = null,
        ?array  $nouvellesValeurs = null,
        string  $severite        = 'info',
        string  $details         = ''
    ): void {
        try {
            $db = Database::getInstance();

            // Récupération de l'utilisateur connecté
            $userId = class_exists('Auth') ? Auth::id() : null;

            $data = [
                'user_id'           => $userId,
                'action'            => $action,
                'module'            => $module,
                'table_concernee'   => $module,
                'enregistrement_id' => $enregistrementId,
                'anciennes_valeurs' => $anciennesValeurs ? json_encode($anciennesValeurs, JSON_UNESCAPED_UNICODE) : null,
                'nouvelles_valeurs' => $nouvellesValeurs ? json_encode($nouvellesValeurs, JSON_UNESCAPED_UNICODE) : null,
                'ip'                => Security::getIp(),
                'user_agent'        => mb_substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500),
                'details'           => $details,
                'severite'          => $severite,
                'cree_le'           => date('Y-m-d H:i:s'),
            ];

            $db->insert('journal_audit', $data);

        } catch (Throwable $e) {
            // Si la BDD n'est pas disponible, écriture dans un fichier
            self::logToFile('ERREUR_DB_LOG', $action . ': ' . $e->getMessage());
        }
    }

    /**
     * Journalise une action critique avec niveau "critique".
     */
    public static function critical(
        string $action,
        string $module          = '',
        ?int   $enregistrementId = null,
        string $details         = ''
    ): void {
        self::log($action, $module, $enregistrementId, null, null, 'critique', $details);
    }

    /**
     * Journalise un avertissement.
     */
    public static function warning(
        string $action,
        string $module          = '',
        ?int   $enregistrementId = null,
        string $details         = ''
    ): void {
        self::log($action, $module, $enregistrementId, null, null, 'attention', $details);
    }

    // =====================================================
    // JOURNALISATION EN FICHIER (fallback + erreurs PHP)
    // =====================================================

    /**
     * Écrit une entrée dans le fichier de log système.
     *
     * @param string $level   Niveau : 'INFO' | 'WARNING' | 'ERROR' | 'CRITICAL'
     * @param string $message Message à journaliser
     * @param array  $context Données contextuelles
     */
    public static function logToFile(
        string $level,
        string $message,
        array  $context = []
    ): void {
        $logDir  = defined('ROOT_PATH') ? ROOT_PATH . '/storage/logs' : sys_get_temp_dir();
        $logFile = $logDir . '/app_' . date('Y-m-d') . '.log';

        // Création du dossier si nécessaire
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }

        $contextStr = !empty($context) ? ' | ' . json_encode($context, JSON_UNESCAPED_UNICODE) : '';
        $userId     = class_exists('Auth') ? (Auth::id() ?? 'guest') : 'system';
        $ip         = class_exists('Security') ? Security::getIp() : ($_SERVER['REMOTE_ADDR'] ?? '-');
        $uri        = $_SERVER['REQUEST_URI'] ?? 'CLI';

        $entry = sprintf(
            "[%s] [%s] [User:%s] [IP:%s] [%s] %s%s%s",
            date('Y-m-d H:i:s'),
            strtoupper($level),
            $userId,
            $ip,
            $uri,
            $message,
            $contextStr,
            PHP_EOL
        );

        @file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);
    }

    // =====================================================
    // RÉCUPÉRATION DES LOGS (admin)
    // =====================================================

    /**
     * Récupère les entrées du journal d'audit avec filtres et pagination.
     *
     * @param  array $filters  ['action','module','user_id','severite','date_debut','date_fin']
     * @param  int   $page     Numéro de page
     * @param  int   $perPage  Éléments par page
     * @return array ['data' => array, 'total' => int, 'pages' => int]
     */
    public static function getAuditLogs(array $filters = [], int $page = 1, int $perPage = 50): array
    {
        $db     = Database::getInstance();
        $where  = ['1=1'];
        $params = [];

        // Application des filtres
        if (!empty($filters['action'])) {
            $where[]  = 'ja.action LIKE ?';
            $params[] = '%' . $filters['action'] . '%';
        }
        if (!empty($filters['module'])) {
            $where[]  = 'ja.module = ?';
            $params[] = $filters['module'];
        }
        if (!empty($filters['user_id'])) {
            $where[]  = 'ja.user_id = ?';
            $params[] = (int)$filters['user_id'];
        }
        if (!empty($filters['severite'])) {
            $where[]  = 'ja.severite = ?';
            $params[] = $filters['severite'];
        }
        if (!empty($filters['date_debut'])) {
            $where[]  = 'ja.cree_le >= ?';
            $params[] = $filters['date_debut'] . ' 00:00:00';
        }
        if (!empty($filters['date_fin'])) {
            $where[]  = 'ja.cree_le <= ?';
            $params[] = $filters['date_fin'] . ' 23:59:59';
        }

        $whereStr = implode(' AND ', $where);
        $offset   = ($page - 1) * $perPage;

        // Comptage total
        $total = (int)$db->query(
            "SELECT COUNT(*) FROM journal_audit ja WHERE $whereStr",
            $params
        )->fetchColumn();

        // Données paginées
        $data = $db->query(
            "SELECT ja.*,
                    CONCAT(u.prenom, ' ', u.nom) AS user_nom,
                    u.role AS user_role
             FROM journal_audit ja
             LEFT JOIN users u ON u.id = ja.user_id
             WHERE $whereStr
             ORDER BY ja.cree_le DESC
             LIMIT ? OFFSET ?",
            array_merge($params, [$perPage, $offset])
        )->fetchAll();

        return [
            'data'    => $data,
            'total'   => $total,
            'pages'   => (int)ceil($total / $perPage),
            'page'    => $page,
            'perPage' => $perPage,
        ];
    }

    /**
     * Retourne les statistiques du journal d'audit.
     */
    public static function getAuditStats(): array
    {
        $db = Database::getInstance();

        return [
            'total_actions'       => (int)$db->query('SELECT COUNT(*) FROM journal_audit')->fetchColumn(),
            'actions_today'       => (int)$db->query("SELECT COUNT(*) FROM journal_audit WHERE DATE(cree_le) = CURDATE()")->fetchColumn(),
            'actions_critiques'   => (int)$db->query("SELECT COUNT(*) FROM journal_audit WHERE severite = 'critique'")->fetchColumn(),
            'connexions_echouees' => (int)$db->query("SELECT COUNT(*) FROM journal_audit WHERE action = ? AND DATE(cree_le) = CURDATE()", [ACTION_FAILED_LOGIN])->fetchColumn(),
        ];
    }

    /**
     * Nettoie les anciens logs (conservation : 12 mois).
     * À appeler via un cron job mensuel.
     */
    public static function cleanOldLogs(int $keepMonths = 12): int
    {
        return Database::getInstance()->query(
            "DELETE FROM journal_audit WHERE cree_le < DATE_SUB(NOW(), INTERVAL ? MONTH)",
            [$keepMonths]
        )->rowCount();
    }
}
