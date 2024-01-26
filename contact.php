<?php include 'includes/header.php'; ?>

<?php
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

// Handle contact form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $contact_name = $_POST["contact_name"];
    $contact_surname = $_POST["contact_surname"];
    $contact_email = $_POST["contact_email"];

    if (!empty($contact_name) && !empty($contact_surname) && !empty($contact_email)) {
        $sql = "INSERT INTO contacts (contact_name, contact_surname, contact_email) VALUES ('$contact_name', '$contact_surname', '$contact_email')";
        if ($conn->query($sql) === TRUE) {
            echo "New contact created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    // Handle contact unlinking
    if(isset($_POST['unlink_contact_id'])) {
        $unlink_contact_id = $_POST['unlink_contact_id'];
        $sql_unlink_contact = "DELETE FROM client_contacts WHERE contact_id = $unlink_contact_id";
        if ($conn->query($sql_unlink_contact) === TRUE) {
            echo "Contact unlinked successfully";
        } else {
            echo "Error unlinking contact: " . $conn->error;
        }
    }
}

// Display list of contacts with the number of linked clients
$sql_contacts = "SELECT contacts.contact_id, contacts.contact_name, contacts.contact_surname, contacts.contact_email, COUNT(client_contacts.client_id) AS num_clients_linked
                FROM contacts
                LEFT JOIN client_contacts ON contacts.contact_id = client_contacts.contact_id
                GROUP BY contacts.contact_id, contacts.contact_name, contacts.contact_surname, contacts.contact_email";
$result_contacts = $conn->query($sql_contacts);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacts</title>
    <style>
        /* CSS styles */
        body {
            background-color: #091f3d; /* Dark blue background color */
            color: #fff; /* Text color */
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 800px; /* Increased max-width for the container */
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
        }
        label {
            display: block;
            margin-bottom: 10px;
        }
        input[type="text"], input[type="email"], input[type="submit"] {
            padding: 8px 12px;
            margin-bottom: 10px;
            border: none;
            border-radius: 5px;
            width: 100%; /* Make input fields and select elements full width */
            box-sizing: border-box; /* Ensure padding and border are included in the width */
        }
        input[type="submit"] {
            background-color: #4CAF50; /* Green */
            color: white;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049; /* Darker green */
        }
        h2, h3 {
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Contacts</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <h3>Add a new Contact</h3>
            <label for="contact_name">Name:</label>
            <input type="text" name="contact_name" id="contact_name" required><br>
            <label for="contact_surname">Surname:</label>
            <input type="text" name="contact_surname" id="contact_surname" required><br>
            <label for="contact_email">Email:</label>
            <input type="email" name="contact_email" id="contact_email" required pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$"><br>
            <input type="submit" value="Create Contact">
        </form>

        <!-- Display List of Contacts in Table Form -->
<?php
if ($result_contacts->num_rows > 0) {
    echo "<h3>List of Contacts:</h3>";
    echo "<table>";
    echo "<tr><th>Contact Name</th><th>Surname</th><th>Email</th><th>Clients Linked</th><th>Action</th></tr>";
    while ($row = $result_contacts->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["contact_name"] . "</td>";
        echo "<td>" . $row["contact_surname"] . "</td>";
        echo "<td>" . $row["contact_email"] . "</td>";
        echo "<td>" . $row["num_clients_linked"] . "</td>";
        echo "<td><form method='post' action='unlink_contact.php'><input type='hidden' name='contact_id' value='" . $row["contact_id"] . "'><input type='submit' value='Unlink'></form></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No contact(s) found.";
}
?>
    </div>
</body>
</html>

<?php
$conn->close();
?>
