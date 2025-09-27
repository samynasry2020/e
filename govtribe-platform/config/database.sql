-- GovTribe Platform Database Schema
-- MySQL 8.0+ with InnoDB engine
-- Created: 2025-01-27

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Create database
CREATE DATABASE IF NOT EXISTS govtribe_platform 
    CHARACTER SET utf8mb4 
    COLLATE utf8mb4_unicode_ci;

USE govtribe_platform;

-- Core tables
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
    external_id VARCHAR(100) NOT NULL,
    title VARCHAR(500) NOT NULL,
    description TEXT NULL,
    description_status ENUM('ok', 'missing') NOT NULL DEFAULT 'missing',
    agency_id INT UNSIGNED NULL,
    posted_at DATETIME NOT NULL,
    due_at DATETIME NULL,
    notice_type ENUM('Solicitation', 'Combined') NOT NULL,
    set_aside VARCHAR(100) NULL,
    ui_link VARCHAR(1024) NULL,
    score INT UNSIGNED DEFAULT 0 CHECK (score <= 100),
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
    FULLTEXT idx_fulltext (title, description),
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
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NULL,
    phone VARCHAR(50) NULL,
    title VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_agency_id (agency_id),
    INDEX idx_email (email),
    FOREIGN KEY (agency_id) REFERENCES agencies(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE files (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    path VARCHAR(500) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    size INT UNSIGNED NOT NULL,
    sha256 VARCHAR(64) NOT NULL UNIQUE,
    source_url VARCHAR(1024) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_sha256 (sha256),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB;

CREATE TABLE documents (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    opportunity_id INT UNSIGNED NOT NULL,
    file_id INT UNSIGNED NOT NULL,
    label VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (opportunity_id) REFERENCES opportunities(id) ON DELETE CASCADE,
    FOREIGN KEY (file_id) REFERENCES files(id) ON DELETE CASCADE,
    INDEX idx_opportunity_id (opportunity_id)
) ENGINE=InnoDB;

CREATE TABLE opportunity_changes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    opportunity_id INT UNSIGNED NOT NULL,
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    payload JSON NOT NULL,
    FOREIGN KEY (opportunity_id) REFERENCES opportunities(id) ON DELETE CASCADE,
    INDEX idx_opportunity_id (opportunity_id),
    INDEX idx_changed_at (changed_at)
) ENGINE=InnoDB;

-- BOM/Proposal/Award pipeline tables
CREATE TABLE suppliers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    contact_name VARCHAR(255) NULL,
    email VARCHAR(255) NULL,
    phone VARCHAR(50) NULL,
    address TEXT NULL,
    status ENUM('Active', 'Inactive', 'Suspended') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status)
) ENGINE=InnoDB;

CREATE TABLE rfqs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    opportunity_id INT UNSIGNED NOT NULL,
    supplier_id INT UNSIGNED NOT NULL,
    sent_at TIMESTAMP NULL,
    due_at TIMESTAMP NULL,
    status ENUM('Draft', 'Sent', 'Received', 'Overdue') DEFAULT 'Draft',
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (opportunity_id) REFERENCES opportunities(id) ON DELETE CASCADE,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE CASCADE,
    INDEX idx_opportunity_id (opportunity_id),
    INDEX idx_status (status)
) ENGINE=InnoDB;

CREATE TABLE quotes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    rfq_id INT UNSIGNED NOT NULL,
    total_amount DECIMAL(12,2) NULL,
    file_id INT UNSIGNED NULL,
    received_at TIMESTAMP NULL,
    valid_until TIMESTAMP NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (rfq_id) REFERENCES rfqs(id) ON DELETE CASCADE,
    FOREIGN KEY (file_id) REFERENCES files(id) ON DELETE SET NULL,
    INDEX idx_rfq_id (rfq_id)
) ENGINE=InnoDB;

CREATE TABLE boms (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    opportunity_id INT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    version VARCHAR(20) DEFAULT '1.0',
    total_cost DECIMAL(12,2) DEFAULT 0.00,
    total_price DECIMAL(12,2) DEFAULT 0.00,
    markup_percent DECIMAL(5,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (opportunity_id) REFERENCES opportunities(id) ON DELETE CASCADE,
    INDEX idx_opportunity_id (opportunity_id)
) ENGINE=InnoDB;

CREATE TABLE bom_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    bom_id INT UNSIGNED NOT NULL,
    sku VARCHAR(100) NULL,
    description TEXT NOT NULL,
    quantity DECIMAL(10,3) NOT NULL DEFAULT 1.000,
    unit_cost DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    unit_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    markup_percent DECIMAL(5,2) DEFAULT 0.00,
    total_cost DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    total_price DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    clin_code VARCHAR(50) NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (bom_id) REFERENCES boms(id) ON DELETE CASCADE,
    INDEX idx_bom_id (bom_id),
    INDEX idx_sort_order (sort_order)
) ENGINE=InnoDB;

CREATE TABLE proposals (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    opportunity_id INT UNSIGNED NOT NULL,
    version VARCHAR(20) NOT NULL,
    package_file_id INT UNSIGNED NULL,
    checklist JSON NULL,
    status ENUM('Draft', 'Ready', 'Submitted') DEFAULT 'Draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (opportunity_id) REFERENCES opportunities(id) ON DELETE CASCADE,
    FOREIGN KEY (package_file_id) REFERENCES files(id) ON DELETE SET NULL,
    UNIQUE KEY uk_opportunity_version (opportunity_id, version),
    INDEX idx_opportunity_id (opportunity_id),
    INDEX idx_status (status)
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
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (opportunity_id) REFERENCES opportunities(id) ON DELETE CASCADE,
    FOREIGN KEY (proposal_id) REFERENCES proposals(id) ON DELETE SET NULL,
    FOREIGN KEY (evidence_file_id) REFERENCES files(id) ON DELETE SET NULL,
    INDEX idx_opportunity_id (opportunity_id),
    INDEX idx_channel (channel)
) ENGINE=InnoDB;

CREATE TABLE awards (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    opportunity_id INT UNSIGNED NOT NULL,
    po_number VARCHAR(100) NULL,
    award_amount DECIMAL(12,2) NULL,
    award_date DATE NULL,
    status ENUM('Pending', 'Awarded', 'Lost', 'Cancelled') DEFAULT 'Pending',
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (opportunity_id) REFERENCES opportunities(id) ON DELETE CASCADE,
    INDEX idx_opportunity_id (opportunity_id),
    INDEX idx_status (status)
) ENGINE=InnoDB;

CREATE TABLE clins (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    award_id INT UNSIGNED NOT NULL,
    clin_code VARCHAR(50) NOT NULL,
    description TEXT NOT NULL,
    amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (award_id) REFERENCES awards(id) ON DELETE CASCADE,
    INDEX idx_award_id (award_id)
) ENGINE=InnoDB;

CREATE TABLE invoices (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    award_id INT UNSIGNED NOT NULL,
    invoice_number VARCHAR(100) NOT NULL,
    portal ENUM('PIEE', 'IPP') NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    status ENUM('Draft', 'Submitted', 'Approved', 'Paid', 'Rejected') DEFAULT 'Draft',
    submitted_at TIMESTAMP NULL,
    paid_at TIMESTAMP NULL,
    file_id INT UNSIGNED NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (award_id) REFERENCES awards(id) ON DELETE CASCADE,
    FOREIGN KEY (file_id) REFERENCES files(id) ON DELETE SET NULL,
    INDEX idx_award_id (award_id),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- Security and settings tables
CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('Admin', 'Capture Manager', 'Proposal Manager', 'Sales Engineer', 'Vendor Manager', 'Accountant', 'Viewer') NOT NULL,
    status ENUM('Active', 'Inactive', 'Suspended') DEFAULT 'Active',
    mfa_secret VARCHAR(32) NULL,
    last_login_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_status (status)
) ENGINE=InnoDB;

CREATE TABLE sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT NOT NULL,
    last_seen TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_last_seen (last_seen)
) ENGINE=InnoDB;

CREATE TABLE settings (
    setting_key VARCHAR(100) PRIMARY KEY,
    setting_value TEXT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE audit_log (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NULL,
    action VARCHAR(100) NOT NULL,
    entity VARCHAR(100) NOT NULL,
    entity_id INT UNSIGNED NULL,
    meta JSON NULL,
    ip_address VARCHAR(45) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_entity (user_id, entity),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Insert default settings
INSERT INTO settings (setting_key, setting_value) VALUES
('sam_api_key', ''),
('sam_use_alpha', '0'),
('default_naics_codes', '["334111", "541512"]'),
('default_set_aside_codes', '["WOSB", "EDWOSB"]'),
('default_posted_days', '30'),
('preferred_agencies', '[]'),
('fetch_attachments', '1'),
('email_smtp', '{"host":"","port":587,"username":"","password":"","encryption":"tls"}'),
('timezone', 'America/Los_Angeles'),
('files_root', '/workspace/govtribe-platform/storage/uploads'),
('site_name', 'GovTribe Platform'),
('maintenance_mode', '0');

-- Create default admin user (password: admin123 - CHANGE IMMEDIATELY)
INSERT INTO users (email, password_hash, role, status) VALUES
('admin@govtribe.local', '$argon2id$v=19$m=65536,t=4,p=3$dGVzdC5zYWx0LmhlcmU$test.hash.here', 'Admin', 'Active');

SET FOREIGN_KEY_CHECKS = 1;