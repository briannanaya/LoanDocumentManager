# Loan Document Management System  

## Table of Contents  
- [Project Overview](#project-overview)  
- [Features](#features)
- [Demos](#demos)
- [Technologies Used](#technologies-used)  
- [Learning Objectives](#learning-objectives)  
- [Course Goals](#course-goals)

## Project Overview  
This project was developed as part of a hands-on course on enterprise-level software system development. The course focused on principles and best practices, including:  
- Architectural patterns  
- Database modeling  
- Remote deployment  
- Concurrency management  
- Enterprise data systems  

The **Loan Document Management System** provides a complete solution for managing loan document files by:  
1. **Retrieving loan files**: Using API endpoints to query and request loan documents from an external server simulating employee uploads.  
2. **Storing files**: Organizing and storing retrieved documents in a local file system.  
3. **Database integration**: Automating the process of moving file metadata and content into a database (via phpMyAdmin) using cron jobs.  
4. **Web interface**: Allowing users to search loans by criteria such as type, loan number, or date range. Employees can also manually upload PDF files.  

## Features  
- **Data Management**: Robust handling of loan documents, ensuring consistency and organization.  
- **Cron Job Automation**: Streamlined workflows for importing data into the database.  
- **User-Friendly Interface**: Simple web page for efficient loan searching and retrieval.  
- **Search Capabilities**: Query loans by multiple parameters or retrieve all loans with ease.

## Demos
**Search Functionality**

<img width="605" alt="Screenshot 2024-12-18 at 7 48 01 PM" src="https://github.com/user-attachments/assets/532f56b6-3d56-481b-856f-9ebebd870b1b" />
<img width="605" alt="Screenshot 2024-12-18 at 7 50 08 PM" src="https://github.com/user-attachments/assets/98a19968-9378-43ab-b093-0f744a93da66" />
<img width="605" alt="Screenshot 2024-12-18 at 7 50 22 PM" src="https://github.com/user-attachments/assets/4e92a667-769a-423a-abff-f3173b51eafb" />
<img width="605" alt="Screenshot 2024-12-18 at 7 50 33 PM" src="https://github.com/user-attachments/assets/50d9390d-cbf1-48b2-90cf-96f23c518862" />

**Upload PDF Functionality**

<img width="605" alt="Screenshot 2024-12-18 at 8 21 40 PM" src="https://github.com/user-attachments/assets/22850f78-d1a8-4ac5-9805-1e805e75093c" />
<img width="605" alt="Screenshot 2024-12-18 at 8 21 59 PM" src="https://github.com/user-attachments/assets/6c3300d7-cfd7-4d79-b783-b0d078a972d7" />

## Technologies Used  
This project utilizes the following technologies:  
- **Programming Languages**: PHP, HTML, CSS 
- **Database Management**: MySQL (via phpMyAdmin)  
- **API Integration**: RESTful APIs to retrieve and query loan documents  
- **Automation**: Cron jobs for scheduled tasks  
- **Web Development Tools**:  
  - Nginx Server
- **Infrastructure**:  
  - **AWS EC2 Instance** for deploying and hosting the application remotely  

## Learning Objectives  
Through this project, we gained:  
- Practical experience with **enterprise software development concepts**.  
- A solid understanding of **data coherency across multiple RDBMS**.  
- Skills in creating **middleware** and implementing **job scheduling**.  
- Insights into building **API integrations** and file storage systems.  

This project represents an integration of enterprise-level principles into a practical and functional system, demonstrating skills in software architecture, database management, and web application development.  

