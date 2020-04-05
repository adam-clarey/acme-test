<?php

require_once __DIR__ . '/vendor/autoload.php';

use \Dallgoot\Yaml;

/*
 * SQLite Database object
 */

class MyDB extends SQLite3 {
  function __construct() {
    $this->open('acme8.db');
  }
}

/*
 * Session ids are used for storing basket information in the database.
 */
function getSessionId() {
  $session_id = session_id();
  if (empty($session_id)) {

    // Need to close straight after to prevent session file locking.
    session_start([
      'read_and_close' => TRUE,
    ]);
  }
  return session_id();
}

/*
 * Get the database connection object.
 */
function getDB() {
  return new MyDB();
}

/**
 * Add a single product to the basket depending on which product the user
 * clicked on.
 *
 * @param $code
 * @return bool
 */
function addProductToBasket($code) {

  // Added validation to ensure users can't post invalid product codes.
  if (!checkProductExists($code)) {
    return FALSE;
  }
  $session_id = getSessionId();

  $db = getDB();
  // Use bindValue to avoid SQL injection.
  $query = $db->prepare("INSERT INTO BASKET (SESSION,CODE, QUANTITY)
                         VALUES (:session, :code, 1)
                         ON CONFLICT(SESSION, CODE) 
                         DO UPDATE SET QUANTITY=QUANTITY+1;");
  $query->bindValue(":session", $session_id, SQLITE3_TEXT);
  $query->bindValue(":code", $code, SQLITE3_TEXT);


  if (($result = $query->execute()) === FALSE) {
    $db->close();
    unset($db);
    return FALSE;
  }
  else {
    $db->close();
    unset($db);
    return TRUE;
  }

}

/**
 * Update the basket quantity for a given product code.
 *
 * @param $code
 * @param $quantity
 * @return bool
 */
function updateBasket($code, $quantity) {

  // Added validation to ensure users can't post invalid product codes.
  if (!checkProductExists($code) || !(is_numeric($quantity) && $quantity >= 0)) {
    return FALSE;
  }
  $db = getDB();
  $session_id = getSessionId();

  $query = $db->prepare("INSERT INTO BASKET (SESSION,CODE, QUANTITY)
                         VALUES (:session, :code, 1)
                         ON CONFLICT(SESSION, CODE) 
                         DO UPDATE SET QUANTITY=:quantity;");
  $query->bindValue(":session", $session_id, SQLITE3_TEXT);
  $query->bindValue(":code", $code, SQLITE3_TEXT);
  $query->bindValue(":quantity", $quantity, SQLITE3_TEXT);


  if (($result = $query->execute()) === FALSE) {
    $db->close();
    unset($db);
    return FALSE;
  }
  else {
    $db->close();
    unset($db);
    return TRUE;
  }
}

/**
 * Helper function to add validation to cart updates.
 *
 * @param $code
 * @return bool
 */
function checkProductExists($code) {
  $db = getDB();
  // Use bindValue to avoid SQL injection.
  $query = $db->prepare("SELECT * from PRODUCTS WHERE CODE = :code");
  $query->bindValue(":code", $code, SQLITE3_TEXT);
  $result = $query->execute();
  if (!empty($result)) {
    return TRUE;
  }
  else {
    return FALSE;
  }
}

/**
 * Used to create the 'cart' link in the header. The value will be updated
 * when the cart quantities change. It will also change colour when there are
 * products in the cart.
 *
 * @return string
 */
function getCartContent() {
  $total = getBasketTotalAmount();
  if ($total > 0) {
    $class = 'active';
  }
  else {
    $class = '';
  }
  return '<a href="/cart.php" id="cart-wrap" class="' . $class . '"><i class="glyphicon glyphicon-shopping-cart"></i> $' . noHTML(number_format((float) $total, 2, '.', '')) . '</a>';

}

/**
 * Helper function to get the total basket amount.
 *
 * @return float
 */
function getBasketTotalAmount() {
  return getBasket('total');
}

/**
 * A multi-purpose function for generating cart contents. The logic for
 * calculating the total amount and cart table are the same so we re-use the
 * same code.
 * Where $type = 'display' it will render the cart contents, where it is 'total'
 * it will only return the numerical amount.
 *
 *
 * @param $type
 * @return float|int|null|string
 */
function getBasket($type) {

  $db = getDB();
  $session_id = getSessionId();
  // Get delivery charge values
  $config = file_get_contents('config.yml');
  $yaml = Yaml::parse($config, 0, FALSE);

  // Get current basket items.
  $query = $db->prepare("
    SELECT b.CODE, p.NAME, QUANTITY,  PRICE
    FROM BASKET b
    INNER JOIN PRODUCTS p on b.CODE = p.CODE
    WHERE SESSION = :session");
  $query->bindValue(":session", $session_id, SQLITE3_TEXT);

  if (($result = $query->execute()) === FALSE) {
    return NULL;
  }

  $red_count = 0;
  $total_amount = 0;
  $output = '';
  while ($row = $result->fetchArray(SQLITE3_ASSOC)) {

    if ($row['QUANTITY'] === 0) {
      continue;
    }
    if ($row['CODE'] === 'R01') {
      $red_count = $row['QUANTITY'];
      $red_price = $row['PRICE'];
    }
    $total_amount += ($row['QUANTITY'] * $row['PRICE']);
    $total_row = round(($row['QUANTITY'] * $row['PRICE']), 2);

    if ($type === 'display') {
      $output .= '<tr>
                    <td>' . noHTML($row['CODE']) . '</td>
                    <td>' . noHTML($row['NAME']) . '</td>
                    <td>
                      <form action="/cart.php" method="POST">
                        <input type="number" name="quantity" class="form-control" value="' . noHTML($row['QUANTITY']) . '" />
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="product" value="' . noHTML($row['CODE']) . '">
                        <input type="submit" class="btn btn-success" value="Update" />
                      </form>
                    </td>
                    <td>$' . noHTML($row['PRICE']) . '</td>
                    <td>$' . noHTML(number_format((float) $total_row, 2, '.', '')) . '</td>
                  </tr>';
    }
  }
  if ($red_count > 1) {
    $discount_amount = 0;
    for ($i = 2; $i <= $red_count; $i += 2) {
      $total_amount -= ($red_price / 2);
      $discount_amount += ($red_price / 2);
    }

    if ($type == 'display') {
      $output .=
        '<tr>
             <td class="no-border" colspan="3"></td>
             <td>Discount:</td>
             <td>-$' . noHTML(number_format((float) round($discount_amount, 2), 2, '.', '')) . '</td>
           </tr>';
    }
  }

  if ($total_amount > 0) {
    $delivery = 0;
    if ($total_amount < 50) {
      $delivery = $yaml->delivery->primary;

    }
    elseif ($total_amount < 90) {
      $delivery = $yaml->delivery->secondary;
    }
    $total_amount += $delivery;

    if ($type === 'display') {
      $output .=
        '<tr>
           <td class="no-border" colspan="3"></td>
           <td>Delivery:</td>
           <td>$' . noHTML($delivery) . '</td>
         </tr>';
      $output .=
        '<tr>
           <td class="no-border" colspan="3"></td>
           <td><strong>Total:</strong></td>
           <td>$' . noHTML(number_format((float) round(round($total_amount, 2, PHP_ROUND_HALF_DOWN), 2), 2, '.', '')) . '</td>
         </tr>';
    }
  }

  $db->close();
  unset($db);
  if ($type === 'display') {
    return $output;
  }
  elseif ($type === 'total') {
    return $total_amount;
  }

}

/**
 * Escape all HTML, JavaScript, and CSS
 *
 * @param string $input The input string
 * @param string $encoding Which character encoding are we using?
 * @return string
 */
function noHTML($input, $encoding = 'UTF-8') {
  return htmlentities($input, ENT_QUOTES | ENT_HTML5, $encoding);
}