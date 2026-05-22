<?php
/**
 * =====================================================
 * EUROCARE HUMANITAIRE - Classe Database
 * =====================================================
 * Fichier : app/core/Database.php
 * Description : Wrapper PDO en Singleton pour gérer
 *               toutes les interactions avec MySQL.
 *               Inclut préparation des requêtes,
 *               transactions et requêtes sécurisées.
 * =====================================================
 */

defined('BASEPATH') or die('Accès direct interdit.');

class Database
{
    /** @var Database|null Instance unique (Singleton) */
    private static ?Database $instance = null;

    /** @var PDO Connexion PDO active */
    private PDO $pdo;

    /** @var PDOStatement|false Dernière requête préparée */
    private $statement;

    /** @var int Nombre de requêtes exécutées (debug) */
    private int $queryCount = 0;

    // =====================================================
    // CONSTRUCTEUR PRIVÉ (Singleton)
    // =====================================================
    /**
     * Initialise la connexion PDO sécurisée à MySQL.
     * Privé pour forcer l'utilisation de getInstance().
     */
    private function __construct()
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
        );

        $options = unserialize(DB_OPTIONS);

        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Journalisation de l'erreur sans exposer les détails
            $this->logError('Erreur de connexion à la base de données : ' . $e->getMessage());
            // Message générique pour l'utilisateur
            throw new RuntimeException('Service temporairement indisponible. Veuillez réessayer.');
        }
    }

    /** Empêche le clonage de l'instance */
    private function __clone() {}

    /** Empêche la désérialisation */
    public function __wakeup()
    {
        throw new RuntimeException('Désérialisation interdite.');
    }

    // =====================================================
    // SINGLETON : OBTENIR L'INSTANCE UNIQUE
    // =====================================================
    /**
     * Retourne l'instance unique de la connexion base de données.
     *
     * @return Database
     */
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // =====================================================
    // MÉTHODES PRINCIPALES DE REQUÊTES
    // =====================================================

    /**
     * Prépare et exécute une requête SQL paramétrée.
     *
     * @param  string $sql    Requête SQL avec placeholders (? ou :nom)
     * @param  array  $params Paramètres à binder (protection SQLi)
     * @return self           Pour chaînage : ->query()->fetch()
     */
    public function query(string $sql, array $params = []): self
    {
        $this->queryCount++;

        try {
            $this->statement = $this->pdo->prepare($sql);

            // Binding sécurisé des paramètres avec types PDO
            if (!empty($params)) {
                foreach ($params as $key => $value) {
                    $paramKey  = is_int($key) ? $key + 1 : $key;
                    $paramType = match(true) {
                        is_int($value)   => PDO::PARAM_INT,
                        is_bool($value)  => PDO::PARAM_BOOL,
                        is_null($value)  => PDO::PARAM_NULL,
                        default          => PDO::PARAM_STR,
                    };
                    $this->statement->bindValue($paramKey, $value, $paramType);
                }
            }

            $this->statement->execute();

        } catch (PDOException $e) {
            $this->logError('Erreur SQL : ' . $e->getMessage() . ' | Query : ' . $sql);
            throw new RuntimeException('Erreur de traitement des données.');
        }

        return $this;
    }

    /**
     * Récupère UNE seule ligne (tableau associatif).
     *
     * @return array|false Tableau associatif ou false
     */
    public function fetch(): array|false
    {
        return $this->statement ? $this->statement->fetch() : false;
    }

    /**
     * Récupère TOUTES les lignes.
     *
     * @return array Tableau de tableaux associatifs
     */
    public function fetchAll(): array
    {
        return $this->statement ? $this->statement->fetchAll() : [];
    }

    /**
     * Récupère la valeur d'une seule colonne de la première ligne.
     *
     * @param  int $columnIndex Index de la colonne (défaut: 0)
     * @return mixed
     */
    public function fetchColumn(int $columnIndex = 0): mixed
    {
        return $this->statement ? $this->statement->fetchColumn($columnIndex) : null;
    }

    /**
     * Retourne le nombre de lignes affectées par la dernière requête.
     *
     * @return int
     */
    public function rowCount(): int
    {
        return $this->statement ? $this->statement->rowCount() : 0;
    }

    /**
     * Retourne l'ID auto-incrémenté du dernier INSERT.
     *
     * @return string|false
     */
    public function lastInsertId(): string|false
    {
        return $this->pdo->lastInsertId();
    }

    // =====================================================
    // MÉTHODES UTILITAIRES CRUD
    // =====================================================

    /**
     * Insère un enregistrement dans une table.
     *
     * @param  string $table  Nom de la table
     * @param  array  $data   Tableau [colonne => valeur]
     * @return int|false      ID inséré ou false
     */
    public function insert(string $table, array $data): int|false
    {
        if (empty($data)) return false;

        // Nettoyage des noms de colonnes (sécurité)
        $columns   = array_map(fn($col) => "`$col`", array_keys($data));
        $placeholders = array_fill(0, count($data), '?');

        $sql = sprintf(
            'INSERT INTO `%s` (%s) VALUES (%s)',
            $table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        $this->query($sql, array_values($data));
        $id = $this->lastInsertId();
        return $id ? (int)$id : false;
    }

    /**
     * Met à jour des enregistrements dans une table.
     *
     * @param  string $table      Nom de la table
     * @param  array  $data       Tableau [colonne => nouvelle_valeur]
     * @param  array  $conditions Conditions WHERE [colonne => valeur]
     * @return int                Nombre de lignes affectées
     */
    public function update(string $table, array $data, array $conditions): int
    {
        if (empty($data) || empty($conditions)) return 0;

        $setParts   = array_map(fn($col) => "`$col` = ?", array_keys($data));
        $whereParts = array_map(fn($col) => "`$col` = ?", array_keys($conditions));

        $sql = sprintf(
            'UPDATE `%s` SET %s WHERE %s',
            $table,
            implode(', ', $setParts),
            implode(' AND ', $whereParts)
        );

        $params = array_merge(array_values($data), array_values($conditions));
        $this->query($sql, $params);
        return $this->rowCount();
    }

    /**
     * Supprime des enregistrements d'une table.
     *
     * @param  string $table      Nom de la table
     * @param  array  $conditions Conditions WHERE [colonne => valeur]
     * @return int                Nombre de lignes supprimées
     */
    public function delete(string $table, array $conditions): int
    {
        if (empty($conditions)) return 0;

        $whereParts = array_map(fn($col) => "`$col` = ?", array_keys($conditions));

        $sql = sprintf(
            'DELETE FROM `%s` WHERE %s',
            $table,
            implode(' AND ', $whereParts)
        );

        $this->query($sql, array_values($conditions));
        return $this->rowCount();
    }

    /**
     * Cherche un enregistrement par ses conditions.
     *
     * @param  string $table      Nom de la table
     * @param  array  $conditions Conditions WHERE [colonne => valeur]
     * @param  string $select     Colonnes à sélectionner (défaut: *)
     * @return array|false
     */
    public function findOne(string $table, array $conditions, string $select = '*'): array|false
    {
        $whereParts = array_map(fn($col) => "`$col` = ?", array_keys($conditions));

        $sql = sprintf(
            'SELECT %s FROM `%s` WHERE %s LIMIT 1',
            $select, $table, implode(' AND ', $whereParts)
        );

        return $this->query($sql, array_values($conditions))->fetch();
    }

    /**
     * Cherche tous les enregistrements correspondant aux conditions.
     *
     * @param  string $table      Nom de la table
     * @param  array  $conditions Conditions WHERE (optionnel)
     * @param  string $select     Colonnes à sélectionner
     * @param  string $orderBy    Ordre de tri
     * @param  int    $limit      Limite de résultats (0 = pas de limite)
     * @return array
     */
    public function findAll(
        string $table,
        array  $conditions = [],
        string $select     = '*',
        string $orderBy    = '',
        int    $limit      = 0
    ): array {
        $sql    = "SELECT $select FROM `$table`";
        $params = [];

        if (!empty($conditions)) {
            $whereParts = array_map(fn($col) => "`$col` = ?", array_keys($conditions));
            $sql  .= ' WHERE ' . implode(' AND ', $whereParts);
            $params = array_values($conditions);
        }

        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }

        if ($limit > 0) {
            $sql .= " LIMIT $limit";
        }

        return $this->query($sql, $params)->fetchAll();
    }

    /**
     * Compte les enregistrements selon des conditions.
     *
     * @param  string $table      Nom de la table
     * @param  array  $conditions Conditions WHERE (optionnel)
     * @return int
     */
    public function count(string $table, array $conditions = []): int
    {
        $sql    = "SELECT COUNT(*) FROM `$table`";
        $params = [];

        if (!empty($conditions)) {
            $whereParts = array_map(fn($col) => "`$col` = ?", array_keys($conditions));
            $sql  .= ' WHERE ' . implode(' AND ', $whereParts);
            $params = array_values($conditions);
        }

        return (int)$this->query($sql, $params)->fetchColumn();
    }

    // =====================================================
    // GESTION DES TRANSACTIONS
    // =====================================================

    /**
     * Démarre une transaction.
     */
    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    /**
     * Valide la transaction.
     */
    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    /**
     * Annule la transaction en cas d'erreur.
     */
    public function rollBack(): bool
    {
        return $this->pdo->inTransaction() ? $this->pdo->rollBack() : false;
    }

    /**
     * Vérifie si une transaction est en cours.
     */
    public function inTransaction(): bool
    {
        return $this->pdo->inTransaction();
    }

    // =====================================================
    // MÉTHODES DE DEBUG ET JOURNALISATION
    // =====================================================

    /**
     * Retourne le nombre de requêtes exécutées (debug).
     */
    public function getQueryCount(): int
    {
        return $this->queryCount;
    }

    /**
     * Journalise une erreur SQL dans les logs.
     */
    private function logError(string $message): void
    {
        $logFile = defined('ROOT_PATH')
            ? ROOT_PATH . '/storage/logs/db_errors.log'
            : sys_get_temp_dir() . '/eurocare_db.log';

        $entry = sprintf(
            '[%s] [%s] %s%s',
            date('Y-m-d H:i:s'),
            $_SERVER['REQUEST_URI'] ?? 'CLI',
            $message,
            PHP_EOL
        );

        @error_log($entry, 3, $logFile);
    }
}
