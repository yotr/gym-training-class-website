-- db-update.sql
-- Schema update only (NO inserts / seed data)
-- Use this if you already have an existing database and want to migrate
-- from the old submissions schema (location_area/training_level/no_injuries_confirmed)
-- to the new schema (la7_member/training_background/medical_disclaimer_confirmed/payment_confirmed).

USE gym_group_session;

-- 1) Add new columns (placed after age for readability)
ALTER TABLE submissions
  ADD COLUMN la7_member ENUM('yes', 'no') NOT NULL AFTER age,
  ADD COLUMN training_background ENUM('beginner', 'returning', 'active') NOT NULL AFTER la7_member,
  ADD COLUMN medical_disclaimer_confirmed TINYINT(1) NOT NULL DEFAULT 0 AFTER training_background,
  ADD COLUMN payment_confirmed TINYINT(1) NOT NULL DEFAULT 0 AFTER medical_disclaimer_confirmed;

-- 2) Drop old indexes/columns (old form fields)
-- If any of these statements fail because an index/column doesn't exist,
-- comment the failing line and re-run.
ALTER TABLE submissions
  DROP INDEX idx_submissions_location,
  DROP INDEX idx_submissions_noinjuries,
  DROP COLUMN location_area,
  DROP COLUMN training_level,
  DROP COLUMN no_injuries_confirmed;

-- 3) Add new indexes
ALTER TABLE submissions
  ADD INDEX idx_submissions_medical (medical_disclaimer_confirmed),
  ADD INDEX idx_submissions_payment (payment_confirmed);

