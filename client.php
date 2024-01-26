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

// Handle client form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $client_name = $_POST["client_name"];

    // Count the number of existing clients
    $sql_count_clients = "SELECT COUNT(*) AS num_clients FROM clients";
    $result_count_clients = $conn->query($sql_count_clients);
    $row_count_clients = $result_count_clients->fetch_assoc();
    $num_clients = (int)$row_count_clients['num_clients'] + 1;

    // Extract the first three characters of the client name
    $client_prefix = strtoupper(substr($client_name, 0, 3));

    // Generate client code with leading zeros
    $client_code = $client_prefix . str_pad($num_clients, 3, '0', STR_PAD_LEFT);

    if (!empty($client_name)) {
        $sql = "INSERT INTO clients (client_name, client_code) VALUES ('$client_name', '$client_code')";
        if ($conn->query($sql) === TRUE) {
            echo "New client created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Display list of clients with the number of contacts linked
$sql_clients = "SELECT clients.*, COUNT(client_contacts.contact_id) AS num_contacts_linked
                FROM clients
                LEFT JOIN client_contacts ON clients.client_id = client_contacts.client_id
                GROUP BY clients.client_id";
$result_clients = $conn->query($sql_clients);

// Check if the query was successful
if (!$result_clients) {
    echo "Error: " . $sql_clients . "<br>" . $conn->error;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Clients</title>
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
        input[type="text"], input[type="submit"] {
            padding: 8px 12px;
            margin-bottom: 10px;
            border: none;
            border-radius: 5px;
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
        <h2>Clients</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <h3>Add Clients Form</h3>
            <label for="client_name">Client Name:</label>
            <input type="text" name="client_name" id="client_name">
            <input type="submit" value="Create Client">
        </form>

        <!-- Display List of Clients -->
        <?php
        if ($result_clients->num_rows > 0) {
            echo "<h3>List of Clients:</h3>";
            echo "<table>";
            echo "<tr><th>Client Code</th><th>Name</th><th>No. of Contacts Linked</th><th>Action</th></tr>";
            while ($row = $result_clients->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . str_pad($row["client_code"], 3, '0', STR_PAD_LEFT) . "</td>";
                echo "<td>" . $row["client_name"] . "</td>";
                echo "<td>" . $row["num_contacts_linked"] . "</td>";
                echo "<td><form method='post' action='unlink_client.php'><input type='hidden' name='client_id' value='" . $row["client_id"] . "'><input type='submit' value='Unlink'></form></td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "No client(s) found.";
        }
        ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>
