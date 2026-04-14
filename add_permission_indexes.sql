-- Performance optimization for Spatie Permission package
-- Run this in phpMyAdmin or MySQL command line

-- Add indexes to speed up permission queries
ALTER TABLE `model_has_permissions` ADD INDEX `idx_model_type_id` (`model_type`, `model_id`);
ALTER TABLE `model_has_roles` ADD INDEX `idx_model_type_id` (`model_type`, `model_id`);
ALTER TABLE `role_has_permissions` ADD INDEX `idx_role_permission` (`role_id`, `permission_id`);
ALTER TABLE `permissions` ADD INDEX `idx_guard_name` (`guard_name`);
ALTER TABLE `roles` ADD INDEX `idx_guard_name` (`guard_name`);

-- Verify indexes were created
SHOW INDEX FROM `model_has_permissions`;
SHOW INDEX FROM `model_has_roles`;
SHOW INDEX FROM `role_has_permissions`;
