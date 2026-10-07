<?php

namespace App\Smark;

use PDO;
use PDOException;

class SQLite {
    private $conn; // Database connection object
    private $table; // Table name for operations
    private $columns = '*'; // Default columns to select
    private $where = []; // Array to hold WHERE conditions
    private $orWhere = []; // Array to hold OR WHERE conditions
    private $bindings = []; // Array to hold values for placeholders in SQL statements
    private $sql; // SQL query string to execute

    // Constructor to initialize the database connection
    public function __construct($dbFile) {
        $this->conn = $this->createConnection($dbFile); // Create a new database connection
    }

    // Method to create a connection to the SQLite database
    private function createConnection($dbFile) {
        try {
            // Create a new PDO instance for the SQLite database
            $conn = new PDO("sqlite:$dbFile");
            // Set the error mode to exceptions
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Handle connection error and output the message
            echo "Error connecting to database: " . $e->getMessage() . "<br>";
            return null; // Return null if connection fails
        }
        return $conn; // Return the database connection
    }

    // Method to create a new table in the database
    public function createTable($table, $columns) {
        // SQL statement to create a table if it doesn't already exist
        $sql = "CREATE TABLE IF NOT EXISTS $table ($columns);";
        try {
            $this->conn->exec($sql); // Execute the SQL statement
        } catch (PDOException $e) {
            // Handle error during table creation and output the message
            echo "Error creating table: " . $e->getMessage() . "<br>";
        }
    }

    // Method to specify the table to be used for subsequent queries
    public function table($table) {
        $this->table = $table; // Set the table name
        return $this; // Enable method chaining
    }

    // Method to specify which columns to select
    public function select($columns = '*') {
        $this->columns = $columns; // Set the columns to select
        return $this; // Enable method chaining
    }

    // Method to add a WHERE clause to the query
    public function where($column, $operator, $value) {
        $this->where[] = "$column $operator ?"; // Add the condition to the where array
        $this->bindings[] = $value; // Add the value to the bindings array
        return $this; // Enable method chaining
    }

    // Method to add an OR WHERE clause to the query
    public function orWhere($column, $operator, $value) {
        $this->orWhere[] = "$column $operator ?"; // Add the condition to the orWhere array
        $this->bindings[] = $value; // Add the value to the bindings array
        return $this; // Enable method chaining
    }

    // Method to execute the SELECT query and return results
    public function get() {
        $this->sql = "SELECT $this->columns FROM $this->table"; // Build the initial SQL query

        // Build the WHERE clause
        $whereClauses = array_merge($this->where, $this->orWhere); // Combine where and orWhere arrays
        if (!empty($whereClauses)) {
            $this->sql .= " WHERE " . implode(' AND ', $this->where); // Add WHERE conditions to the SQL
            if (!empty($this->orWhere)) {
                $this->sql .= " OR " . implode(' OR ', $this->orWhere); // Add OR conditions if any
            }
        }

        try {
            $stmt = $this->conn->prepare($this->sql); // Prepare the SQL statement
            $stmt->execute($this->bindings); // Execute the statement with bindings
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all results as associative arrays
        } catch (PDOException $e) {
            // Handle error during retrieval and output the message
            echo "Error retrieving records: " . $e->getMessage() . "<br>";
            return []; // Return an empty array in case of error
        }
    }

    // Method to insert a new record into the table
    public function insert($data) {
        $columns = implode(", ", array_keys($data)); // Create a string of column names
        $placeholders = ":" . implode(", :", array_keys($data)); // Create a string of placeholders
        $sql = "INSERT INTO $this->table ($columns) VALUES ($placeholders);"; // Build the INSERT SQL statement
        $stmt = $this->conn->prepare($sql); // Prepare the SQL statement
        try {
            $stmt->execute($data); // Execute the statement with the data
        } catch (PDOException $e) {
            // Handle error during insertion and output the message
            echo "Error inserting record: " . $e->getMessage() . "<br>";
        }
    }

    // Method to update existing records in the table
    public function update($data) {
        $setPart = ""; // Initialize the set part of the SQL statement
        foreach ($data as $key => $value) {
            $setPart .= "$key = :$key, "; // Build the SET clause
            $this->bindings[":$key"] = $value; // Bind the value to the placeholder
        }
        $setPart = rtrim($setPart, ", "); // Remove the trailing comma and space
        $this->sql = "UPDATE $this->table SET $setPart"; // Build the UPDATE SQL statement

        // Build WHERE clause
        if (!empty($this->where)) {
            $this->sql .= " WHERE " . implode(' AND ', $this->where); // Add WHERE conditions to the SQL
            if (!empty($this->orWhere)) {
                $this->sql .= " OR " . implode(' OR ', $this->orWhere); // Add OR conditions if any
            }
        }

        $stmt = $this->conn->prepare($this->sql); // Prepare the SQL statement
        try {
            $stmt->execute($this->bindings); // Execute the statement with the bindings
        } catch (PDOException $e) {
            // Handle error during update and output the message
            echo "Error updating records: " . $e->getMessage() . "<br>";
        }
    }

    // Method to delete records from the table
    public function delete() {
        $this->sql = "DELETE FROM $this->table"; // Build the DELETE SQL statement

        // Build WHERE clause
        if (!empty($this->where)) {
            $this->sql .= " WHERE " . implode(' AND ', $this->where); // Add WHERE conditions to the SQL
            if (!empty($this->orWhere)) {
                $this->sql .= " OR " . implode(' OR ', $this->orWhere); // Add OR conditions if any
            }
        }
        try {
            $stmt = $this->conn->prepare($this->sql); // Prepare the SQL statement
            $stmt->execute($this->bindings); // Execute the statement with the bindings
        } catch (PDOException $e) {
            // Handle error during deletion and output the message
            echo "Error deleting records: " . $e->getMessage() . "<br>";
        }
    }

    // Method to count the number of records in the table
    public function count() {
        $this->sql = "SELECT COUNT(*) as count FROM $this->table"; // Build the COUNT SQL statement

        // Build WHERE clause
        if (!empty($this->where)) {
            $this->sql .= " WHERE " . implode(' AND ', $this->where); // Add WHERE conditions to the SQL
            if (!empty($this->orWhere)) {
                $this->sql .= " OR " . implode(' OR ', $this->orWhere); // Add OR conditions if any
            }
        }

        try {
            $stmt = $this->conn->prepare($this->sql); // Prepare the SQL statement
            $stmt->execute($this->bindings); // Execute the statement with the bindings
            $result = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch the count result
            return $result['count']; // Return the count value
        } catch (PDOException $e) {
            // Handle error during counting and output the message
            echo "Error counting records: " . $e->getMessage() . "<br>";
            return 0; // Return 0 in case of error
        }
    }

    // Method to pluck specific columns from the table
    public function pluck(...$columns) {
        $columnsString = implode(", ", $columns); // Create a string of columns to select
        $this->sql = "SELECT $columnsString FROM $this->table"; // Build the SELECT SQL statement

        // Build WHERE clause
        if (!empty($this->where)) {
            $this->sql .= " WHERE " . implode(' AND ', $this->where); // Add WHERE conditions to the SQL
            if (!empty($this->orWhere)) {
                $this->sql .= " OR " . implode(' OR ', $this->orWhere); // Add OR conditions if any
            }
        }

        try {
            $stmt = $this->conn->prepare($this->sql); // Prepare the SQL statement
            $stmt->execute($this->bindings); // Execute the statement with the bindings
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all rows as associative arrays
        } catch (PDOException $e) {
            // Handle error during plucking and output the message
            echo "Error plucking columns: " . $e->getMessage() . "<br>";
            return []; // Return an empty array in case of error
        }
    }

    // Method to retrieve the latest (n) records from the table
    public function latest($n, $orderBy = 'id') {
        $this->sql = "SELECT * FROM $this->table ORDER BY $orderBy DESC LIMIT :limit"; // Build the SQL for latest records

        // Bind the limit parameter
        $this->bindings[':limit'] = $n; // Set the limit binding

        // Build WHERE clause
        if (!empty($this->where)) {
            $this->sql .= " WHERE " . implode(' AND ', $this->where); // Add WHERE conditions to the SQL
            if (!empty($this->orWhere)) {
                $this->sql .= " OR " . implode(' OR ', $this->orWhere); // Add OR conditions if any
            }
        }

        try {
            $stmt = $this->conn->prepare($this->sql); // Prepare the SQL statement
            $stmt->execute($this->bindings); // Execute the statement with the bindings
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all rows as associative arrays
        } catch (PDOException $e) {
            // Handle error during retrieval of latest records and output the message
            echo "Error retrieving latest records: " . $e->getMessage() . "<br>";
            return []; // Return an empty array in case of error
        }
    }

    // Method to convert the results of a SELECT query to an array
    public function toArray() {
        $this->sql = "SELECT * FROM $this->table"; // Build the SELECT SQL statement

        // Build WHERE clause
        if (!empty($this->where)) {
            $this->sql .= " WHERE " . implode(' AND ', $this->where); // Add WHERE conditions to the SQL
            if (!empty($this->orWhere)) {
                $this->sql .= " OR " . implode(' OR ', $this->orWhere); // Add OR conditions if any
            }
        }

        try {
            $stmt = $this->conn->prepare($this->sql); // Prepare the SQL statement
            $stmt->execute($this->bindings); // Execute the statement with the bindings
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all rows as associative arrays
        } catch (PDOException $e) {
            // Handle error during conversion to array and output the message
            echo "Error converting results to array: " . $e->getMessage() . "<br>";
            return []; // Return an empty array in case of error
        }
    }

    // Method to add an ORDER BY clause to the query
    public function orderBy($column, $direction = 'ASC') {
        // Ensure the direction is either ASC or DESC
        $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC'; // Normalize direction to uppercase

        // Add the ORDER BY clause to the SQL
        if (strpos($this->sql, 'ORDER BY') === false) {
            $this->sql .= " ORDER BY $column $direction"; // Append ORDER BY if it doesn't exist
        } else {
            // If ORDER BY already exists, append to it
            $this->sql .= ", $column $direction"; // Add additional ORDER BY conditions
        }

        return $this; // Enable method chaining
    }

    // Method to execute a raw SQL query
    public function query($sql, $bindings = []) {
        try {
            // Prepare the SQL statement
            $stmt = $this->conn->prepare($sql);

            // Execute the statement with the bindings
            $stmt->execute($bindings);

            // Determine if it's a SELECT statement
            if (stripos(trim($sql), 'SELECT') === 0) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch results for SELECT queries
            } elseif (stripos(trim($sql), 'INSERT') === 0) {
                return $this->conn->lastInsertId(); // Return the last inserted ID for INSERT queries
            } elseif (stripos(trim($sql), 'UPDATE') === 0 || stripos(trim($sql), 'DELETE') === 0) {
                return $stmt->rowCount(); // Return the number of affected rows for UPDATE and DELETE queries
            }
        } catch (PDOException $e) {
            // Handle error during query execution and output the message
            echo "Error executing query: " . $e->getMessage() . "<br>";
            return false; // Return false in case of error
        }
    }

    // Method to clear the state of the query builder
    public function clear() {
        $this->table = null; // Reset the table name
        $this->columns = '*'; // Reset to default columns
        $this->where = []; // Clear WHERE conditions
        $this->orWhere = []; // Clear OR WHERE conditions
        $this->bindings = []; // Clear bindings
    }

    // Method to close the database connection
    public function closeConnection() {
        $this->conn = null; // Set the connection to null to close it
        echo "Connection closed.<br>"; // Output confirmation of closure
    }
}
