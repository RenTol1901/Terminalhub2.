CREATE DATABASE IF NOT EXISTS terminalhub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE terminalhub;

SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS trips;
DROP TABLE IF EXISTS vehicles;
DROP TABLE IF EXISTS routes;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS=1;

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','staff','passenger') NOT NULL DEFAULT 'passenger',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE routes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  origin VARCHAR(120) NOT NULL,
  destination VARCHAR(120) NOT NULL,
  estimated_minutes SMALLINT UNSIGNED NOT NULL DEFAULT 60,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY unique_route (origin,destination)
) ENGINE=InnoDB;

CREATE TABLE vehicles (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  operator_name VARCHAR(120) NOT NULL,
  vehicle_type ENUM('Van','Bus','Mini Bus') NOT NULL,
  plate_number VARCHAR(30) NOT NULL UNIQUE,
  capacity SMALLINT UNSIGNED NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE trips (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  route_id INT UNSIGNED NOT NULL,
  vehicle_id INT UNSIGNED NOT NULL,
  departure_time DATETIME NOT NULL,
  arrival_time DATETIME NOT NULL,
  fare DECIMAL(10,2) NOT NULL DEFAULT 0,
  seats_available SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  status ENUM('scheduled','boarding','departed','completed','cancelled') NOT NULL DEFAULT 'scheduled',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_trip_route FOREIGN KEY (route_id) REFERENCES routes(id),
  CONSTRAINT fk_trip_vehicle FOREIGN KEY (vehicle_id) REFERENCES vehicles(id),
  INDEX idx_trip_departure (departure_time,status)
) ENGINE=InnoDB;

CREATE TABLE bookings (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  trip_id INT UNSIGNED NOT NULL,
  seats TINYINT UNSIGNED NOT NULL DEFAULT 1,
  status ENUM('pending','confirmed','rejected','cancelled') NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_booking_user FOREIGN KEY (user_id) REFERENCES users(id),
  CONSTRAINT fk_booking_trip FOREIGN KEY (trip_id) REFERENCES trips(id),
  INDEX idx_booking_user (user_id,created_at)
) ENGINE=InnoDB;

-- Demo password for all three accounts: password
-- Hash is generated with PHP password_hash('password', PASSWORD_DEFAULT) compatible bcrypt hash.
INSERT INTO users (full_name,email,password_hash,role) VALUES
('Terminal Administrator','admin@terminalhub.local','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.','admin'),
('Terminal Staff','staff@terminalhub.local','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.','staff'),
('Sample Passenger','passenger@terminalhub.local','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.','passenger');

INSERT INTO routes (origin,destination,estimated_minutes) VALUES
('Puerto Princesa','Roxas',120),
('Puerto Princesa','El Nido',360),
('Puerto Princesa','San Vicente',240),
('Puerto Princesa','Brooke''s Point',240);

INSERT INTO vehicles (operator_name,vehicle_type,plate_number,capacity) VALUES
('TerminalHub Demo Lines','Van','DEMO-001',12),
('TerminalHub Demo Lines','Bus','DEMO-002',45),
('Palawan Express Transit','Mini Bus','DEMO-003',24);

INSERT INTO trips (route_id,vehicle_id,departure_time,arrival_time,fare,seats_available,status) VALUES
(1,1,DATE_ADD(NOW(), INTERVAL 2 DAY),DATE_ADD(NOW(), INTERVAL 2 DAY) + INTERVAL 2 HOUR,350.00,12,'scheduled'),
(2,2,DATE_ADD(NOW(), INTERVAL 3 DAY),DATE_ADD(NOW(), INTERVAL 3 DAY) + INTERVAL 6 HOUR,650.00,45,'scheduled'),
(3,3,DATE_ADD(NOW(), INTERVAL 1 DAY),DATE_ADD(NOW(), INTERVAL 1 DAY) + INTERVAL 4 HOUR,500.00,24,'scheduled'),
(4,1,DATE_ADD(NOW(), INTERVAL 4 DAY),DATE_ADD(NOW(), INTERVAL 4 DAY) + INTERVAL 4 HOUR,550.00,12,'scheduled');
