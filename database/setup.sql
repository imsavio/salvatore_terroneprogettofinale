-- ============================================
-- Script SQL per creare il database del blog
-- ============================================

-- Crea il database
CREATE DATABASE IF NOT EXISTS blog_db 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- Usa il database
USE blog_db;

-- Crea la tabella articles
CREATE TABLE IF NOT EXISTS articles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    author_name VARCHAR(100) NOT NULL,
    author_email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status ENUM('draft', 'published') DEFAULT 'draft',
    views INT DEFAULT 0,
    
    -- Indici per migliorare le performance
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    INDEX idx_author_email (author_email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserisci articoli di esempio
INSERT INTO articles (title, content, author_name, author_email, status, views) VALUES
('Benvenuto nel Blog', 
'Questo è il primo articolo del blog. Qui puoi condividere le tue storie, esperienze e pensieri con la community. Ogni storia merita di essere raccontata!', 
'Mario Rossi', 
'mario.rossi@example.com', 
'published', 
15),

('Come Iniziare a Scrivere', 
'Scrivere un articolo è semplice. Basta trovare un argomento che ti appassiona e iniziare a raccontare. Non serve essere uno scrittore professionista, basta essere autentici e condividere la propria esperienza.', 
'Giulia Verdi', 
'giulia.verdi@example.com', 
'published', 
8),

('La Potenza delle Storie', 
'Le storie hanno il potere di connettere le persone, di ispirare e di creare empatia. Quando condividiamo le nostre esperienze, permettiamo ad altri di sentirsi meno soli e di trovare conforto nelle nostre parole.', 
'Luca Bianchi', 
'luca.bianchi@example.com', 
'published', 
22),

('Consigli per Scrittori Esordienti', 
'Se sei nuovo nella scrittura, ecco alcuni consigli: scrivi regolarmente, leggi molto, sii autentico e non aver paura di condividere le tue emozioni. La pratica rende perfetti!', 
'Anna Neri', 
'anna.neri@example.com', 
'draft', 
0),

('Il Viaggio della Vita', 
'La vita è un viaggio fatto di momenti, esperienze e incontri. Ogni giorno è una nuova pagina da scrivere, ogni esperienza è una storia da raccontare. Condividiamo insieme questo viaggio!', 
'Paolo Blu', 
'paolo.blu@example.com', 
'published', 
12);

-- Verifica i dati inseriti
SELECT COUNT(*) as total_articles FROM articles;
SELECT COUNT(*) as published_articles FROM articles WHERE status = 'published';
SELECT COUNT(*) as draft_articles FROM articles WHERE status = 'draft';

