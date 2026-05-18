-- Event Management & Ticketing (EMT) - Import in phpMyAdmin: choose file or paste
-- Database: emt_db

CREATE DATABASE IF NOT EXISTS emt_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE emt_db;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS events;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('attendee','organiser','admin') NOT NULL DEFAULT 'attendee',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE events (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  organiser_id INT UNSIGNED NOT NULL,
  category_id INT UNSIGNED NOT NULL,
  title VARCHAR(200) NOT NULL,
  description TEXT,
  event_date DATETIME NOT NULL,
  location VARCHAR(200) NOT NULL,
  ticket_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  total_seats INT UNSIGNED NOT NULL,
  available_seats INT UNSIGNED NOT NULL,
  image VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_events_organiser FOREIGN KEY (organiser_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_events_category FOREIGN KEY (category_id) REFERENCES categories(id)
) ENGINE=InnoDB;

CREATE TABLE bookings (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  attendee_id INT UNSIGNED NOT NULL,
  event_id INT UNSIGNED NOT NULL,
  quantity INT UNSIGNED NOT NULL,
  total_price DECIMAL(10,2) NOT NULL,
  booking_code VARCHAR(32) NOT NULL UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_bookings_attendee FOREIGN KEY (attendee_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_bookings_event FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO categories (name) VALUES
  ('Academic'),
  ('Sports'),
  ('Music'),
  ('Workshop');

-- Default password for seeded accounts: admin123 (change after first login)
INSERT INTO users (name, email, password, role) VALUES
  ('System Admin', 'admin@emt.local', '$2y$10$oeWk08H5kJ5Dg4gUzWlp6uImcWgZLBJpBQakYndk1nyqqxByicZqy', 'admin'),
  ('Demo Organiser', 'organiser@emt.local', '$2y$10$oeWk08H5kJ5Dg4gUzWlp6uImcWgZLBJpBQakYndk1nyqqxByicZqy', 'organiser');

INSERT INTO events (organiser_id, category_id, title, description, event_date, location, ticket_price, total_seats, available_seats, image)
VALUES
  (2, 1, 'Campus Tech Talk', 'Intro to web security and PHP basics.', '2026-09-15 14:00:00', 'Main Auditorium', 0.00, 100, 100, NULL),
  (2, 3, 'Spring Concert', 'Student bands live performance.', '2026-05-20 18:00:00', 'Open Air Stage', 15.50, 200, 200, NULL);
