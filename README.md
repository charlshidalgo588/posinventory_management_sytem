# POS & Inventory Management System (SIMS)

## Overview

The **Sheems Steel Construction Supply Sales and Inventory Management System (SIMS)** is a **web-based, full-stack application** developed as a **combined academic project** for:

- **CSC 106 – Software Engineering**
- **ITE 18 – Applications Development and Emerging Technologies**

at **Caraga State University (CSU)**.

The project was **based on an existing open-source Laravel POS system** and was **significantly modified, extended, and restructured** to meet specific business requirements and academic objectives.  
Major enhancements include **frontend modernization using Vue.js**, backend refactoring, and full system integration.

## Business Problem

Small retail and construction supply businesses often rely on **manual, paper-based processes** for sales and inventory tracking, which leads to:

- Errors and inconsistencies
- Operational inefficiencies
- Lack of real-time business insights

**SIMS addresses these issues** by providing:

- Automated POS transactions
- Real-time inventory updates
- Centralized sales and inventory reporting

## System Features

- Product, category, and supplier management
- Point-of-Sale (POS) transaction processing
- Automatic inventory deduction per sale
- Inventory activity logs (audit trail)
- Sales and inventory reports
- Dashboard with key business metrics
- Secure user authentication

## System Architecture

The system follows a **client–server architecture**:

- **Frontend:** Vue.js Single Page Application (migrated from Laravel Blade templates)
- **Backend:** Laravel RESTful API
- **Database:** MySQL
- **Authentication:** Laravel Sanctum

The frontend communicates with the backend through **RESTful API endpoints**, enabling improved **modularity, maintainability, and user experience**.

## Technologies Used

- **Frontend:** Vue.js, Axios, Bootstrap / Tailwind CSS
- **Backend:** Laravel (PHP), Laravel Sanctum
- **Database:** MySQL
- **Tools:** VS Code, Composer, npm

## My Role & Contributions

My contributions to this project include:

- Analyzing system requirements and identifying functional gaps
- Migrating the frontend from Laravel Blade templates to a **Vue.js SPA**
- Integrating Vue.js with Laravel API endpoints
- Modifying backend logic to support POS and inventory workflows
- Implementing and validating inventory consistency
- Testing, debugging, and documenting the system

This project demonstrates my ability to **understand, adapt, and improve existing systems**, a common requirement in real-world software development.

## Documentation

Detailed system documentation, diagrams, and screenshots are available in the **`/docs`** folder.

## Future Improvements

- Role-based access control (multi-user support)
- Advanced analytics and reporting dashboards
- Mobile application support
- Barcode scanner integration

## Academic Context & Attribution

This project was developed for **academic and demonstration purposes** at **Caraga State University**.

The initial codebase was sourced from an **open-source Laravel POS project** and was **extensively modified and enhanced** to meet project requirements.
