<?php

require_once __DIR__ . '/../config/database-standalone.php';

class ArticleManager {
    
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function createArticle($title, $content, $author_name, $author_email, $status = 'draft') {
        $this->validateArticleData($title, $content, $author_name, $author_email, $status);
        
        $title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
        $content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
        $author_name = htmlspecialchars($author_name, ENT_QUOTES, 'UTF-8');
        
        try {
            $stmt = $this->db->prepare("
                INSERT INTO articles (title, content, author_name, author_email, status) 
                VALUES (:title, :content, :author_name, :author_email, :status)
            ");
            
            $stmt->execute([
                ':title' => $title,
                ':content' => $content,
                ':author_name' => $author_name,
                ':author_email' => $author_email,
                ':status' => $status
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Errore durante la creazione dell'articolo: " . $e->getMessage());
        }
    }
    
    public function getAllArticles($status = 'published', $orderBy = 'created_at DESC') {
        try {
            $sql = "SELECT * FROM articles";
            $params = [];
            
            if ($status !== null) {
                $sql .= " WHERE status = :status";
                $params[':status'] = $status;
            }
            
            $allowedColumns = ['id', 'title', 'created_at', 'updated_at', 'status', 'views'];
            $allowedDirections = ['ASC', 'DESC'];
            
            $orderParts = explode(' ', trim($orderBy));
            $column = $orderParts[0] ?? 'created_at';
            $direction = strtoupper($orderParts[1] ?? 'DESC');
            
            if (!in_array($column, $allowedColumns)) {
                $column = 'created_at';
            }
            
            if (!in_array($direction, $allowedDirections)) {
                $direction = 'DESC';
            }
            
            $sql .= " ORDER BY " . $column . " " . $direction;
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("Errore durante il recupero degli articoli: " . $e->getMessage());
        }
    }
    
    public function getArticleById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM articles WHERE id = :id");
            $stmt->execute([':id' => $id]);
            
            $article = $stmt->fetch();
            return $article ? $article : null;
        } catch (PDOException $e) {
            throw new Exception("Errore durante il recupero dell'articolo: " . $e->getMessage());
        }
    }
    
    public function updateArticle($id, $data) {
        if (!is_numeric($id) || $id <= 0) {
            throw new Exception("ID articolo non valido");
        }
        
        $allowedFields = ['title', 'content', 'author_name', 'author_email', 'status'];
        $updateFields = [];
        $params = [':id' => $id];
        
        foreach ($data as $field => $value) {
            if (in_array($field, $allowedFields)) {
                if ($field !== 'status') {
                    $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                }
                
                $updateFields[] = "$field = :$field";
                $params[":$field"] = $value;
            }
        }
        
        if (empty($updateFields)) {
            throw new Exception("Nessun campo valido da aggiornare");
        }
        
        if (isset($data['author_email'])) {
            $this->validateEmail($data['author_email']);
        }
        
        if (isset($data['status'])) {
            $this->validateStatus($data['status']);
        }
        
        try {
            $sql = "UPDATE articles SET " . implode(', ', $updateFields) . " WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new Exception("Errore durante l'aggiornamento dell'articolo: " . $e->getMessage());
        }
    }
    
    public function deleteArticle($id) {
        if (!is_numeric($id) || $id <= 0) {
            throw new Exception("ID articolo non valido");
        }
        
        try {
            $stmt = $this->db->prepare("DELETE FROM articles WHERE id = :id");
            $stmt->execute([':id' => $id]);
            
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new Exception("Errore durante l'eliminazione dell'articolo: " . $e->getMessage());
        }
    }
    
    public function incrementViews($id) {
        if (!is_numeric($id) || $id <= 0) {
            throw new Exception("ID articolo non valido");
        }
        
        try {
            $stmt = $this->db->prepare("UPDATE articles SET views = views + 1 WHERE id = :id");
            $stmt->execute([':id' => $id]);
            
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new Exception("Errore durante l'incremento delle visualizzazioni: " . $e->getMessage());
        }
    }
    
    public function getRecentArticles($limit = 5) {
        if (!is_numeric($limit) || $limit <= 0) {
            $limit = 5;
        }
        
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM articles 
                WHERE status = 'published' 
                ORDER BY created_at DESC 
                LIMIT :limit
            ");
            
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("Errore durante il recupero degli articoli recenti: " . $e->getMessage());
        }
    }
    
    private function validateArticleData($title, $content, $author_name, $author_email, $status) {
        if (empty($title) || strlen($title) > 255) {
            throw new Exception("Il titolo è obbligatorio e non può superare i 255 caratteri");
        }
        
        if (empty($content)) {
            throw new Exception("Il contenuto è obbligatorio");
        }
        
        if (empty($author_name) || strlen($author_name) > 100) {
            throw new Exception("Il nome dell'autore è obbligatorio e non può superare i 100 caratteri");
        }
        
        $this->validateEmail($author_email);
        $this->validateStatus($status);
    }
    
    private function validateEmail($email) {
        if (empty($email) || strlen($email) > 100) {
            throw new Exception("L'email è obbligatoria e non può superare i 100 caratteri");
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Formato email non valido");
        }
    }
    
    private function validateStatus($status) {
        if (!in_array($status, ['draft', 'published'])) {
            throw new Exception("Status non valido. Deve essere 'draft' o 'published'");
        }
    }
}

