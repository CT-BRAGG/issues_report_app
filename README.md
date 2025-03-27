# Issues Reporting Application

## Project Overview

This project is an **Issues Reporting Application** designed to help users report and track issues in a system. The application allows authenticated users to submit, view, and manage issues, as well as communicate with other users regarding reported problems.

### Features:
- **User Authentication**: Allows users to log in using email and password.
- **Issue Ticketing**: Users can create new issue tickets, assign priorities, and track the status of their issues.
- **Comments Section**: Each issue ticket has a comment section where users can add notes and updates regarding the issue.
- **Admin Panel**: Admins have the ability to view all issues and users, and they can manage issue statuses and assign tickets.

## Future Features

### 1. **Registering New Users with SVSU Emails Only**
   - Implement a user registration feature where users can sign up only if they have a valid **SVSU email address** (e.g., `username@svsu.edu`). This will restrict the app to students, faculty, and staff at Saginaw Valley State University (SVSU).
   - A verification email process can be added to confirm the validity of the email address before granting access to the application.

### 2. **Role-Based User Permissions**
   - Allow for multiple user roles, such as **Admin**, **User**, and **Support**. Each role would have specific permissions, such as admins being able to manage users and issue statuses, while regular users can only create and view issues.

### 3. **Priority Levels and Due Dates**
   - Add functionality to set priority levels for issues (e.g., **High**, **Medium**, **Low**) and allow users to set due dates for resolving the issue.
   - Notifications could be sent to users as their issue approaches its due date.

### 4. **Issue Status Tracking**
   - Implement a feature that tracks the status of an issue, such as **Open**, **In Progress**, **Resolved**, and **Closed**. Users should be able to update the status of their tickets, and admins should be able to assign or change statuses.
   - Implement automatic status updates based on certain conditions, such as resolving an issue when all comments are marked as resolved.

### 5. **Issue Search and Filters**
   - Add a search and filtering functionality to allow users to quickly find issues by title, description, status, priority, or date created.
   - Filters can also help sort tickets based on criteria such as most recent or priority level.

### 6. **Email Notifications**
   - Implement email notifications to notify users when a ticket they reported is updated, commented on, or resolved.
   - Admins and users should also receive notifications when they are assigned a new issue or when their issue is escalated.

### 7. **User Profile and Settings**
   - Allow users to update their profile information, such as name, email, and contact number.
   - Add a password reset feature so users can recover access to their accounts in case they forget their password.

### 8. **File Attachments**
   - Users should be able to attach files (e.g., screenshots, documents) to their issues to provide more context or support.

### 9. **Issue Categorization**
   - Implement categories for issues (e.g., **UI Bugs**, **Database Issues**, **Performance**, **Security**, etc.) to allow for better organization and sorting.

### 10. **Ticket Resolution and Escalation**
   - Implement the ability for admins to escalate issues to higher levels of support or management.
   - Create a workflow for resolving tickets, including a feature to mark an issue as "Resolved" once all parties are satisfied.

## Technologies Used

- **PHP**: Backend server-side scripting language for handling user authentication, issue creation, and management.
- **MySQL**: Database for storing user data, issues, and related information.
- **HTML/CSS**: Frontend structure and styling.
- **Git**: Version control to track code changes.
