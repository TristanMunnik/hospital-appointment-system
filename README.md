# Hospital System

A role-based hospital management web app built with PHP and MySQL. Patients book appointments and view their medical records, doctors manage their schedule and write records, and admins manage users and appointments.

## Features

| Role    | What they can do |
|---------|------------------|
| Patient | Book appointments with a doctor, view their appointments and medical records |
| Doctor  | View their appointments, add medical records for patients |
| Admin   | Add users (patients, doctors, admins), update appointment status, delete appointments |

## Tech and security decisions

- **PHP 8 + PDO + MySQL** with prepared statements for every query (no SQL injection)
- **Passwords hashed** with `password_hash` / `password_verify` (bcrypt)
- **Role-based access control** via a single `require_role()` helper
- **CSRF tokens** on every form that changes data
- **Output escaping** (`e()` helper) on all user-supplied data to prevent XSS
- **Session ID regenerated on login** to prevent session fixation
- Only `public/` is meant to be web-accessible; config and shared code live in `src/`

## Project structure

```
public/            Web root (the only folder the server should expose)
  index.php        Redirects to the right dashboard or login
  login.php, logout.php
  patient/         dashboard.php, book-appointment.php
  doctor/          dashboard.php, medical-records.php
  admin/           dashboard.php, controls.php
  assets/          css/ and js/
src/               Shared code, not web-accessible
  bootstrap.php    Session, helpers, CSRF, role checks
  config.example.php
database/
  schema.sql       Tables for the app
```

## Setup

Requires PHP 8.1+, MySQL 8+ and Node (only used to run the npm scripts; there are no npm dependencies).

1. Create the database: `npm run db:setup`
2. Copy `src/config.example.php` to `src/config.php` and enter your database credentials
3. Create the first admin account (instructions at the bottom of `database/schema.sql`)
4. Start the app: `npm run dev`
5. Open http://localhost:8000 and log in

## Roadmap

- [ ] Screenshots
- [ ] Automated tests
- [ ] Live demo
