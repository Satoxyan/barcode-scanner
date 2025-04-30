<!-- # **SIPUPS** | e-Book Based Library Information System 📚  

SIPUPS (e-Book Based Library Information System) is a digital platform designed to efficiently manage book lending in the form of e-books. Built using **Filament v3.x** on the **Laravel 11.x** framework, SIPUPS provides a modern and intuitive interface for **administrators, officers, and visitors**.  

- **Administrators** have full control over managing books and user data.  
- **Officers** can only manage the e-book collection.  
- **Visitors** can **read e-books directly** within the system without downloading them, ensuring secure and controlled access.  

Key features include:  
✅ Drag-and-drop functionality to upload book covers and PDFs.  
✅ Authentication using `filament/user`.  
✅ A **responsive admin panel** for seamless management.  

---

## **Installation & Setup**  

### **Prerequisites**  
Before running this project, make sure you have the following tools installed:  

- PHP (latest stable version)  
- Composer  
- Node.js & npm  
- MySQL  
- A web server (Apache, Nginx, etc.)  
- A modern web browser  

### **Clone the Repository**  
```bash
git clone https://github.com/dxnz-id/sipups.git
cd sipups
``` -->

### **Install Dependencies**  
```bash
composer install
npm install
```

### **Environment Configuration**  
1. Copy the `.env.example` file and rename it to `.env`.  
2. Configure the environment file to match your database and application settings.  
3. Link the storage:  
   ```bash
   php artisan storage:link
   ```
4. Generate an application key:  
   ```bash
   php artisan key:generate
   ```

### **Storage Setup**  
```bash
php artisan storage:link
```
### **Database Setup**  
```bash
php artisan migrate
php artisan db:seed
```

### **Start the Application**  
```bash
composer run dev
```

Open your browser and visit:  
```
http://localhost:8000/admin/login
```

### **Default Login Credentials**  
You can log in using the following accounts:  

| Email                 | Password      |
|-----------------------|--------------|
| user@example.com      | labnotes123  |

---
<!-- 
## **Panel Details**  

### **Admin Panel**  
📌 **Dashboard**  
- Total users, books, categories  
- Administrator, officers, visitors  
- 4 newest books displayed in a card interface  

📌 **Books Management**  
- View, edit, delete books  
- Add new books with title, author, publisher, ISBN, category, cover, PDF file, and description  

📌 **Category Management**  
- View, edit, delete categories  
- Create new categories  

📌 **User Management**  
- View, edit (roles), and delete users  

📌 **Profile Settings**  
- Update name, email, password  
- Manage active browser sessions  
- Delete account  

---

### **Officer Panel**  
📌 **Dashboard**  
- Total users, books, categories  
- 4 newest books displayed  

📌 **Books Management**  
- View, edit, delete books  
- Add new books  

📌 **Category Management**  
- View, edit, delete categories  
- Create new categories  

📌 **User List**  

📌 **Profile Settings**  
- Update name, email, password  
- Manage browser sessions  
- Delete account  

---

### **Visitor Panel**  
📌 **Dashboard**  
- Total users, books, categories  
- 4 newest books displayed  

📌 **Books Section**  
- View book list and details  
- Read books directly through the system  

📌 **Profile Settings**  
- Update name, email, password  
- Manage browser sessions  
- Delete account  

---

## **Database Structure**  

### **Books Table**  
| Column        | Type        |
|--------------|------------|
| id           | Integer    |
| title        | String     |
| author       | String     |
| publisher    | String     |
| published_at | Date       |
| isbn         | String     |
| category_id  | Integer    |
| description  | Text       |
| covers       | String     |
| pdf_file     | String     |
| created_at   | Timestamp  |
| updated_at   | Timestamp  |

### **Users Table**  
| Column          | Type        |
|---------------|------------|
| id            | Integer    |
| name          | String     |
| email         | String     |
| roles         | String     |
| email_verified_at | Timestamp |
| password      | String     |
| remember_token | String     |
| created_at    | Timestamp  |
| updated_at    | Timestamp  |

### **Categories Table**  
| Column       | Type        |
|-------------|------------|
| id          | Integer    |
| name        | String     |
| created_at  | Timestamp  |
| updated_at  | Timestamp  |

Other tables include **sessions, permissions, roles, job batches, failed jobs, password resets**, and more.  

---

## **Contributing**  

We welcome contributions! Please check out our [Contribution Guidelines](CONTRIBUTING.md) for more details on how to contribute.  

--- -->
