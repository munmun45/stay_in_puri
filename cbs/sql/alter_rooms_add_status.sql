-- Add is_active column to rooms table
ALTER TABLE rooms ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Status: 1=Active, 0=Inactive';
