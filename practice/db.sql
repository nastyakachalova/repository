CREATE TABLE guests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    room_number INT,
    FOREIGN KEY (room_number) REFERENCES rooms(id)
);

CREATE TABLE employees (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    position VARCHAR(50) NOT NULL
);

CREATE TABLE rooms (
    id INT PRIMARY KEY AUTO_INCREMENT,
    room_number INT NOT NULL,
    capacity INT NOT NULL,
    price DECIMAL(8, 2) NOT NULL
);

CREATE TABLE registration (
    id INT PRIMARY KEY AUTO_INCREMENT,
    guest_id INT,
    checkin DATE NOT NULL,
    checkout DATE NOT NULL,
    FOREIGN KEY (guest_id) REFERENCES guests(id)
);