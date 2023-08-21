###### API service for data storage

**Requirements:**

* store data in a database (MySql)
* to fill the database with data from files (csv and xml format), implement it as a console command
* when duplicating data during
* loading from files - update records in the database, determine uniqueness by ID
* The API should have only one command (route/ﬁnd) that will return data
* provide the API command with the ability to select data by criteria: ship-to name, customer email, status
* provide the API command with the ability to specify the record limit and pagination when selecting data
* deploy the project using Docker Compose

###### What you need to know?

The following `bash` scripts are available to you: `build.sh` - runs the Docker build

The `orders` table has the following set of fields: `id`, `purchase_date`, `customer_name`, `customer_email`, `grant_total`, `status`

The following `Docker` containers are used: `Nginx ver. 1.25.2`, `MySQL ver. 8.1`, `PHP-FPM ver. 8.2-fpm`, `PhpMyadmin ver. 5.2.1`

**You can customize as you wish*

###### What to do now?

1. Run the `build.sh` script, which is located at the root of the project and wait for it to complete
2. Now we need to execute the commands inside the `m2e_fpm` container. To do this, log into the container itself using the `docker exec -ti m2e_fpm bash` command and execute the following commands:
   - `php m2e migrate:create-table orders  `- this will create a table
   - `php m2e seed:fill-table orders csv` - the table will be filled with data, you can also specify the last `xml` parameter
3. make sure that the API service is available in your browser at the following links: `127.0.1.1:7080` or `127.0.0.1:7080`, and if you have added entries to the hosts file `api.service-name.loc:7080`
4. you can also use `phpmyadmin` via the same links, just change the port to `7081`

###### Work with API using HTTP protocol (for example in browser):

1. `http://api.service-name.loc:7080/order/find/` or example  `http://127.0.1.1:7080/order/find/`
2. `http://api.service-name.loc:7080/order/find/?limit=2`
3. `http://api.service-name.loc:7080/order/find/?customer_name=Bilbo%20Baggins`
4. `http://api.service-name.loc:7080/order/find/?customer_name=Bilbo%20Baggins&limit=1`
5. `http://api.service-name.loc:7080/order/find/?customer_name=Bilbo%20Baggins&limit=1&offset=2`
6. `http://api.service-name.loc:7080/order/find/?customer_name=Bilbo%20Baggins&status=completed`
7. etc ...

###### CLI - the following commands are available:

1. `php m2e migrate:create-table orders` - this will create a new table
2. `php m2e seed:fill-table orders [csv OR xml]` - this will fill it with data from a csv or xml file located in the `resources` directory
