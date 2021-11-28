Clone this project

Put this folder in the server folder

Create a database in sql

Change invoice.php file according to yours 

        $servername = "HOST_NAME";
        $username = "SQL_USERNAME";
        $password = "SQL_PASSWORD";
        $db = "DB_NAME";

Create two tables for invoices and details.
Run the follwing queries

===================Query 1 starts here=====================

CREATE TABLE IF NOT EXISTS Invoices (ID int(11) AUTO_INCREMENT,
sub_total FLOAT NOT NULL,
sub_total_wot FLOAT NOT NULL,
PRIMARY KEY  (ID));


===================Query 1 ends here=====================

===================Query 2 starts here=====================

CREATE TABLE IF NOT EXISTS InvoiceDetails (ID int(11) AUTO_INCREMENT,
name varchar(255) NOT NULL,
quantity FLOAT NOT NULL,
unit_price FLOAT NOT NULL,
tax FLOAT NOT NULL,
total FLOAT NOT NULL,
total_wot FLOAT NOT NULL,
invoice_id int,
PRIMARY KEY  (ID),
FOREIGN KEY (invoice_id) REFERENCES Invoices(ID));

===================Query 2 ends here=====================

Open index.html in browser