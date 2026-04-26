-- Gym Training Class Website
-- Schema + seed data (MySQL 8+ compatible)

CREATE DATABASE IF NOT EXISTS gym_group_session CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE gym_group_session;

-- Admin/users who can log in to the dashboard
CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'staff') NOT NULL DEFAULT 'staff',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_username (username)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Form submissions
CREATE TABLE IF NOT EXISTS submissions (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    package ENUM('member', 'nonmember') NOT NULL,
    full_name VARCHAR(120) NOT NULL,
    whatsapp VARCHAR(40) NOT NULL,
    age TINYINT UNSIGNED NOT NULL,
    la7_member ENUM('yes', 'no') NOT NULL,
    training_background ENUM(
        'beginner',
        'returning',
        'active'
    ) NOT NULL,
    medical_disclaimer_confirmed TINYINT(1) NOT NULL DEFAULT 0,
    payment_confirmed TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_submissions_age (age),
    KEY idx_submissions_medical (medical_disclaimer_confirmed),
    KEY idx_submissions_payment (payment_confirmed),
    KEY idx_submissions_createdat (created_at)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Seed admin user: username=admin, password=admin123
INSERT INTO
    users (
        username,
        password_hash,
        role,
        is_active
    )
VALUES (
        'admin',
        '$2y$10$1klGXoZDQrc0vYzQZ8e.9.iZC4iTLU6mBtqYf38LzvzfKZ.rZDxbe',
        'admin',
        1
    )
ON DUPLICATE KEY UPDATE
    password_hash = VALUES(password_hash),
    role = VALUES(role),
    is_active = VALUES(is_active);

-- Seed 20 example rows (so the dashboard has data immediately)
INSERT INTO
    submissions (
        package,
        full_name,
        whatsapp,
        age,
        la7_member,
        training_background,
        medical_disclaimer_confirmed,
        payment_confirmed,
        created_at
    )
VALUES (
        'member',
        'Mona Hassan',
        '01012345678',
        41,
        'yes',
        'returning',
        1,
        1,
        '2026-04-10 10:15:00'
    ),
    (
        'nonmember',
        'Ahmed Tarek',
        '01122223333',
        45,
        'no',
        'active',
        1,
        1,
        '2026-04-10 11:25:00'
    ),
    (
        'member',
        'Sara Adel',
        '01098765432',
        40,
        'yes',
        'returning',
        0,
        0,
        '2026-04-11 09:05:00'
    ),
    (
        'nonmember',
        'Omar Said',
        '01234567890',
        52,
        'no',
        'active',
        1,
        1,
        '2026-04-11 14:42:00'
    ),
    (
        'member',
        'Nour Ali',
        '01055556666',
        47,
        'yes',
        'beginner',
        1,
        0,
        '2026-04-12 12:01:00'
    ),
    (
        'nonmember',
        'Hany Magdy',
        '01599990000',
        58,
        'returning',
        1,
        'no',
        1,
        '2026-04-12 16:18:00'
    ),
    (
        'member',
        'Dina Mostafa',
        '01033334444',
        44,
        'yes',
        'active',
        0,
        0,
        '2026-04-13 08:44:00'
    ),
    (
        'member',
        'Karim Youssef',
        '01177778888',
        49,
        'yes',
        'returning',
        1,
        1,
        '2026-04-13 19:12:00'
    ),
    (
        'nonmember',
        'Yara Emad',
        '01011112222',
        42,
        'no',
        'beginner',
        1,
        0,
        '2026-04-14 10:31:00'
    ),
    (
        'member',
        'Mohamed Samir',
        '01266667777',
        55,
        'yes',
        'active',
        1,
        1,
        '2026-04-14 13:20:00'
    ),
    (
        'nonmember',
        'Reem Fathy',
        '01044445555',
        46,
        'no',
        'returning',
        1,
        1,
        '2026-04-15 09:58:00'
    ),
    (
        'member',
        'Salma Nabil',
        '01133332222',
        43,
        'yes',
        'beginner',
        0,
        0,
        '2026-04-15 18:06:00'
    ),
    (
        'member',
        'Mostafa Ibrahim',
        '01022220000',
        60,
        'yes',
        'active',
        1,
        1,
        '2026-04-16 07:22:00'
    ),
    (
        'nonmember',
        'Huda Khaled',
        '01211110000',
        48,
        'no',
        'returning',
        1,
        1,
        '2026-04-16 15:49:00'
    ),
    (
        'member',
        'Ehab Abdelrahman',
        '01090909090',
        51,
        'yes',
        'active',
        1,
        1,
        '2026-04-17 11:09:00'
    ),
    (
        'nonmember',
        'Mariam Nasser',
        '01112121212',
        40,
        'returning',
        0,
        'no',
        0,
        '2026-04-17 20:14:00'
    ),
    (
        'member',
        'Rania Ashraf',
        '01067676767',
        53,
        'yes',
        'active',
        1,
        1,
        '2026-04-18 10:02:00'
    ),
    (
        'nonmember',
        'Tamer Soliman',
        '01234343434',
        57,
        'no',
        'beginner',
        1,
        0,
        '2026-04-18 13:37:00'
    ),
    (
        'member',
        'Laila Mahmoud',
        '01056565656',
        45,
        'yes',
        'returning',
        1,
        1,
        '2026-04-19 09:40:00'
    ),
    (
        'nonmember',
        'Ziad Hamdy',
        '01198989898',
        62,
        'no',
        'active',
        1,
        1,
        '2026-04-19 17:55:00'
    );