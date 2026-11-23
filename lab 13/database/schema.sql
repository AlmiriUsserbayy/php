CREATE DATABASE IF NOT EXISTS php_course CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE php_course;

CREATE TABLE IF NOT EXISTS items (
    id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code       VARCHAR(20)  NOT NULL UNIQUE,
    name       VARCHAR(150) NOT NULL,
    quantity   INT UNSIGNED NOT NULL DEFAULT 0,
    unit       VARCHAR(10)  NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
               ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT chk_items_unit CHECK (unit IN ('шт', 'кг', 'л', 'м', 'упак'))
) ENGINE=InnoDB;

INSERT IGNORE INTO items (code, name, quantity, unit) VALUES
    ('BOLT-M8',  'Болт M8x40',          1200, 'шт'),
    ('CEM-M500', 'Цемент М500',          350, 'кг'),
    ('PAINT-W',  'Краска белая',          48, 'л'),
    ('CABLE-3',  'Кабель ВВГ 3x2.5',     500, 'м'),
    ('GLOVE-L',  'Перчатки рабочие',      30, 'упак');
