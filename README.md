# Installation

Set your server as per normal for serving PHP web pages.
In a live environment only /index.php and /cart.php would be directly served/executable by external requests.
config.yml would also be inacessible to web traffic. 

Run 'composer install' in the root directory.

This will initialise a new SQLite database, create the 2 required table:
1. PRODUCTS
2. BASKETS

as well as populate the PRODUCTS tables.

After installation, the permissions for the install.php will be locked down.

# Usage
Access the test site at /index.php

You will see 3 products as described plus additional promotion and delivery info.

Clicking 'Add to cart' will populate the BASKET table with the current SESSION id used to identify individual user baskets.
On the cart page (/cart.php) You will see all listed products, with delivery, promotion and the total cost.
Each product in the cart can have its quantity updated directly by submitting an integer value >= 0.

