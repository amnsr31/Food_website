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

    // Output the bill modal
    echo '<div id="billModal" class="modal fade" role="dialog">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Srivastava Cafe\' - Invoice</h4>
              </div>
              <div class="modal-body">
                <h4>Invoice Number: ' . $invoiceNumber . '</h4>
                <h4>Grand Total: &#8377;' . $gtotal . '/-</h4>
                <h4>Name: ' . $name . '</h4>
                <h4>Address: ' . $address . '</h4>
                <h4>Contact Number: ' . $contactNumber . '</h4>
                <!-- Add more details as needed -->
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="printBill()">Print</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>';
}
?>

<html>

<head>
    <title> Cart | Srivastava Cafe' </title>
    <link rel="stylesheet" type="text/css" href="css/payment.css">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>

    <!-- Add your additional head content here -->
</head>

<body>
    <button onclick="topFunction()" id="myBtn" title="Go to top">
        <span class="glyphicon glyphicon-chevron-up"></span>
    </button>

    <script type="text/javascript">
        window.onscroll = function() {
            scrollFunction()
        };

        function scrollFunction() {
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                document.getElementById("myBtn").style.display = "block";
            } else {
                document.getElementById("myBtn").style.display = "none";
            }
        }

        function topFunction() {
            document.body.scrollTop = 0;
            document.documentElement.scrollTop = 0;
        }

        function printBill() {
            var printWindow = window.open('', '_blank');
            printWindow.document.write('<html><head><title>Srivastava Cafe\' - Invoice</title></head><body>');
            printWindow.document.write(document.getElementById("billModal").innerHTML);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
            printWindow.close();
        }
    </script>

    <!-- Add your navigation code here -->

    <!-- Add the user details form -->
    <form method="post" action="">
        <div class="container">
            <div class="row">
                <div class="col-md-4 col-md-offset-4">
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
                    <button type="submit" class="btn btn-success btn-block">Proceed to Cash On Delivery</button>
                </div>
            </div>
        </div>
    </form>

    <br><br><br><br><br><br>
</body>

</html>