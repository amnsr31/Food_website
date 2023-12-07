<?php
session_start();
require 'connection.php';
$conn = Connect();

if (!isset($_SESSION['login_user2'])) {
  header("location: customerlogin.php");
  exit();
}

$username = $_SESSION["login_user2"];

// Handle the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = $_POST["name"];
  $address = $_POST["address"];
  $contactNumber = $_POST["contactNumber"];

  // Save user details to the session for later use
  $_SESSION["user_details"] = [
    "name" => $name,
    "address" => $address,
    "contactNumber" => $contactNumber
  ];

  // Process the order and generate a bill
  $gtotal = 0;
  foreach ($_SESSION["cart"] as $keys => $values) {
    $F_ID = $values["food_id"];
    $foodname = $values["food_name"];
    $quantity = $values["food_quantity"];
    $price =  $values["food_price"];
    $total = ($values["food_quantity"] * $values["food_price"]);
    $R_ID = $values["R_ID"];
    $order_date = date('Y-m-d');

    $gtotal = $gtotal + $total;

    $query = "INSERT INTO ORDERS (F_ID, foodname, price,  quantity, order_date, username, R_ID, name, address, contactNumber) 
                    VALUES ('$F_ID', '$foodname', '$price', '$quantity', '$order_date', '$username', '$R_ID', '$name', '$address', '$contactNumber')";

    $success = $conn->query($query);

    if (!$success) {
?>
      <div class="container">
        <div class="jumbotron">
          <h1>Something went wrong!</h1>
          <p>Try again later.</p>
        </div>
      </div>
<?php
    }
  }

  // Generate a unique invoice number (you can customize this logic)
  $invoiceNumber = uniqid('INV');
  $_SESSION['invoice_details'] = [
    'invoiceNumber' => $invoiceNumber,
    'gtotal' => $gtotal
  ];

  // Output the bill
  echo '<div id="bill-container" class="container mt-5">
            <div class="card">
                <div class="card-header">
                    <h2 class="text-center">Srivastava Cafe</h2>
                    <h3 class="text-center">Order Summary</h3>
                </div>
                <div class="card-body">
                    <h5 class="card-title">Invoice Number: ' . $invoiceNumber . '</h5>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Food Item</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>';
  foreach ($_SESSION["cart"] as $keys => $values) {
    echo '<tr>
                                <td>' . $values["food_name"] . '</td>
                                <td>' . $values["food_quantity"] . '</td>
                                <td>&#8377;' . $values["food_price"] . '</td>
                                <td>&#8377;' . ($values["food_quantity"] * $values["food_price"]) . '</td>
                            </tr>';
  }
  echo '</tbody>
                    </table>
                    <p class="lead">Grand Total: &#8377;' . $gtotal . '/-</p>
                    <h4>Customer Details:</h4>
                    <p>Name: ' . $name . '</p>
                    <p>Address: ' . $address . '</p>
                    <p>Contact Number: ' . $contactNumber . '</p>
                </div>
            </div>
            <br>
            <!-- Return to Home button added here -->
            <a href="index.php" class="btn btn-primary">Return to Home</a>
        </div>';

  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Details | Srivastava Cafe</title>
  <link rel="stylesheet" type="text/css" href="css/payment.css">
  <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
  <script type="text/javascript" src="js/jquery.min.js"></script>
  <script type="text/javascript" src="js/bootstrap.min.js"></script>
  <style>
    /* Add your custom styles here */
  </style>
</head>

<body>
  <div class="container text-center">
    <h1>Order Details</h1>
    <form method="post" action="">
      <div class="form-group">
        <label for="name">Name:</label>
        <input type="text" class="form-control" id="name" name="name" required>
      </div>
      <div class="form-group">
        <label for="address">Address:</label>
        <textarea class="form-control" id="address" name="address" required></textarea>
      </div>
      <div class="form-group">
        <label for="contactNumber">Contact Number:</label>
        <input type="text" class="form-control" id="contactNumber" name="contactNumber" required>
      </div>
      <button type="submit" class="btn btn-success">Proceed to Cash On Delivery</button>
    </form>
  </div>
  <br><br><br><br><br><br>
</body>

</html>