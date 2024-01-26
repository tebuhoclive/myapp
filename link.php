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

// Handle contact linking to clients
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['client_id']) && isset($_POST['contact_id'])) {
        $client_id = $_POST['client_id'];
        $contact_id = $_POST['contact_id'];
        
        // Insert the linking information into the database
        $sql_link_contact = "INSERT INTO client_contacts (client_id, contact_id) VALUES ('$client_id', '$contact_id')";
        
        if ($conn->query($sql_link_contact) === TRUE) {
            echo "Contact linked to client successfully";
        } else {
            echo "Error linking contact to client: " . $conn->error;
        }
    }
}

// Fetch clients and contacts
$sql_clients = "SELECT * FROM clients";
$result_clients = $conn->query($sql_clients);

$sql_contacts = "SELECT * FROM contacts";
$result_contacts = $conn->query($sql_contacts);

$sql_client_contacts = "SELECT c.client_name, con.contact_name
                        FROM clients c
                        LEFT JOIN client_contacts cc ON c.client_id = cc.client_id
                        LEFT JOIN contacts con ON cc.contact_id = con.contact_id";
$result_client_contacts = $conn->query($sql_client_contacts);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Contacts to Clients</title>
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

        input[type="text"],
        input[type="submit"],
        select {
            padding: 12px;
            margin-bottom: 15px;
            border: none;
            border-radius: 5px;
            width: 100%;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
            width: auto;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        h2,
        h3 {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Link Contacts to Clients</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="client_id">Select Client:</label>
        <select name="client_id" id="client_id">
            <?php
            $result_clients->data_seek(0);
            while ($row = $result_clients->fetch_assoc()) {
                echo "<option value='" . $row["client_id"] . "'>" . $row["client_name"] . "</option>";
            }
            ?>
        </select>

        <label for="contact_id">Select Contact:</label>
        <select name="contact_id" id="contact_id">
            <?php
            $result_contacts->data_seek(0);
            while ($row = $result_contacts->fetch_assoc()) {
                echo "<option value='" . $row["contact_id"] . "'>" . $row["contact_name"] . "</option>";
            }
            ?>
        </select>

        <input type="submit" value="Link Contact to Client">
    </form>

    <h3>List of Clients and Their Contacts:</h3>
    <table>
        <tr>
            <th>Client Name</th>
            <th>Associated Contacts</th>
        </tr>
        <?php
        while ($row = $result_client_contacts->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["client_name"] . "</td>";
            echo "<td>" . $row["contact_name"] . "</td>";
            echo "</tr>";
        }
        ?>
    </table>
</div>
</body>
</html>

<?php
$conn->close();
?>
