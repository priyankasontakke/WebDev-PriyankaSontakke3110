-- Run this once in phpMyAdmin (or the mysql client) against the smart_farming database.
-- Example: mysql -u root -p smart_farming < soil_tests.sql

CREATE TABLE IF NOT EXISTS soil_tests (
    id              INT UNSIGNED  NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id         INT           NOT NULL,              -- users.id of the logged-in farmer
    field_name      VARCHAR(100)  NOT NULL,
    location        VARCHAR(150)  DEFAULT NULL,
    test_date       DATE          NOT NULL,
    ph              DECIMAL(4,2)  NOT NULL,
    ec              DECIMAL(5,2)  NOT NULL,              -- dS/m
    organic_carbon  DECIMAL(4,2)  NOT NULL,              -- %
    nitrogen        DECIMAL(7,2)  NOT NULL,              -- kg/ha
    phosphorus      DECIMAL(6,2)  NOT NULL,              -- kg/ha
    potassium       DECIMAL(7,2)  NOT NULL,              -- kg/ha
    notes           TEXT          DEFAULT NULL,
    created_at      TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_date (user_id, test_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
