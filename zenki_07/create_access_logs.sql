CREATE TABLE `access_logs` (
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `user_agent` TEXT NOT NULL,
    `remote_ip` TEXT NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
);
