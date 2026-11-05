-- ==============================================================================
-- Banco de Dados: Conversor Bananal
-- ==============================================================================

CREATE DATABASE IF NOT EXISTS `conversor_bananal` 
  CHARACTER SET utf8mb4 
  COLLATE utf8mb4_unicode_ci;

USE `conversor_bananal`;

-- Tabela de histórico de conversões
CREATE TABLE IF NOT EXISTS `conversoes` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `session_id` VARCHAR(128) NOT NULL COMMENT 'ID da sessão nativa PHP do usuário',
  `nome_original` VARCHAR(255) NOT NULL,
  `formato_origem` VARCHAR(32) NOT NULL,
  `tamanho_origem` BIGINT UNSIGNED NOT NULL,
  `formato_destino` VARCHAR(32) NOT NULL,
  `arquivo_convertido` VARCHAR(255) NULL,
  `tamanho_convertido` BIGINT UNSIGNED NULL,
  `status` ENUM('pendente', 'processando', 'concluido', 'erro') NOT NULL DEFAULT 'pendente',
  `mensagem_erro` TEXT NULL,
  `token_download` VARCHAR(64) NOT NULL UNIQUE,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `concluido_em` DATETIME NULL,
  INDEX `idx_session` (`session_id`),
  INDEX `idx_token` (`token_download`),
  INDEX `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela com tipos suportados e matriz de conversões sugeridas
CREATE TABLE IF NOT EXISTS `tipos_suportados` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `categoria` VARCHAR(50) NOT NULL COMMENT 'imagem, documento, dados, audio, etc.',
  `extensao` VARCHAR(16) NOT NULL UNIQUE,
  `mime_type` VARCHAR(100) NOT NULL,
  `nome_legivel` VARCHAR(100) NOT NULL,
  `conversoes_sugeridas` TEXT NOT NULL COMMENT 'Formatos separados por vírgula'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Carga inicial de tipos suportados e sugestões inteligentes
INSERT INTO `tipos_suportados` (`categoria`, `extensao`, `mime_type`, `nome_legivel`, `conversoes_sugeridas`) VALUES
-- Imagens
('imagem', 'png', 'image/png', 'Imagem PNG', 'jpg,jpeg,webp,gif,bmp,pdf,base64'),
('imagem', 'jpg', 'image/jpeg', 'Imagem JPEG/JPG', 'png,webp,gif,bmp,pdf,base64'),
('imagem', 'jpeg', 'image/jpeg', 'Imagem JPEG', 'png,webp,gif,bmp,pdf,base64'),
('imagem', 'webp', 'image/webp', 'Imagem WebP', 'png,jpg,jpeg,gif,bmp,pdf,base64'),
('imagem', 'gif', 'image/gif', 'Imagem GIF', 'png,jpg,jpeg,webp,bmp,pdf'),
('imagem', 'bmp', 'image/bmp', 'Imagem Bitmap (BMP)', 'png,jpg,jpeg,webp,gif,pdf'),
('imagem', 'svg', 'image/svg+xml', 'Vetor SVG', 'png,pdf,txt'),

-- Dados e Estruturados
('dados', 'json', 'application/json', 'Arquivo JSON', 'csv,xml,html,txt'),
('dados', 'csv', 'text/csv', 'Planilha CSV', 'json,html,txt,xml'),
('dados', 'xml', 'application/xml', 'Documento XML', 'json,txt,html'),

-- Documentos e Texto
('documento', 'txt', 'text/plain', 'Texto Simples (TXT)', 'html,pdf,json,md'),
('documento', 'md', 'text/markdown', 'Documento Markdown', 'html,txt,pdf'),
('documento', 'html', 'text/html', 'Página Web (HTML)', 'txt,md,pdf'),

-- Outros (para detecção e orientação)
('audio', 'mp3', 'audio/mpeg', 'Áudio MP3', 'wav,ogg'),
('audio', 'wav', 'audio/wav', 'Áudio WAV', 'mp3,ogg')
ON DUPLICATE KEY UPDATE 
  `categoria` = VALUES(`categoria`),
  `mime_type` = VALUES(`mime_type`),
  `nome_legivel` = VALUES(`nome_legivel`),
  `conversoes_sugeridas` = VALUES(`conversoes_sugeridas`);
