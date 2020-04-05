<?php

include_once 'utilities.php';

// Act on form post.
if (!empty($_POST['product']) && $_SERVER['REQUEST_METHOD'] == 'POST') {

  $code = $_POST['product'];
  if ($_POST['action'] === 'add') {
    addProductToBasket($code);
  }
  elseif ($_POST['action'] === 'update') {
    updateBasket($code, $_POST['quantity']);
  }
  unset($_POST['product']);
}

?>

<head>
    <title>Acme Widget Co</title>
    <link rel="shortcut icon" href="/favicon.ico" type="image/png">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet"
          href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u"
          crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet"
          href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css"
          integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp"
          crossorigin="anonymous">

    <!-- custom styles -->
    <link rel="stylesheet"
          href="/style.css">
</head>
<body>
<header class="container">
    <div class="row">
        <div class="col-sm-12">
            <h1 id="page-title"><a href="/">Acme Widget Co</a></h1>
          <?php echo getCartContent(); ?>
        </div>
    </div>

</header>
<section class="container">
    <div class="row">
        <div class="col-sm-12">
            <p>Your shopping backet</p>

            <table id="basket" class="table">
                <thead>
                <th>Code</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total Price</th>
                </thead>
                <tbody>
                <?php
                echo getBasket('display');
                ?>

                </tbody>
            </table>
        </div>
    </div>
</section>

<script
        src="https://code.jquery.com/jquery-2.2.4.min.js"
        integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="
        crossorigin="anonymous"></script>

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
        integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
        crossorigin="anonymous"></script>

<script>
  // This script will prevent the 'Confirm resubmission' popup if refreshing the
  // cart page after adding a product.
  if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
  }
</script>
</body>
