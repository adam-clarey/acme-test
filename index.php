<?php
include_once 'utilities.php';

$db = getDB();
if (!$db) {
  echo $db->lastErrorMsg();
}
$sql = <<<EOF
      SELECT * from PRODUCTS;
EOF;

$ret = $db->query($sql);
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
          href="/css/style.css">
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
            <p>Welcome to Acme Widget Co</p>
          <?php
          while ($row = $ret->fetchArray(SQLITE3_ASSOC)) {
            echo '<div class="col-sm-4 product">';
            echo '<img class="image" src="/images/' . $row['CODE'] . '.png" />';
            echo '<h2>' . noHTML($row['NAME']) . '</h2>';
            echo '<p>' . noHTML($row['CODE']) . '</p>';
            echo '<div class="price">$' . noHTML($row['PRICE']) . '</div>';
            echo '<form action="/cart.php" method="post">
                    <input type="hidden" name="product" value="' . noHTML($row['CODE']) . '">
                    <input type="hidden" name="action" value="add">
                    <input type="submit" class="btn btn-success" value="Add to cart" />
                  </form>';
            echo '</div>';
          }

          $db->close();
          ?>
        </div>
        <div class="col-sm-12">
            <h3>Special offers</h3>
            <p>Buy one red widget, get the second half price!</p>
        </div>
        <div class="col-sm-12">
            <h3>Delivery charges</h3>
            <p>Orders under $50 cost $4.95. For orders under $90, delivery costs
                $2.95. Orders of $90 or more have free delivery.</p>
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

</body>
