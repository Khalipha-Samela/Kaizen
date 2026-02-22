## 🚀 Kaizen – Focus & Task Management App

Kaizen is a productivity-focused task management web application designed to help users stay organized, focused, and efficient. It goes beyond a simple todo list by allowing users to manage tasks through different stages of progress.

---

## ✨ Features

- 📝 Create, edit, and delete tasks
- 🔄 Move tasks between stages (Todo → In Progress → Done)
- 🏷️ Tag system for organizing tasks
- ⚡ Fast and responsive UI
- 🌙 Clean and modern design
- 💾 Persistent storage using MySQL

---

## 🧠 Concept

The app is inspired by the Japanese philosophy of Kaizen (continuous improvement) — helping users make small, consistent progress toward their goals.

---

## 🛠️ Tech Stack

- **Frontend:** HTML, CSS, JavaScript
- **Backend:** PHP
- **Database:** MySQL

---

## 📁 Project Structure

```
Kaizen
├─ .env                  # Environment variables (DB credentials)
├─ assets
│  ├─ css/style.css      # Styling
│  ├─ images/            # Logos & icons
│  └─ js/script.js       # Frontend logic
├─ config
│  └─ db.php             # Database connection
├─ index.php             # Main app entry point
├─ sql
│  └─ schema.sql         # Database schema
├─ tags                  # Tag-related APIs
│  ├─ add_tag.php
│  └─ get_tags.php
└─ tasks                 # Task-related APIs
   ├─ add_task.php
   ├─ delete_task.php
   ├─ edit_task.php
   ├─ get_task.php
   └─ move_task.php

```

--- 

## ⚙️ Setup Instructions

**1. Clone the Repository**
``` 
git clone https://github.com/your-username/kaizen.git 
cd kaizen
```

**2. Set Up Database**
- Create a MySQL database
- Import the schema:
```
mysql -u root -p kaizene < sql/schema.sql

```

**3. Configure Environment**
- Update your ```.env``` file:
```

DB_HOST=localhost
DB_NAME=kaizen
DB_USER=root
DB_PASS=your_password

```

**4. Run the Project**
- Place the project inside your server directory:
   - XAMPP → ```htdocs```
   -WAMP → ```www```
- Start Apache & MySQL
- Open in browser:
```
http://localhost/Kaizen

```

---

## 📸 Screens / Functionality Overview

- Task creation and editing
- Drag or move tasks across workflow stages
- Tag-based filtering (if implemented in UI)

---


## 🚧 Future Improvements

- 🔐 User authentication system
- 📊 Productivity analytics dashboard
- 🔔 Notifications / reminders
- 📱 Mobile responsiveness improvements
- 🌐 API versioning
- 🎵 Focus Music Integration (Coming Soon!)
  - Play background music while working
  - Built-in focus playlists (Lo-fi, ambient, etc.)
  - Option to integrate with external platforms (e.g. Spotify)
  - Volume control and distraction-free mode

---

## 🤝 Contributing

Contributions are welcome!
1. Fork the repo
2. Create a new branch
3. Commit your changes
4. Submit a pull request

---

## 📄 License

This project is open-source and available under the MIT License.

---

## 💡 Author

Built by **Khalipha Samela**
Aspiring developer focused on building impactful productivity tools.