-- GovTribe Platform Database Schema
-- MySQL 8.0+ with InnoDB engine
-- Character set: utf8mb4 for full Unicode support

CREATE DATABASE IF NOT EXISTS govtribe_platform 
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE govtribe_platform;

-- Core opportunity tracking
CREATE TABLE agencies (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    type ENUM('Federal', 'State', 'Local', 'Education') NOT NULL DEFAULT 'Federal',
    fh_code VARCHAR(50) NULL,
    address JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_type (type),
    INDEX idx_fh_code (fh_code)
) ENGINE=InnoDB;

CREATE TABLE opportunities (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    source ENUM('SAM') NOT NULL DEFAULT 'SAM',
    external_id VARCHAR(255) NOT NULL,
    title VARCHAR(500) NOT NULL,
    description TEXT NULL,
    description_status ENUM('ok', 'missing') NOT NULL DEFAULT 'ok',
    agency_id INT UNSIGNED NULL,
    posted_at DATETIME NOT NULL,
    due_at DATETIME NULL,
    notice_type ENUM('Solicitation', 'Combined') NOT NULL,
    set_aside VARCHAR(100) NULL,
    ui_link VARCHAR(1024) NULL,
    score INT DEFAULT 0,
    score_reasons TEXT NULL,
    status ENUM('New', 'Review', 'Pursue', 'No-Bid', 'Awarded', 'Lost') DEFAULT 'New',
    active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_source_external (source, external_id),
    INDEX idx_due_at (due_at),
    INDEX idx_agency_id (agency_id),
    INDEX idx_status (status),
    INDEX idx_active (active),
    INDEX idx_score (score),
    INDEX idx_posted_at (posted_at),
    FULLTEXT(title, description),
    FOREIGN KEY (agency_id) REFERENCES agencies(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE opportunity_naics (
    opportunity_id INT UNSIGNED NOT NULL,
    naics_code VARCHAR(10) NOT NULL,
    PRIMARY KEY (opportunity_id, naics_code),
    FOREIGN KEY (opportunity_id) REFERENCES opportunities(id) ON DELETE CASCADE,
    INDEX idx_naics_code (naics_code)
) ENGINE=InnoDB;

CREATE TABLE contacts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    agency_id INT UNSIGNED NULL,
    opportunity_id INT UNSIGNED NULL,
    name VARCHAR(255) NULL,
    email VARCHAR(255) NULL,
    phone VARCHAR(50) NULL,
    title VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_agency_id (agency_id),
    INDEX idx_opportunity_id (opportunity_id),
    INDEX idx_email (email),
    FOREIGN KEY (agency_id) REFERENCES agencies(id) ON DELETE CASCADE,
    FOREIGN KEY (opportunity_id) REFERENCES opportunities(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE files (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    path VARCHAR(1024) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    mime VARCHAR(100) NOT NULL,
    size INT UNSIGNED NOT NULL,
    sha256 CHAR(64) NOT NULL,
    source_url VARCHAR(1024) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_sha256 (sha256),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB;

CREATE TABLE documents (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    opportunity_id INT UNSIGNED NOT NULL,
    file_id INT UNSIGNED NULL,
    label VARCHAR(255) NULL,
    source_url VARCHAR(1024) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_opportunity_id (opportunity_id),
    INDEX idx_file_id (file_id),
    FOREIGN KEY (opportunity_id) REFERENCES opportunities(id) ON DELETE CASCADE,
    FOREIGN KEY (file_id) REFERENCES files(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE opportunity_changes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    opportunity_id INT UNSIGNED NOT NULL,
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    payload JSON NOT NULL,
    INDEX idx_opportunity_id (opportunity_id),
    INDEX idx_changed_at (changed_at),
    FOREIGN KEY (opportunity_id) REFERENCES opportunities(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Supplier and RFQ management
CREATE TABLE suppliers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    contact_name VARCHAR(255) NULL,
    email VARCHAR(255) NULL,
    phone VARCHAR(50) NULL,
    address JSON NULL,
    status ENUM('Active', 'Inactive', 'Blacklisted') DEFAULT 'Active',
    capabilities JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_email (email)
) ENGINE=InnoDB;

CREATE TABLE rfqs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    opportunity_id INT UNSIGNED NOT NULL,
    supplier_id INT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    due_date DATETIME NULL,
    status ENUM('Draft', 'Sent', 'Responded', 'Expired') DEFAULT 'Draft',
    sent_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_opportunity_id (opportunity_id),
    INDEX idx_supplier_id (supplier_id),
    INDEX idx_status (status),
    INDEX idx_due_date (due_date),
    FOREIGN KEY (opportunity_id) REFERENCES opportunities(id) ON DELETE CASCADE,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE quotes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    rfq_id INT UNSIGNED NOT NULL,
    supplier_id INT UNSIGNED NOT NULL,
    total_amount DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    valid_until DATETIME NULL,
    notes TEXT NULL,
    file_id INT UNSIGNED NULL,
    status ENUM('Draft', 'Submitted', 'Accepted', 'Rejected') DEFAULT 'Draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_rfq_id (rfq_id),
    INDEX idx_supplier_id (supplier_id),
    INDEX idx_status (status),
    FOREIGN KEY (rfq_id) REFERENCES rfqs(id) ON DELETE CASCADE,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE CASCADE,
    FOREIGN KEY (file_id) REFERENCES files(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- BOM and pricing
CREATE TABLE boms (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    opportunity_id INT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    version INT DEFAULT 1,
    total_cost DECIMAL(15,2) DEFAULT 0.00,
    total_price DECIMAL(15,2) DEFAULT 0.00,
    markup_percent DECIMAL(5,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_opportunity_id (opportunity_id),
    FOREIGN KEY (opportunity_id) REFERENCES opportunities(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE bom_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    bom_id INT UNSIGNED NOT NULL,
    sku VARCHAR(100) NULL,
    description VARCHAR(500) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    unit_cost DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    unit_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    markup_percent DECIMAL(5,2) DEFAULT 0.00,
    total_cost DECIMAL(15,2) GENERATED ALWAYS AS (quantity * unit_cost) STORED,
    total_price DECIMAL(15,2) GENERATED ALWAYS AS (quantity * unit_price) STORED,
    clin_code VARCHAR(50) NULL,
    supplier_id INT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_bom_id (bom_id),
    INDEX idx_clin_code (clin_code),
    INDEX idx_supplier_id (supplier_id),
    FOREIGN KEY (bom_id) REFERENCES boms(id) ON DELETE CASCADE,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Proposal management
CREATE TABLE proposals (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    opportunity_id INT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    version INT DEFAULT 1,
    checklist JSON NULL,
    package_file_id INT UNSIGNED NULL,
    status ENUM('Draft', 'Review', 'Final', 'Submitted') DEFAULT 'Draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_opportunity_id (opportunity_id),
    INDEX idx_status (status),
    FOREIGN KEY (opportunity_id) REFERENCES opportunities(id) ON DELETE CASCADE,
    FOREIGN KEY (package_file_id) REFERENCES files(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE submissions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    opportunity_id INT UNSIGNED NOT NULL,
    proposal_id INT UNSIGNED NULL,
    channel ENUM('PIEE', 'FedConnect', 'GSAeBuy', 'Unison', 'Email') NOT NULL,
    submitted_at TIMESTAMP NULL,
    confirmation_ref VARCHAR(255) NULL,
    evidence_file_id INT UNSIGNED NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_opportunity_id (opportunity_id),
    INDEX idx_proposal_id (proposal_id),
    INDEX idx_channel (channel),
    FOREIGN KEY (opportunity_id) REFERENCES opportunities(id) ON DELETE CASCADE,
    FOREIGN KEY (proposal_id) REFERENCES proposals(id) ON DELETE SET NULL,
    FOREIGN KEY (evidence_file_id) REFERENCES files(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Award and invoice tracking
CREATE TABLE awards (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    opportunity_id INT UNSIGNED NOT NULL,
    award_number VARCHAR(255) NULL,
    award_amount DECIMAL(15,2) NULL,
    award_date DATE NULL,
    start_date DATE NULL,
    end_date DATE NULL,
    status ENUM('Pending', 'Active', 'Completed', 'Terminated') DEFAULT 'Pending',
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_opportunity_id (opportunity_id),
    INDEX idx_status (status),
    INDEX idx_award_date (award_date),
    FOREIGN KEY (opportunity_id) REFERENCES opportunities(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE clins (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    award_id INT UNSIGNED NOT NULL,
    clin_number VARCHAR(50) NOT NULL,
    description TEXT NULL,
    quantity INT NULL,
    unit_price DECIMAL(10,2) NULL,
    total_amount DECIMAL(15,2) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_award_id (award_id),
    INDEX idx_clin_number (clin_number),
    FOREIGN KEY (award_id) REFERENCES awards(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE invoices (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    award_id INT UNSIGNED NOT NULL,
    invoice_number VARCHAR(255) NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    invoice_date DATE NOT NULL,
    due_date DATE NULL,
    portal ENUM('PIEE', 'IPP', 'Other') NULL,
    status ENUM('Draft', 'Submitted', 'Approved', 'Paid', 'Rejected') DEFAULT 'Draft',
    package_file_id INT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_award_id (award_id),
    INDEX idx_status (status),
    INDEX idx_invoice_date (invoice_date),
    FOREIGN KEY (award_id) REFERENCES awards(id) ON DELETE CASCADE,
    FOREIGN KEY (package_file_id) REFERENCES files(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- User management and security
CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('Admin', 'Capture Manager', 'Proposal Manager', 'Sales Engineer', 'Vendor Manager', 'Accountant', 'Viewer') NOT NULL,
    status ENUM('Active', 'Inactive', 'Suspended') DEFAULT 'Active',
    first_name VARCHAR(100) NULL,
    last_name VARCHAR(100) NULL,
    mfa_secret VARCHAR(32) NULL,
    last_login TIMESTAMP NULL,
    login_attempts INT DEFAULT 0,
    locked_until TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_status (status)
) ENGINE=InnoDB;

CREATE TABLE sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    last_seen TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_last_seen (last_seen),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE settings (
    `key` VARCHAR(100) PRIMARY KEY,
    `value` TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE audit_log (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NULL,
    action VARCHAR(100) NOT NULL,
    entity VARCHAR(100) NOT NULL,
    entity_id INT UNSIGNED NULL,
    meta JSON NULL,
    ip_address VARCHAR(45) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_entity (entity, entity_id),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Insert default settings
INSERT INTO settings (`key`, `value`) VALUES
('sam_api_key', ''),
('sam_use_alpha', 'false'),
('default_naics_codes', '["334111", "541512"]'),
('default_set_aside_types', '["WOSB", "EDWOSB"]'),
('default_posted_days', '30'),
('preferred_agencies', '[]'),
('fetch_attachments', 'true'),
('email_smtp_host', ''),
('email_smtp_port', '587'),
('email_smtp_username', ''),
('email_smtp_password', ''),
('email_from_address', ''),
('email_from_name', 'GovTribe Platform'),
('timezone', 'America/Los_Angeles'),
('files_max_size', '104857600'),
('backup_retention_days', '30');

-- Insert default admin user (password: admin123 - CHANGE THIS!)
INSERT INTO users (email, password_hash, role, first_name, last_name) VALUES
('admin@example.com', '$argon2id$v=19$m=65536,t=4,p=3$example_salt$example_hash', 'Admin', 'System', 'Administrator');