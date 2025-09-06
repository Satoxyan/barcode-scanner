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

### **Database Setup**  
```bash
php artisan migrate --seed
```

### **Optimize**
```bash
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### **Start the Application**  
```bash
composer run dev
```

Open your browser and visit:  
```
http://localhost:8000/
```
