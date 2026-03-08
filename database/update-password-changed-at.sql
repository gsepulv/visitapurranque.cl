-- Agregar campo password_changed_at a usuarios (2026-03-08)
ALTER TABLE usuarios ADD COLUMN password_changed_at TIMESTAMP NULL DEFAULT NULL;
UPDATE usuarios SET password_changed_at = NOW() WHERE id = 1;
