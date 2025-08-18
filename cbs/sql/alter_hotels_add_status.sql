-- Add is_active column to hotels table with default 0 (Inactive)
ALTER TABLE hotels ADD COLUMN IF NOT EXISTS is_active TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Status: 1=Active, 0=Inactive';

-- If the column already exists with a different default, ensure it defaults to 0
ALTER TABLE hotels MODIFY COLUMN is_active TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Status: 1=Active, 0=Inactive';
