# NRZ-inventory-system
NRZ Hardware Centralized Inventory System
Author: Wisdom B. Ncube  
DOCX

Institution: Joshua Mqabuko Nkomo Polytechnic  
DOCX

⚠️ Proprietary and Confidential
Copyright (c) 2026 Wisdom B. Ncube. All Rights Reserved.
This repository and its contents are proprietary, closed-source software developed for an academic capstone project.  
DOCX

No License Granted: You are NOT granted permission to copy, modify, distribute, or compile any part of this project.

Academic Integrity: Plagiarism or unauthorized reuse of this codebase for other academic projects or commercial use is strictly prohibited.

Access: Source code visibility is provided strictly for assessment and review purposes.

📖 Project Overview
The NRZ Hardware Centralized Inventory System is a web-based application engineered to resolve operational vulnerabilities within the Hardware Department of the National Railways of Zimbabwe (NRZ). Historically, fragmented manual processes have led to the mishandling of physical IT assets, unauthorized equipment movement, and weak individual accountability.  
DOCX
+ 1

This centralized digital solution completely eliminates paper-based ledgers by establishing a secure platform for recording, assigning, auditing, and reporting on physical IT assets throughout their lifecycle.  
DOCX

✨ Core Modules & Features
Authentication & Access Control: The system utilizes Spatie permission control to enforce strict, granular role-based access. Seeded roles include Administrator, Inventory Manager, Technician, Auditor, and Read-Only users.  
DOCX
+ 1

Comprehensive Asset Tracking: Authorized personnel can manage IT hardware assets by recording specifications, serial numbers, warranty expiry dates, and department or location assignments.  
DOCX

Dynamic QR Code Generation: The system integrates the Simple QrCode package to generate dynamic, signed-URL QR codes for physical hardware labeling. Scanning these codes routes users to a secure asset-information page without requiring a separate mobile application.  
DOCX
+ 1

Maintenance Log Management: Technicians can record hardware faults, track repair progress across pending, in-progress, and resolved statuses, and input detailed resolution notes.  
DOCX

Physical Auditing: Auditors can record physical verification results—noting whether assets are found, missing, damaged, or in the wrong location—alongside timestamped notes.  
DOCX

Gate Pass Issuance: The system tracks the authorized movement of hardware off-premises by generating unique gate pass numbers and recording collector details and release times.  
DOCX

Dashboard & Analytics: A reporting module dynamically calculates live metrics for active devices, expired warranties, maintenance summaries, and asset distributions across departments.  
DOCX

💻 Technology Stack
The application is built on a modern open-source technology stack:  
DOCX

Backend Framework: PHP 8.2 (or later) and Laravel 12.  
DOCX

Administrative Interface: FilamentPHP 5.7.  
DOCX

Database: MySQL compatible relational database managed via Laravel migrations and Eloquent ORM.  
DOCX

Frontend Styling & Tooling: Tailwind CSS, Alpine.js, Axios, and Vite.  
DOCX

Testing: Automated testing suite driven by PHPUnit.  
DOCX

⚙️ System Architecture & Security
To ensure data integrity and security, this system utilizes Laravel's native validation and password hashing. The architecture strictly separates models, resources, tables, and pages. Database uniqueness constraints actively prevent the duplication of critical identifiers like asset tags, MAC addresses, and serial numbers. Public-facing elements are secured using signed URLs to prevent unauthorized modifications to asset routing.  
DOCX
+ 3

🚀 Post-Implementation & Deployment
For a production deployment, the system requires an Apache or Nginx web server. The deployment strategy mandates that debug mode (APP_DEBUG=false) is disabled, HTTPS/SSL certificates are enforced, and application secrets are protected via strict environment file permissions. Routine maintenance requires periodic backups of the MySQL database and continuous monitoring of dependency security patches.  
DOCX
+ 2

For any inquiries regarding this project, please contact the author directly.

Are there any specific installation commands (like composer install or php artisan migrate) you would like me to add to a "Local Setup" section for your assessors to use, or do you want to keep those instructions private?
