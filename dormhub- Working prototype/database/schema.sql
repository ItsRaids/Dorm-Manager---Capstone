-- Dormhub — Sprint 1 schema draft
-- Covers: users, households, household membership, invites.
-- Review as a team before treating this as final — field names,
-- lengths, and constraints are a starting point.
--
-- Run with: mysql -u root -p dormhub < database/schema.sql

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    hashed_password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    -- TODO: email verification fields? (verified_at, verification_token)
);

CREATE TABLE IF NOT EXISTS households (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
    -- TODO: household-level settings? (e.g. timezone, avatar/icon)
);

-- Join table: which users belong to which households, and their role.
CREATE TABLE IF NOT EXISTS household_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    household_id INT NOT NULL,
    user_id INT NOT NULL,
    role ENUM('owner', 'member') NOT NULL DEFAULT 'member',
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (household_id) REFERENCES households(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_membership (household_id, user_id)
    -- TODO: decide — can a user belong to more than one household at once?
    -- If not, enforce a unique constraint on user_id alone instead.
);

CREATE TABLE IF NOT EXISTS invites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    household_id INT NOT NULL,
    invited_by INT NOT NULL,
    code VARCHAR(32) NOT NULL UNIQUE,
    status ENUM('pending', 'accepted', 'expired', 'revoked') NOT NULL DEFAULT 'pending',
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (household_id) REFERENCES households(id) ON DELETE CASCADE,
    FOREIGN KEY (invited_by) REFERENCES users(id) ON DELETE CASCADE
    -- TODO: single-use vs multi-use invites?
    -- TODO: invite by email (tie to a specific address) vs open link/code?
);

-- TODO: seed data for local dev (a test user + household) once
-- registration/household-creation logic is implemented.
