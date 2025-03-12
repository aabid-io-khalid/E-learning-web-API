# Laravel API Documentation

## Introduction
This API serves as the backend for an online learning platform where users can create, manage, and enroll in courses. It provides a structured set of RESTful endpoints to facilitate CRUD operations on courses, categories, and tags.

## Base URL
```
http://localhost::8000/api/v1
```

## Authentication
Some routes may require authentication via Laravel Sanctum. Ensure the authentication token is included in the request headers.

## API Endpoints

### Tags Management
- **Get all tags**
  ```http
  GET /api/v1/tags
  ```
  **Response:** List of all tags.
- **Get a specific tag**
  ```http
  GET /api/v1/tags/{id}
  ```
  **Response:** Details of the tag.
- **Create a new tag**
  ```http
  POST /api/v1/tags
  ```
  **Body:**
  ```json
  {
    "name": "Tag Name"
  }
  ```
- **Update an existing tag**
  ```http
  PUT /api/v1/tags/{id}
  ```
  **Body:**
  ```json
  {
    "name": "Updated Tag Name"
  }
  ```
- **Delete a tag**
  ```http
  DELETE /api/v1/tags/{id}
  ```

### Categories Management
- **Get all categories**
  ```http
  GET /api/v1/categories
  ```
- **Get a specific category**
  ```http
  GET /api/v1/categories/{id}
  ```
- **Create a new category**
  ```http
  POST /api/v1/categories
  ```
  **Body:**
  ```json
  {
    "name": "Category Name"
  }
  ```
- **Update an existing category**
  ```http
  PUT /api/v1/categories/{id}
  ```
  **Body:**
  ```json
  {
    "name": "Updated Category Name"
  }
  ```
- **Delete a category**
  ```http
  DELETE /api/v1/categories/{id}
  ```

### Courses Management
- **Get all courses**
  ```http
  GET /api/v1/courses
  ```
- **Get details of a course**
  ```http
  GET /api/v1/courses/{id}
  ```
- **Create a new course**
  ```http
  POST /api/v1/courses
  ```
  **Body:**
  ```json
  {
    "title": "Course Title",
    "description": "Course Description",
    "category_id": 1
  }
  ```
- **Update a course**
  ```http
  PUT /api/v1/courses/{id}
  ```
  **Body:**
  ```json
  {
    "title": "Updated Course Title",
    "description": "Updated Description",
    "category_id": 2
  }
  ```
- **Delete a course**
  ```http
  DELETE /api/v1/courses/{id}
  ```

## Authentication Routes
- **Get Authenticated User**
  ```http
  GET /api/user
  ```
  Requires an authentication token in the request headers.

## Miscellaneous
- **Test Endpoint**
  ```http
  GET /api/test
  ```
  **Response:** `{ "message": "GraveYard in the downstream!" }`

## Tools & Documentation
- Use **Postman** OR **Insomnia** to test API endpoints.
- Future integration with **Swagger** for API documentation.


## License
This project is licensed under the MIT License.

