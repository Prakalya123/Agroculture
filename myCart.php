<?php
session_start();
require 'db.php';

// Redirect if not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] == 0) {
    $_SESSION['message'] = "You need to first login to access this page !!!";
    header("Location: Login/error.php");
    exit();
}

$bid = $_SESSION['id'];

// Handle add to cart
if (isset($_GET['pid'])) {
    $pid = intval($_GET['pid']);
    $qty = isset($_GET['qty']) ? max(1, intval($_GET['qty'])) : 5;
    echo "Trying to add product $pid with qty $qty to cart for buyer $bid<br>";


    $check_sql = "SELECT * FROM mycart WHERE bid = ? AND pid = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "ii", $bid, $pid);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);

    if (mysqli_num_rows($check_result) == 0) {
        $insert_sql = "INSERT INTO mycart (bid, pid, quantity) VALUES (?, ?, ?)";
        $insert_stmt = mysqli_prepare($conn, $insert_sql);
        mysqli_stmt_bind_param($insert_stmt, "iii", $bid, $pid, $qty);
        mysqli_stmt_execute($insert_stmt);        
    }  
}

// Handle remove from cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_item'])) {
    $remove_pid = intval($_POST['pid']);

    $delete_sql = "DELETE FROM mycart WHERE bid = ? AND pid = ?";
    $delete_stmt = mysqli_prepare($conn, $delete_sql);
    mysqli_stmt_bind_param($delete_stmt, "ii", $bid, $remove_pid);
    mysqli_stmt_execute($delete_stmt);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AgroCulture: My Cart</title>
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="login.css"/>
    <script src="js/jquery.min.js"></script>
    <script src="js/skel.min.js"></script>
    <script src="js/skel-layers.min.js"></script>
    <script src="js/init.js"></script>
    <noscript>
        <link rel="stylesheet" href="css/skel.css" />
        <link rel="stylesheet" href="css/style.css" />
        <link rel="stylesheet" href="css/style-xlarge.css" />
    </noscript>
</head>
<body>

<?php require 'menu.php'; ?>

<section id="main" class="wrapper style1 align-center">
    <div class="container">
        <h2>My Cart</h2>

        <section id="two" class="wrapper style2 align-center">
            <div class="container">
                <div class="row">

                    <?php
                    $sql = "SELECT * FROM mycart WHERE bid = ?";
                    $stmt = mysqli_prepare($conn, $sql);
                    mysqli_stmt_bind_param($stmt, "i", $bid);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);

                    while ($row = mysqli_fetch_assoc($result)):
                        $pid = $row['pid'];

                        $product_sql = "SELECT * FROM fproduct WHERE pid = ?";
                        $product_stmt = mysqli_prepare($conn, $product_sql);
                        mysqli_stmt_bind_param($product_stmt, "i", $pid);
                        mysqli_stmt_execute($product_stmt);
                        $product_result = mysqli_stmt_get_result($product_stmt);
                        $product = mysqli_fetch_assoc($product_result);

                        $picDestination = "ImagesAs". $product['pimage'];
                    ?>

                    <div class="col-md-4">
                        <section>
                            <h2 class="title" style="color:black;"><?php echo htmlspecialchars($product['product']); ?></h2>
                            <a href="review.php?pid=<?php echo $product['pid']; ?>">
                                <img class="image fit" src="<?php echo $picDestination; ?>" alt="" />
                            </a>
                            <blockquote>
                                Type: <?php echo htmlspecialchars($product['pcat']); ?><br>
                                Price: <?php echo htmlspecialchars($product['price']); ?> /-<br>
                                Quantity: <?php echo htmlspecialchars($row['quantity']); ?>
                            </blockquote>

                            <form method="POST" action="mycart.php" style="margin-top: 10px;">
                                <input type="hidden" name="pid" value="<?php echo $product['pid']; ?>">
                                <button type="submit" name="remove_item" class="btn btn-danger">Remove</button>
                            </form>
                        </section>
                    </div>

                    <?php endwhile; ?>

                </div>
            </div>
        </section>
    </div>
</section>

</body>
</html>