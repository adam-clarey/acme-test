<?php

include_once 'utilities.php';

$db = getDB();
if(!$db) {
  echo $db->lastErrorMsg();
} else {
  echo "Opened database successfully\n";
}

// Create PRODUCTS table.
$sql =<<<EOF
      CREATE TABLE PRODUCTS
      (  ID INT PRIMARY KEY             NOT NULL,
         NAME           VARCHAR(255)    NOT NULL,
         CODE           CHAR(3)         NOT NULL,
         PRICE          DECIMAL         NOT NULL
      );
EOF;

$ret = $db->exec($sql);
if(!$ret){
  echo $db->lastErrorMsg();
} else {
  echo "Table created successfully\n";
}

// Create BASKET table.
$sql =<<<EOF
      CREATE TABLE BASKET
      (
         SESSION        VARCHAR(255)    NOT NULL,
         CODE           CHAR(3)         NOT NULL,
         QUANTITY       INT             NOT NULL,
         PRIMARY KEY (SESSION, CODE)
      );
EOF;

$ret = $db->exec($sql);
if(!$ret){
  echo $db->lastErrorMsg();
} else {
  echo "Table created successfully\n";
}

$sql =<<<EOF
      INSERT INTO PRODUCTS (ID,NAME,CODE,PRICE)
      VALUES (1, 'Red Widget', 'R01', 32.95 );

      INSERT INTO PRODUCTS (ID,NAME,CODE,PRICE)
      VALUES (2, 'Green Widget', 'G01', 24.95 );

      INSERT INTO PRODUCTS (ID,NAME,CODE,PRICE)
      VALUES (3, 'Blue Widget', 'B01', 7.95);

EOF;

$ret = $db->exec($sql);
if(!$ret) {
  echo $db->lastErrorMsg();
} else {
  echo "Records created successfully\n";
}

$db->close();

?>