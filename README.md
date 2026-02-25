MINI Library Management System in Laravel using MVC Architecture 
Description / Overview 
In this activity, students will design and develop a Mini Library Management System using the Model–View
Controller (MVC) architecture in Laravel. The system must implement authentication using Laravel Breeze, 
manage users, students, books, and authors, and handle borrowing transactions with automated fine 
computation. 
This activity evaluates students' ability to: 
 Apply Laravel MVC architecture properly 
 Implement relational database design 
 Use migrations, models, controllers, routes, and views correctly 
 Apply authentication scaffolding (Breeze) 
 Implement business logic such as borrowing, returning, and fine computation 
 Design a clean and user-friendly interface using a frontend framework 
System Requirements & Features 
1. Authentication 
 Implement authentication using Laravel Breeze. 
 Users can: 
 Login 
 Change password 
 No Role-Based Access Control (RBAC) required. 
2. Student Module 
 Students are NOT required to log in. 
 A student: 
 Can borrow multiple books. 
 Can return all books or partial books. 
 Must be charged a fine of ₱10 per day per book if overdue. 
3. Books Module 
 Must display: 
 List of all books 
 Available inventory count 
 A book: 
 Can have multiple authors. 
 Must track borrowing availability. 
4. Authors Module 
 Authors must be created in this module. 
 A book can be associated with multiple authors. 
 Use proper Many-to-Many relationship. 
5. Business Logic Requirements 
 Borrow date and due date must be recorded. 
 Fine = ₱10 × number of overdue days × number of books. 
 Partial return must update: 
 Book inventory 
 Borrow record 
 Fine computation (if applicable) 
6. Design Requirements 
 Must use a frontend framework (Bootstrap, Tailwind, etc.). 
 Layout must be: 
 Clean 
 Organized 
 Responsive 
 Customized according to system purpose 
 Avoid default plain scaffold output only. 
Technical Expectations 
 Students must properly demonstrate: 
 Migrations with correct foreign keys 
 Eloquent relationships: 
 One-to-Many 
 Many-to-Many 
 Controllers with clean logic 
 Proper validation 
 RESTful routing 
 Organized folder structure 
 Clean code practices

Performance 
Indicator 

1. Proper MVC 
Separation   

Clear 
separation of 
Models, Views, 
Controllers; no 
business logic 
inside views  

2. Routing & 
Controller 
Design 

RESTful 
routing; clean 
and organized 
controllers 

3. Code 
Organization 
& Naming 
Conventions 

Follows 
Laravel 
standards and 
clean code 
principles 

4. Validation & 
Error Handling 

Proper 
validation, no 
system crashes, 
user-friendly 
errors

5. Authentication 
(Laravel 
Breeze)

Fully 
functional 
login & 
password 
management 

6. Database 
Management 
& 
Relationships

Correct 
migrations, 
foreign keys, 
and many-to
many 
relationships 

7. Module 
Implementatio
n (Students, 
Books, 
Authors, 
Transactions) 

All modules 
complete and 
interconnected 

8. Design & 
Responsivenes
s

Clean, 
responsive, 
customized 
design 
