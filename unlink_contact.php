<?php
// Include the database connection code here if necessary

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['contact_id'])) {
        $contact_id = $_POST['contact_id'];
        
        // Perform the unlinking operation
        // Assuming you have the database connection established

        // SQL query to unlink the contact from clients
        $sql_unlink_contact = "DELETE FROM client_contacts WHERE contact_id = $contact_id";

        // Execute the query
        if ($conn->query($sql_unlink_contact) === TRUE) {
            echo "Contact unlinked successfully";
        } else {
            echo "Error unlinking contact: " . $conn->error;
        }
    }
}
?>
