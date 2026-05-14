-- CREATE TABLES (correct order)
CREATE TABLE patients (
    patientID INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(50) NOT NULL,
    email VARCHAR(80) NOT NULL,
    `password` VARCHAR(100) NOT NULL,
    phone VARCHAR(15),
    dateOfBirth DATE
);

CREATE TABLE doctor (
    doctorID INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(50) NOT NULL,
    email VARCHAR(80) NOT NULL,
    `password` VARCHAR(100) NOT NULL,
    specialisation VARCHAR(100)
);

CREATE TABLE admin (
    adminID INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(50) NOT NULL,
    email VARCHAR(80) NOT NULL,
    `password` VARCHAR(100) NOT NULL
);

CREATE TABLE appointment (
    appointmentID INT AUTO_INCREMENT PRIMARY KEY,
    patientID INT,
    doctorID INT,
    `date` DATE,
    time TIME,
    status VARCHAR(20),
    FOREIGN KEY (patientID) REFERENCES patients(patientID),
    FOREIGN KEY (doctorID) REFERENCES doctor(doctorID)
);

CREATE TABLE medical_record (
    recordID INT AUTO_INCREMENT PRIMARY KEY,
    patientID INT,
    doctorID INT,
    diagnosis TEXT,
    treatment TEXT,
    `date` DATETIME,
    FOREIGN KEY (patientID) REFERENCES patients(patientID),
    FOREIGN KEY (doctorID) REFERENCES doctor(doctorID)
);

-- INSERT SAMPLE DATA
INSERT INTO patients (full_name, email, `password`, phone, dateOfBirth)
VALUES ('John Smith', 'john@email.com', 'password123', '0821234567', '1990-05-15');

INSERT INTO patients (full_name, email, `password`, phone, dateOfBirth)
VALUES ('Sarah Johnson', 'sarah@email.com', 'password123', '0837654321', '1985-08-22');

INSERT INTO doctor (full_name, email, `password`, specialisation)
VALUES ('Dr James Wilson', 'james@hospital.com', 'password123', 'Cardiologist');

INSERT INTO doctor (full_name, email, `password`, specialisation)
VALUES ('Dr Emily Brown', 'emily@hospital.com', 'password123', 'General Practitioner');

INSERT INTO admin (full_name, email, `password`)
VALUES ('Admin User', 'admin@hospital.com', 'password123');

INSERT INTO appointment (patientID, doctorID, `date`, time, status)
VALUES (1, 1, '2026-06-01', '09:00:00', 'confirmed');

INSERT INTO appointment (patientID, doctorID, `date`, time, status)
VALUES (2, 2, '2026-06-02', '14:00:00', 'requested');

INSERT INTO medical_record (patientID, doctorID, diagnosis, treatment, `date`)
VALUES (1, 1, 'High blood pressure', 'Prescribed medication and lifestyle changes', '2026-05-01');

INSERT INTO medical_record (patientID, doctorID, diagnosis, treatment, `date`)
VALUES (2, 2, 'Common cold', 'Rest and fluids recommended', '2026-05-03');