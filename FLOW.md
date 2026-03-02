MINI Library Management System in Laravel using MVC Architecture
Description / Overview
In this activity, students will design and develop a Mini Library Management System using the Model–View–
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
[
    Based on standard library system rules and the scope of a "Mini" project for students, the answer is:

The student can borrow multiple DIFFERENT books.

Reasoning:
Standard Library Rule: In almost all libraries, a single borrower is allowed to borrow only one copy of a specific book title at a time. (e.g., You cannot borrow "Harry Potter 1" twice; you just borrow one copy of it).
Simplicity for your Project: Implementing a "Quantity" field (e.g., a dropdown to select "2 copies" of the same book) adds extra complexity to your code and UI that isn't explicitly required.
How it works in your system:
The Scenario:
A student approaches the desk with 3 books:

Clean Code (1 copy)
Introduction to Algorithms (1 copy)
Clean Code (A second copy - DUPLICATE)
The Admin Process:

The Admin selects the student.
The Admin selects Clean Code. The system checks available_count.
The Admin selects Introduction to Algorithms. The system checks available_count.
If the Admin tries to add Clean Code again: The system should ideally prevent adding the same book ID twice to the same transaction list (or just ignore it).
Database Implication (For your Logic):
Correct Approach (Distinct Books):
Your borrowed_books table will simply store the book_id.
Record 1: Borrowing ID #1 -> Book ID #5 (Clean Code)
Record 2: Borrowing ID #1 -> Book ID #8 (Intro to Algorithms)
Inventory: Clean Code available count - 1.
Incorrect Approach (unless you add Quantity feature):
Storing the same book_id twice in the same transaction.
Record 1: Borrowing ID #1 -> Book ID #5
Record 2: Borrowing ID #1 -> Book ID #5 (This is redundant and messy without a quantity field).
Recommendation:
Design your system so that in the "Borrowing Form," the Admin selects books from a list (checkboxes). Once a book is checked, it is added to the "To Borrow" list. Checking it again removes it. This naturally enforces borrowing multiple DIFFERENT books.
]
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


The Setup: Initial Data
Before any borrowing happens, the Admin logs in (using Laravel Breeze) and sets up the library:

Authors: Admin adds authors: "J.K. Rowling" and "Robert C. Martin".
Books: Admin adds a book: "Harry Potter" (linked to J.K. Rowling, Inventory: 3) and "Clean Code" (linked to Robert C. Martin, Inventory: 2).
Students: Admin creates a profile for a student: "Juan Dela Cruz".
Scenario 1: The Borrowing Process (Day 1)
The Situation: Student Juan Dela Cruz approaches the library counter. He wants to borrow "Harry Potter" and "Clean Code".

From the Student's Perspective:
Juan hands his Student ID card to the librarian.
He places two books on the counter: "Harry Potter" and "Clean Code".
He waits for the librarian to scan/process them.
The librarian tells him: "You have borrowed 2 books. They are due on March 15, 2026."
Juan leaves the library with the books.
From the Admin's Perspective (The System Interaction):
Login: The Admin logs into the system using their email/password.
Navigate: Admin clicks "Transactions" in the sidebar and selects "New Borrowing".
Select Student: Admin types "Juan" in the search box. The system auto-fills Juan's profile from the students table.
Select Books: Admin scans or types the book titles.
Selects "Harry Potter". (System checks: Available = 3. OK.)
Selects "Clean Code". (System checks: Available = 2. OK.)
Set Dates: The system auto-sets Borrow Date = Today and calculates Due Date = +7 days.
Process: Admin clicks the "Borrow Books" button.
System Logic (Behind the Scenes):

borrowings table: A new record is created for Juan.
borrowed_books table: Two records are created linking the books to this transaction.
books table:
"Harry Potter" available_count changes from 3 → 2.
"Clean Code" available_count changes from 2 → 1.
Scenario 2: The Partial Return (Day 4)
The Situation: Juan returns early. He is done reading "Harry Potter" but is still reading "Clean Code".

From the Student's Perspective:
Juan hands only "Harry Potter" to the librarian.
The librarian scans it and says: "Returned successfully. You still have 'Clean Code' due on March 15."
Juan leaves.
From the Admin's Perspective:
Navigate: Admin goes to "Active Transactions" and searches for "Juan".
Process Return: Admin clicks "Process Return" on Juan's active record.
Select Items to Return: The system lists the two borrowed books.
Admin checks the checkbox for "Harry Potter".
Admin leaves "Clean Code" unchecked.
Confirm: Admin clicks "Confirm Return".
System Logic (Behind the Scenes):

books table: "Harry Potter" available_count changes from 2 → 3.
borrowed_books table: The record for "Harry Potter" is marked as "Returned". The record for "Clean Code" remains "Active".
Fine: No fine is calculated yet because the book was returned early.
Scenario 3: The Overdue Return & Fine Computation (Day 12)
The Situation: It is now 5 days past the due date. Juan finally returns "Clean Code".

From the Student's Perspective:
Juan hands "Clean Code" to the librarian.
The librarian frowns and says: "This is 5 days overdue."
The system shows a fine. The librarian says: "Your fine is ₱50."
Juan pays the ₱50. The librarian marks the fine as paid in the system.
From the Admin's Perspective:
Navigate: Admin goes to "Active Transactions". Juan's record is highlighted in Red (indicating overdue).
Process Return: Admin clicks "Process Return".
Select Item: Admin checks the box for "Clean Code".
System Calculation:
The system sees Return Date is 5 days after Due Date.
Formula: ₱10 (fine rate) × 5 (days overdue) × 1 (book) = ₱50.
The screen updates to show: "Fine Due: ₱50".
Confirm: Admin clicks "Confirm Return & Pay Fine".
System Logic (Behind the Scenes):

books table: "Clean Code" available_count changes from 1 → 2.
borrowed_books table: "Clean Code" is marked returned, fine_amount set to 50.
borrowings table: Transaction status changes to "Closed". Total fine logged.
Summary for your Demo
Admin: The "Worker" who logs in, manages inventory, and inputs data into the system.
Student: The "Client" who physically interacts with the books but never touches the software.
System: The "Brain" that tracks dates, calculates inventory, and automates the math for fines.