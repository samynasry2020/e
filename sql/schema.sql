-- Schema for Private GovTribe-Style Platform (MySQL 8)
-- Engine: InnoDB, Charset: utf8mb4

SET NAMES utf8mb4 COLLATE utf8mb4_0900_ai_ci;
SET time_zone = '+00:00';

-- Ensure database options (optional; handled by server usually)
SET SESSION sql_require_primary_key = 0;

-- Tables are created idempotently for first run convenience

CREATE TABLE IF NOT EXISTS settings (
  `key` VARCHAR(191) NOT NULL,
  `value` TEXT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS users (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  email VARCHAR(320) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('Admin','CaptureManager','ProposalManager','SalesEngineer','VendorManager','Accountant','Viewer') NOT NULL,
  status ENUM('Active','Disabled') NOT NULL DEFAULT 'Active',
  mfa_secret VARCHAR(64) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS sessions (
  id CHAR(64) NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL,
  ip VARCHAR(45) NULL,
  ua VARCHAR(512) NULL,
  last_seen DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_sessions_user_id (user_id),
  CONSTRAINT fk_sessions_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS agencies (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  `type` ENUM('Federal','State','Local','Education') NOT NULL DEFAULT 'Federal',
  fh_code VARCHAR(64) NULL,
  address JSON NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_agencies_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS contacts (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  agency_id BIGINT UNSIGNED NULL,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(320) NULL,
  phone VARCHAR(64) NULL,
  title VARCHAR(255) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_contacts_agency_id (agency_id),
  CONSTRAINT fk_contacts_agency FOREIGN KEY (agency_id) REFERENCES agencies(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS opportunities (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  source ENUM('SAM') NOT NULL,
  external_id VARCHAR(128) NOT NULL,
  title VARCHAR(512) NOT NULL,
  description MEDIUMTEXT NULL,
  description_status ENUM('ok','missing') NOT NULL DEFAULT 'missing',
  agency_id BIGINT UNSIGNED NULL,
  posted_at DATETIME NULL,
  due_at DATETIME NULL,
  notice_type ENUM('Solicitation','Combined') NULL,
  set_aside VARCHAR(64) NULL,
  ui_link VARCHAR(1024) NULL,
  score INT NOT NULL DEFAULT 0,
  score_reasons TEXT NULL,
  status ENUM('New','Review','Pursue','No-Bid','Awarded','Lost') NOT NULL DEFAULT 'New',
  active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_opportunities_source_external (source, external_id),
  KEY idx_opportunities_due_at (due_at),
  KEY idx_opportunities_agency_id (agency_id),
  FULLTEXT KEY ft_opportunities_title_description (title, description),
  CONSTRAINT fk_opportunities_agency FOREIGN KEY (agency_id) REFERENCES agencies(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS opportunity_naics (
  opportunity_id BIGINT UNSIGNED NOT NULL,
  naics_code VARCHAR(10) NOT NULL,
  PRIMARY KEY (opportunity_id, naics_code),
  CONSTRAINT fk_opportunity_naics_opportunity FOREIGN KEY (opportunity_id) REFERENCES opportunities(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS files (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `path` VARCHAR(1024) NOT NULL,
  original_name VARCHAR(512) NOT NULL,
  mime VARCHAR(128) NOT NULL,
  size BIGINT UNSIGNED NOT NULL,
  sha256 CHAR(64) NOT NULL,
  source_url VARCHAR(1024) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_files_sha256 (sha256),
  KEY idx_files_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS documents (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  opportunity_id BIGINT UNSIGNED NOT NULL,
  file_id BIGINT UNSIGNED NULL,
  label VARCHAR(255) NULL,
  PRIMARY KEY (id),
  KEY idx_documents_opportunity_id (opportunity_id),
  KEY idx_documents_file_id (file_id),
  CONSTRAINT fk_documents_opportunity FOREIGN KEY (opportunity_id) REFERENCES opportunities(id) ON DELETE CASCADE,
  CONSTRAINT fk_documents_file FOREIGN KEY (file_id) REFERENCES files(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS opportunity_changes (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  opportunity_id BIGINT UNSIGNED NOT NULL,
  changed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  payload JSON NOT NULL,
  PRIMARY KEY (id),
  KEY idx_opportunity_changes_opportunity_id (opportunity_id),
  KEY idx_opportunity_changes_changed_at (changed_at),
  CONSTRAINT fk_opportunity_changes_opportunity FOREIGN KEY (opportunity_id) REFERENCES opportunities(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Suppliers, RFQs, Quotes
CREATE TABLE IF NOT EXISTS suppliers (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  status ENUM('Active','Inactive') NOT NULL DEFAULT 'Active',
  contact_name VARCHAR(255) NULL,
  contact_email VARCHAR(320) NULL,
  contact_phone VARCHAR(64) NULL,
  notes TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_suppliers_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS rfqs (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  opportunity_id BIGINT UNSIGNED NOT NULL,
  supplier_id BIGINT UNSIGNED NOT NULL,
  sent_at DATETIME NULL,
  due_at DATETIME NULL,
  status ENUM('Draft','Sent','Received','Closed') NOT NULL DEFAULT 'Draft',
  notes TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_rfqs_opportunity_id (opportunity_id),
  KEY idx_rfqs_supplier_id (supplier_id),
  CONSTRAINT fk_rfqs_opportunity FOREIGN KEY (opportunity_id) REFERENCES opportunities(id) ON DELETE CASCADE,
  CONSTRAINT fk_rfqs_supplier FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS quotes (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  rfq_id BIGINT UNSIGNED NOT NULL,
  supplier_id BIGINT UNSIGNED NOT NULL,
  total_amount DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  file_id BIGINT UNSIGNED NULL,
  received_at DATETIME NULL,
  status ENUM('Pending','Received','Rejected','Accepted') NOT NULL DEFAULT 'Pending',
  notes TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_quotes_rfq_id (rfq_id),
  KEY idx_quotes_supplier_id (supplier_id),
  CONSTRAINT fk_quotes_rfq FOREIGN KEY (rfq_id) REFERENCES rfqs(id) ON DELETE CASCADE,
  CONSTRAINT fk_quotes_supplier FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE CASCADE,
  CONSTRAINT fk_quotes_file FOREIGN KEY (file_id) REFERENCES files(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- BOMs and items
CREATE TABLE IF NOT EXISTS boms (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  opportunity_id BIGINT UNSIGNED NOT NULL,
  name VARCHAR(255) NOT NULL,
  notes TEXT NULL,
  version INT NOT NULL DEFAULT 1,
  total_cost DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  total_price DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_boms_opportunity_id (opportunity_id),
  CONSTRAINT fk_boms_opportunity FOREIGN KEY (opportunity_id) REFERENCES opportunities(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS bom_items (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  bom_id BIGINT UNSIGNED NOT NULL,
  position INT NOT NULL DEFAULT 0,
  sku VARCHAR(128) NULL,
  description TEXT NULL,
  qty DECIMAL(14,4) NOT NULL DEFAULT 1.0000,
  unit_cost DECIMAL(14,4) NOT NULL DEFAULT 0.0000,
  markup_percent DECIMAL(7,4) NOT NULL DEFAULT 0.0000,
  total_cost DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  total_price DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  clin_code VARCHAR(64) NULL,
  PRIMARY KEY (id),
  KEY idx_bom_items_bom_id (bom_id),
  CONSTRAINT fk_bom_items_bom FOREIGN KEY (bom_id) REFERENCES boms(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Proposals
CREATE TABLE IF NOT EXISTS proposals (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  opportunity_id BIGINT UNSIGNED NOT NULL,
  version INT NOT NULL DEFAULT 1,
  package_file_id BIGINT UNSIGNED NULL,
  checklist JSON NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_proposals_opportunity_id (opportunity_id),
  CONSTRAINT fk_proposals_opportunity FOREIGN KEY (opportunity_id) REFERENCES opportunities(id) ON DELETE CASCADE,
  CONSTRAINT fk_proposals_file FOREIGN KEY (package_file_id) REFERENCES files(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Submissions
CREATE TABLE IF NOT EXISTS submissions (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  opportunity_id BIGINT UNSIGNED NOT NULL,
  channel ENUM('PIEE','FedConnect','GSAeBuy','Unison','Email') NOT NULL,
  submitted_at DATETIME NULL,
  confirmation_ref VARCHAR(255) NULL,
  evidence_file_id BIGINT UNSIGNED NULL,
  notes TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_submissions_opportunity_id (opportunity_id),
  CONSTRAINT fk_submissions_opportunity FOREIGN KEY (opportunity_id) REFERENCES opportunities(id) ON DELETE CASCADE,
  CONSTRAINT fk_submissions_evidence_file FOREIGN KEY (evidence_file_id) REFERENCES files(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Awards and CLINs
CREATE TABLE IF NOT EXISTS awards (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  opportunity_id BIGINT UNSIGNED NOT NULL,
  award_number VARCHAR(128) NULL,
  awarded_at DATETIME NULL,
  amount DECIMAL(14,2) NULL,
  notes TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_awards_opportunity_id (opportunity_id),
  CONSTRAINT fk_awards_opportunity FOREIGN KEY (opportunity_id) REFERENCES opportunities(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS clins (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  award_id BIGINT UNSIGNED NOT NULL,
  clin_code VARCHAR(64) NOT NULL,
  description TEXT NULL,
  amount DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  status ENUM('Open','Closed') NOT NULL DEFAULT 'Open',
  period_start DATE NULL,
  period_end DATE NULL,
  PRIMARY KEY (id),
  KEY idx_clins_award_id (award_id),
  CONSTRAINT fk_clins_award FOREIGN KEY (award_id) REFERENCES awards(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS invoices (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  award_id BIGINT UNSIGNED NOT NULL,
  portal ENUM('PIEE','IPP') NOT NULL,
  amount DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  status ENUM('Draft','Submitted','Paid','Rejected') NOT NULL DEFAULT 'Draft',
  submitted_at DATETIME NULL,
  paid_at DATETIME NULL,
  file_id BIGINT UNSIGNED NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_invoices_award_id (award_id),
  CONSTRAINT fk_invoices_award FOREIGN KEY (award_id) REFERENCES awards(id) ON DELETE CASCADE,
  CONSTRAINT fk_invoices_file FOREIGN KEY (file_id) REFERENCES files(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Audit Log
CREATE TABLE IF NOT EXISTS audit_log (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NULL,
  action VARCHAR(64) NOT NULL,
  entity VARCHAR(64) NULL,
  entity_id BIGINT UNSIGNED NULL,
  meta JSON NULL,
  ip VARCHAR(45) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_audit_user_entity_created (user_id, entity, created_at),
  CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Helpful initial settings (non-sensitive)
INSERT INTO settings (`key`, `value`) VALUES
  ('app.tz', 'America/Los_Angeles')
ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);

