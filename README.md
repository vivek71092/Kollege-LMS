# 🎓 Kollege LMS - Learning Management System

[![Live Demo](https://img.shields.io/badge/demo-live-brightgreen)](https://kollege.ct.ws/)
[![GitHub](https://img.shields.io/badge/github-repository-blue)](https://github.com/vivek71092/Kollege-LMS)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

> A comprehensive Learning Management System built with PHP, MySQL, and Bootstrap for educational institutions.

**🌐 Live Website**: [https://kollege.ct.ws/](https://kollege.ct.ws/)

---

## 📚 Project Information

**Project Title**: Kollege LMS - Learning Management System  
**Developer**: Vivek Kumar  
**Program**: Bachelor of Computer Applications (BCA) - 6th Semester  
**University**: Chandigarh University

---

## 🎯 Project Overview

Kollege LMS is a feature-rich Learning Management System designed to digitize and streamline academic processes in educational institutions. The platform provides role-based interfaces for Students, Teachers, and Administrators, enabling efficient management of courses, assignments, attendance, grades, and communication.

### Problem Statement

Traditional educational institutions face challenges in managing academic processes manually, including:
- Inefficient paper-based attendance and grade tracking
- Lack of centralized communication between students and teachers
- Difficulty in managing and distributing course materials
- Time-consuming assignment submission and grading processes
- Limited accessibility to academic records and announcements

### Solution

Kollege LMS addresses these challenges by providing:
- **Digital Attendance Management** - Automated tracking and reporting
- **Online Assignment System** - Seamless submission and grading workflow
- **Centralized Content Management** - Easy distribution of notes and study materials
- **Internal Messaging System** - Direct communication channels
- **Real-time Notifications** - Instant updates on grades, assignments, and announcements
- **Comprehensive Analytics** - Data-driven insights for administrators

---

## ✨ Key Features

### 👨‍🎓 Student Portal
- 📊 Personalized dashboard with academic statistics
- 📚 Course enrollment and management
- 📝 Assignment submission with file uploads
- 📅 Attendance records and calendar
- 💯 Grade viewing with detailed feedback
- 📖 Access to course materials and notes
- 💬 Internal messaging system
- 🔔 Real-time notifications
- 👤 Profile management

### 👨‍🏫 Teacher Portal
- 📊 Teaching dashboard with class analytics
- 📚 Course and subject management
- ✍️ Assignment creation and grading
- ✅ Attendance marking interface
- 💯 Grade management system
- 📤 Study material upload
- 👥 Student management
- 📈 Performance reports
- 💬 Student communication

### 👨‍💼 Admin Panel
- 🎛️ System-wide dashboard and analytics
- 👥 Complete user management (CRUD)
- 📚 Course and subject administration
- 📢 Announcement management
- 📊 Comprehensive reporting system
- 📧 Email configuration
- ⚙️ System settings
- 🔒 Audit logs and security
- 💾 Database backup functionality
- 📈 Advanced analytics

---

## 🛠️ Technology Stack

### Backend
- **Language**: PHP 7.4+
- **Database**: MySQL 8.0
- **Architecture**: MVC-inspired pattern
- **ORM**: PDO (PHP Data Objects)

### Frontend
- **Framework**: Bootstrap 5.x
- **JavaScript**: Vanilla JS + jQuery
- **Icons**: Font Awesome 6.x
- **CSS**: Custom responsive stylesheets

### Security
- Password hashing (bcrypt)
- Prepared statements (SQL injection prevention)
- XSS protection (htmlspecialchars)
- CSRF protection
- Session security (HttpOnly, Secure cookies)
- Role-based access control (RBAC)

### Development Tools
- Git version control
- Composer (dependency management)
- Environment configuration (.env)

---

## 📁 Project Structure

```
kollege.ct.ws/
│
├── index.php                 # Homepage entry point
├── config.php               # Database & site configuration
├── functions.php            # Common utility functions
├── error_handler.php        # Error handling & logging
│
├── api/                     # REST-like API endpoints
│   ├── users/              # User management APIs
│   ├── courses/            # Course APIs
│   ├── assignments/        # Assignment APIs
│   ├── attendance/         # Attendance APIs
│   ├── marks/              # Grading APIs
│   ├── messages/           # Messaging APIs
│   └── notifications/      # Notification APIs
│
├── auth/                    # Authentication system
│   ├── login.php
│   ├── register.php
│   ├── logout.php
│   └── reset-password.php
│
├── dashboard/              # Role-based dashboards
│   ├── student/           # Student interface
│   ├── teacher/           # Teacher interface
│   └── admin/             # Admin panel
│
├── classes/                # PHP Classes (OOP)
│   ├── Database.php       # Database singleton
│   ├── User.php
│   ├── Course.php
│   ├── Assignment.php
│   ├── Attendance.php
│   └── Authentication.php
│
├── public/                 # Public assets
│   ├── css/               # Stylesheets
│   ├── js/                # JavaScript files
│   ├── images/            # Images & icons
│   └── uploads/           # User uploaded files
│
├── includes/               # Reusable components
│   ├── header.php
│   ├── footer.php
│   ├── navbar.php
│   └── sidebar.php
│
├── pages/                  # Public pages
│   ├── home.php
│   ├── about.php
│   ├── contact.php
│   └── announcements.php
│
├── migrations/             # Database setup
│   └── create_tables.sql
│
└── utils/                  # Utility functions
    ├── helpers.php
    └── validators.php
```

---

## 💾 Database Schema

The system uses 16 interconnected tables:

### Core Tables
- **Users** - User accounts with role-based access
- **Courses** - Academic programs/degrees
- **Subjects** - Individual classes within courses
- **Enrollments** - Student-subject registrations

### Academic Management
- **Assignments** - Assignment details
- **Submissions** - Student submissions
- **Marks** - Grading system
- **Attendance** - Daily attendance records
- **Notes** - Study materials

### Communication
- **Messages** - Internal messaging
- **Notifications** - System notifications
- **Announcements** - System-wide broadcasts

### System
- **ClassSchedule** - Timetable management
- **Settings** - System configuration
- **AuditLogs** - Security audit trail
- **Certificates** - Course completion certificates

**🔗 ERD and detailed schema available in**: `/migrations/create_tables.sql`

---

## 🚀 Installation & Setup

### Prerequisites
- PHP 7.4 or higher
- MySQL 8.0 or higher
- Apache/Nginx web server
- Composer (optional)

### Quick Start

1. **Clone the repository**
```bash
git clone https://github.com/vivek71092/Kollege-LMS.git
cd Kollege-LMS
```

2. **Database Setup**
```bash
# Import the database schema
mysql -u your_username -p your_database < migrations/create_tables.sql
```

3. **Configure Environment**
```bash
# Copy .env.example to .env
cp .env.example .env

# Edit .env with your database credentials
DB_HOST=localhost
DB_NAME=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
BASE_URL=http://localhost/kollege-lms/
```

4. **Set Permissions**
```bash
# Make upload directories writable
chmod 755 public/uploads/
chmod 755 logs/
```

5. **Access the Application**
```
http://localhost/kollege-lms/
```

---

## 👤 Demo Accounts

Access the live demo at [https://kollege.ct.ws/](https://kollege.ct.ws/) using these credentials:

| Role | Email | Password |
|------|-------|----------|
| **Admin** | admin@kollege.ct.ws | password |
| **Teacher** | teacher@kollege.ct.ws | password |
| **Student** | student@example.com | password |

> ⚠️ **Note**: Demo accounts are for evaluation purposes only. Please do not change passwords.

---

## 📸 Screenshots

### Student Dashboard
![Student Dashboard](public/images/screenshots/student-dashboard.png)
*Personalized dashboard showing courses, assignments, and attendance*

### Teacher Interface
![Teacher Dashboard](public/images/screenshots/teacher-dashboard.png)
*Course management and grading interface*

### Admin Panel
![Admin Panel](public/images/screenshots/admin-dashboard.png)
*Complete system administration and analytics*

---

## 🔒 Security Features

- ✅ Password hashing using bcrypt algorithm
- ✅ Prepared statements for SQL injection prevention
- ✅ XSS protection through input sanitization
- ✅ CSRF token validation on forms
- ✅ Session security with HttpOnly and Secure flags
- ✅ Role-based access control (RBAC)
- ✅ File upload validation and sanitization
- ✅ Audit logging for critical operations
- ✅ Environment-based configuration
- ✅ Error handling with user-friendly messages

---

## 🧪 Testing

The system has been tested for:
- ✅ **Functionality Testing** - All features working as expected
- ✅ **Security Testing** - SQL injection, XSS, CSRF protection verified
- ✅ **Usability Testing** - User-friendly interfaces across roles
- ✅ **Compatibility Testing** - Works on Chrome, Firefox, Safari, Edge
- ✅ **Performance Testing** - Optimized database queries
- ✅ **Responsive Testing** - Mobile and tablet compatibility

---

## 📊 System Requirements

### Server Requirements
- **PHP**: 7.4 or higher
- **MySQL**: 8.0 or higher
- **Web Server**: Apache 2.4+ or Nginx
- **Memory**: 512MB RAM minimum
- **Storage**: 500MB minimum

### Browser Requirements
- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

---

## 🎓 Academic Context

### Project Objectives
1. Develop a comprehensive web-based Learning Management System
2. Implement secure user authentication and authorization
3. Create intuitive role-based interfaces
4. Design and implement a normalized database schema
5. Apply software engineering principles and best practices
6. Deploy the application on a live server

### Learning Outcomes
- Full-stack web development with PHP and MySQL
- Database design and normalization
- Object-oriented programming in PHP
- Security implementation in web applications
- RESTful API design patterns
- Version control with Git
- Deployment and server management

### Technologies Learned
- PHP backend development
- MySQL database management
- Bootstrap responsive design
- JavaScript DOM manipulation
- AJAX for dynamic content
- Session management
- File handling and uploads
- Error handling and logging

---

## 📈 Future Enhancements

### Planned Features
- [ ] Real-time chat using WebSockets
- [ ] Video conferencing integration
- [ ] Mobile application (Android/iOS)
- [ ] AI-powered grade predictions
- [ ] Advanced analytics dashboard
- [ ] Quiz and examination module
- [ ] Discussion forums
- [ ] Calendar integration
- [ ] Email notifications
- [ ] Multi-language support
- [ ] Payment gateway integration
- [ ] Certificate generation
- [ ] Parent portal
- [ ] Library management
- [ ] Transport management

---

## 🤝 Contributing

This is an academic project, but suggestions and feedback are welcome!

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 📄 License

This project is part of an academic submission for Chandigarh University. All rights reserved.

---

## 📞 Contact

**Vivek Kumar**  
BCA 6th Semester, Chandigarh University

- **Email**: vivek71092@example.com
- **GitHub**: [@vivek71092](https://github.com/vivek71092)
- **LinkedIn**: [Vivek Kumar](https://linkedin.com/in/vivek71092)
- **Project Link**: [https://github.com/vivek71092/Kollege-LMS](https://github.com/vivek71092/Kollege-LMS)
- **Live Demo**: [https://kollege.ct.ws/](https://kollege.ct.ws/)

---

## 🙏 Acknowledgments

- **Chandigarh University** - For providing the opportunity and guidance
- **Project Guide** - For mentorship and support throughout the development
- **Faculty Members** - For their valuable feedback and suggestions
- **Open Source Community** - For the tools and libraries used in this project
- **Bootstrap Team** - For the excellent CSS framework
- **Font Awesome** - For the icon library

---

## 📝 Project Documentation

Complete project documentation including:
- System Analysis & Design
- ER Diagrams & DFD
- User Manual
- Test Reports
- API Documentation

**Available in**: `/docs` directory

---

## 📊 Project Statistics

- **Lines of Code**: 12,000+
- **PHP Files**: 210+
- **Database Tables**: 16
- **Features Implemented**: 50+
- **Development Time**: 600+ hours
- **Commit History**: Available on GitHub

---

## 🌟 Project Highlights

✨ **Comprehensive Feature Set** - Complete LMS functionality  
🔒 **Security First** - Multiple layers of protection  
📱 **Responsive Design** - Works on all devices  
⚡ **Performance Optimized** - Fast load times  
🎨 **Clean UI/UX** - Intuitive user interfaces  
📊 **Data-Driven** - Analytics and reporting  
🔧 **Maintainable Code** - Well-structured and documented  
🚀 **Production Ready** - Deployed and live  

---

## 💡 Key Takeaways

This project demonstrates:
- Professional web application development
- Real-world problem-solving
- Software engineering best practices
- Database design and optimization
- Security implementation
- Full development lifecycle experience
- Deployment and maintenance

---

<div align="center">

**⭐ If you find this project useful, please consider giving it a star on GitHub! ⭐**

Made with ❤️ by Vivek Kumar for Chandigarh University BCA Major Project

**© 2024-2025 Kollege LMS. All Rights Reserved.**

</div>Kollege LMS
