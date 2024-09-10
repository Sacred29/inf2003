# Environment Setup file
1. Create a .env file
## Sample .env file
```
DB_HOST=localhost
DB_USERNAME=root
DB_PASSWORD=secret
```

## How to access envvar in .php files [Both Local and VM]
2. In all Php files envvars are being used, this line of code needs to be included.
``` r
require_once __DIR__ . '/config.php';
```
To call the environment variables, enter the following in php.

```
$_ENV['DB_HOST']

'''You can test by echoing the variable'''
'''<?php echo $_ENV['DB_HOST']?>'''
````

## Adding Environment Variables to VM in APACHE2 UBUNTU 22.04
open this file.
```
sudo nano /etc/apache2/envvars
```

Add variables like this at the bottom of the file
```
export varName=varValue
```
Sudo Restart apache2
```
sudo systemctl restart apache2
```