# Installation

Using composer, run:

composer create-project adam-clarey/acme-widget-co --dev

This will install the project, dependencies and it will also initialise a new SQLite database and create the 2 required table:
1. PRODUCTS
2. BASKETS
                                                            
The install script will then populate the PRODUCTS table.
                                                            
After installation, the permissions for the install.php will be locked down.

Setup your server as per normal for serving PHP web pages. The root index can be found at [current-path]/acme-widget-co/index.php

In a live environment only /index.php and /cart.php would be directly served/executable by external requests.

/config.yml would also be inacessible to web traffic. 

# Usage
Access the test site at [localpath]/index.php

You will see 3 products as described plus additional promotion and delivery info.

Clicking 'Add to cart' will populate the BASKET table with the current SESSION id used to identify individual user baskets.

On the cart page (/cart.php) You will see all listed products, with delivery, promotion and the total cost.

Each product in the cart can have its quantity updated directly by submitting an integer value >= 0.

