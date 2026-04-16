# Hackathon Management System

## 📌 Project Overview

A web-based Hackathon Management System (HackHub) developed using PHP, MySQL, HTML, CSS, and JavaScript. This system helps manage hackathons, teams, submissions, and results efficiently.

---

## 🚀 Features

* User Registration & Login
* Create and Manage Hackathons
* Team Formation
* Team communication
* Project Submission
* Admin Dashboard
* Score Management

---

## 🛠️ Technologies Used

* PHP
* MySQL
* HTML, CSS, JavaScript
* XAMPP (Apache Server)

---

## ⚙️ How to Run the Project

1. Clone the repository

2. Move folder to:
   C:/xampp/htdocs/

3. Start XAMPP (Apache & MySQL)

4. Open phpMyAdmin
   Create database:
   hackathon_db

5. Import the SQL file

6. Run in browser:
   http://localhost/hackathon

---

## 🗄️ Database Schema – HackHub

### Database: `hackhub`

---

### 👤 `students` Table

| Column     | Type         | Description       |
| ---------- | ------------ | ----------------- |
| `id`       | INT (PK, AI) | Student ID        |
| `name`     | VARCHAR(100) | Student full name |
| `email`    | VARCHAR(100) | Email (unique)    |
| `password` | VARCHAR(255) | Hashed password   |
| `college`  | VARCHAR(150) | College name      |
| `branch`   | VARCHAR(100) | Branch/Stream     |

---

### 👥 `teams` Table

| Column       | Type                   | Description        |
| ------------ | ---------------------- | ------------------ |
| `id`         | INT (PK, AI)           | Team ID            |
| `team_name`  | VARCHAR(100)           | Team name          |
| `leader_id`  | INT (FK → students.id) | Team leader        |
| `created_at` | DATETIME               | Team creation date |

---

### 🤝 `team_members` Table

| Column       | Type                   | Description |
| ------------ | ---------------------- | ----------- |
| `id`         | INT (PK, AI)           | Member ID   |
| `team_id`    | INT (FK → teams.id)    | Team        |
| `student_id` | INT (FK → students.id) | Student     |

---

### 🏆 `hackathons` Table

| Column        | Type         | Description     |
| ------------- | ------------ | --------------- |
| `id`          | INT (PK, AI) | Hackathon ID    |
| `title`       | VARCHAR(150) | Hackathon title |
| `description` | TEXT         | Details         |
| `start_date`  | DATE         | Start date      |
| `end_date`    | DATE         | End date        |

---

### 📝 `registrations` Table

| Column         | Type                     | Description     |
| -------------- | ------------------------ | --------------- |
| `id`           | INT (PK, AI)             | Registration ID |
| `team_id`      | INT (FK → teams.id)      | Team            |
| `hackathon_id` | INT (FK → hackathons.id) | Hackathon       |

---

### 🚀 `submissions` Table

| Column          | Type                     | Description     |
| --------------- | ------------------------ | --------------- |
| `id`            | INT (PK, AI)             | Submission ID   |
| `team_id`       | INT (FK → teams.id)      | Team            |
| `hackathon_id`  | INT (FK → hackathons.id) | Hackathon       |
| `project_title` | VARCHAR(150)             | Project name    |
| `project_link`  | VARCHAR(255)             | Link            |
| `submitted_at`  | DATETIME                 | Submission time |

---

### 💬 `messages` Table

| Column      | Type                   | Description     |
| ----------- | ---------------------- | --------------- |
| `id`        | INT (PK, AI)           | Message ID      |
| `team_id`   | INT (FK → teams.id)    | Team            |
| `sender_id` | INT (FK → students.id) | Sender          |
| `message`   | TEXT                   | Message content |
| `sent_at`   | DATETIME               | Time sent       |

---

### 📊 `results` Table

| Column         | Type                     | Description |
| -------------- | ------------------------ | ----------- |
| `id`           | INT (PK, AI)             | Result ID   |
| `team_id`      | INT (FK → teams.id)      | Team        |
| `hackathon_id` | INT (FK → hackathons.id) | Hackathon   |
| `score`        | INT                      | Score       |
| `rank`         | INT                      | Rank        |

---

### ⚙️ `admins` Table

| Column     | Type         | Description     |
| ---------- | ------------ | --------------- |
| `id`       | INT (PK, AI) | Admin ID        |
| `username` | VARCHAR(100) | Username        |
| `password` | VARCHAR(255) | Hashed password |

---

## 🔗 Relationships

* One Student → Many Teams (leader)
* One Team → Many Members
* One Hackathon → Many Teams (via registrations)
* One Team → One Submission
* One Team → One Result


---


## 👨‍💻 Author

Rishi
