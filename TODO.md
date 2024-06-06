
# Laravel CRUD Generator Package
This Laravel package aims to simplify the creation of CRUD (Create, Read, Update, Delete) operations by automatically generating the necessary files and codes. The goal is to speed up the development process and ensure a consistent structure across the application.

## Features Implemented:
- [x] Model Generation: Creates Eloquent models with properties like fillable, attributes, casts and relationships.
- [x] Permissions Generation: Automatically adds permissions to the permissions seeder.

## TODOs:
- [ ] Policy Generation: Generates policy classes for authorization.
- [ ] Form Request Generation: Generates form request classes for validation.
- [ ] Controller Generation: Automatically generates resource controllers with standard CRUD methods.
- [ ] Route Registration: Registers the necessary resource routes in the web.php file.
- [ ] View Generation: Creates basic Blade templates for index, create, edit, and show pages.
- [ ] Factory Generation: Automatically generate factories for test data population.
- [ ] API Resource Support: Implement the generation of API resource controllers and routes.
- [ ] Polymorphic Relationships: Add support for polymorphic relationships in models.
- [ ] Soft Deletes: Add support for soft deletes in models and migrations.
- [ ] Testing Scaffolding: Generate basic PHPUnit test cases for CRUD operations.
- [ ] Documentation Generation: Create basic documentation for generated API endpoints.
- [ ] Multi-tenancy Support: Add support for multi-tenant applications.
- [ ] (I dont know what this is yet, but m keeping it) Customizable Templates: Allow users to provide custom templates for controllers, models, views, etc.

## Contribution Guidelines:
- Code Style: Follow the PSR-12 coding standard. Use the pint rule to ensure consistent code formatting.
- Testing: Ensure all new features are covered by appropriate tests. (Yeah we wish)
- Documentation: Update the package documentation for any new features or changes.
