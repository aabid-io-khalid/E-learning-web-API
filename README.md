# Laravel API Documentation - V2

## Introduction
This API serves as the backend for an online learning platform where users can register, authenticate, manage roles, permissions, and enroll in courses. It provides a structured set of RESTful endpoints to facilitate user management, role-based access control, and course enrollment.

## Base URL
`http://localhost:8000/api/v2`

## Authentication
Some routes require authentication via Laravel Sanctum. Ensure the authentication token is included in the request headers.

## API Endpoints

### Authentication Routes
- **Register**  
  `POST /api/v2/register`
  - Registers a new user.

- **Login**  
  `POST /api/v2/login`
  - Logs in a user and returns an authentication token.

- **Logout**  
  `POST /api/v2/logout`
  - Logs out the authenticated user.

## Roles Management
- **Get all roles**  
  `GET /api/v2/roles`
  - Retrieves a list of all roles.

- **Create a role**  
  `POST /api/v2/roles`
  - Creates a new role.

- **Update a role**  
  `PUT /api/v2/roles/{id}`
  - Updates an existing role.

- **Delete a role**  
  `DELETE /api/v2/roles/{id}`
  - Deletes a role.

## Permissions Management
- **Get all permissions**  
  `GET /api/v2/permissions`
  - Retrieves a list of all permissions.

- **Create a permission**  
  `POST /api/v2/permissions`
  - Creates a new permission.

- **Get a specific permission**  
  `GET /api/v2/permissions/{id}`
  - Retrieves details of a specific permission.

- **Update a permission**  
  `PUT /api/v2/permissions/{id}`
  - Updates an existing permission.

- **Delete a permission**  
  `DELETE /api/v2/permissions/{id}`
  - Deletes a permission.

## User Management (Requires Authentication)
- **Get a user profile**  
  `GET /api/v2/users/{user}`
  - Retrieves details of a specific user.

- **Update user profile**  
  `PUT /api/v2/users/edit`
  - Updates the authenticated user's profile.

- **Update any user (Admin Only)**  
  `POST /api/v2/users/{user}`
  - Updates user information (requires `admin` role).

## Student & Mentor Routes
- **Get enrolled courses of a student**  
  `GET /api/v2/students/{id}/courses`
  - Retrieves the courses a student is enrolled in.

- **Get courses taught by a mentor**  
  `GET /api/v2/mentors/{id}/courses`
  - Retrieves the courses assigned to a mentor.

- **Enroll in a course (Student Only)**  
  `POST /api/v2/courses/{course}/enroll`
  - Allows a student to enroll in a course.

- **Get all enrollments in a course**  
  `GET /api/v2/courses/{course}/enrollments`
  - Retrieves all students enrolled in a course.

## Statistics (Admin Only)
- **Get course statistics**  
  `GET /api/v2/stats/courses`
  - Retrieves statistics related to courses.

- **Get category statistics**  
  `GET /api/v2/stats/categories`
  - Retrieves statistics related to categories.

- **Get tag statistics**  
  `GET /api/v2/stats/tags`
  - Retrieves statistics related to tags.

## Tools & Documentation
- Use **Postman** or **Insomnia** to test API endpoints.
- Future integration with **Swagger** for API documentation.

## License
This project is licensed under the **MIT License**.

