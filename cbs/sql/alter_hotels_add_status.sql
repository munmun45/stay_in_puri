-- Add is_active column to hotels table
ALTER TABLE hotels ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Status: 1=Active, 0=Inactive';
