<?php
// Include database connection code if needed

// Check if the form is submitted via POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve client ID from the form
    $client_id = $_POST["client_id"];

    // Check if the client ID is not empty
    if (!empty($client_id)) {
        // Connection to the database
        $servername = "sql6.freesqldatabase.com";
        $username = "sql6679647";
        $password = "GHCjFZMGul";
        $dbname = "sql6679647";
        $port = 3306;

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname, $port);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // SQL query to unlink client from contacts
        $sql_unlink_contacts = "DELETE FROM client_contacts WHERE client_id = $client_id";

        // Execute the SQL query
        if ($conn->query($sql_unlink_contacts) === TRUE) {
            // echo "Client unlinked successfully";
        } else {
            // echo "Error unlinking client: " . $conn->error;
        }

        // Close the database connection
        $conn->close();
    } else {
        echo "Client ID is empty";
    }
} else {
    echo "Invalid request method";
}
?>
