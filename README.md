# DHCR - Administrative Back-End
The applications currently consists of three sub-apps. 
This is the DHCR Back-End and meant to be used by contributors, moderators and admins. 

## Setup
The front-end project is build using composer. 
See instructions on how to install composer and 
CakePhp specific instructions here: 
https://book.cakephp.org/3.0/en/installation.html

After downloading/cloning the repository, run
```bash
php composer.phar update
```

If not present, create directories logs and tmp:
```
<installation_directory>
    |
    |__app
	|
	|__logs
    	|
    	|__tmp
    	|
    	|__...
```

Run following command to make them writable:
```bash
HTTPDUSER=`ps aux | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
setfacl -R -m u:${HTTPDUSER}:rwx tmp
setfacl -R -d -m u:${HTTPDUSER}:rwx tmp
setfacl -R -m u:${HTTPDUSER}:rwx logs
setfacl -R -d -m u:${HTTPDUSER}:rwx logs
```

## Database
You'll require a dump from the production database. CakePhp connects to most SQL dialects, either MySQL, Postgres, MariaDB etc.

## Configuration
The application reads all configuration constants from environment variables present on the system or docker container. 
Each partial app (Front-End, Back-End, API) can be run standing alone without the environment variables being present, eg. for local development. The required settings are then exported on runtime from an .env file present in the applications config directory. Overriding any already present environmental configuration from local file is prohibited, if the variable `DHCR_ENV` is present and TRUE. 

To connect to databases, provide required access keys, set debug level or interconnect the, use the .env.default file as a template. 
```
<installation_directory>
	|
	|__app
		|
		|__Config
			|
			|__.env.default
```

Make sure the file's contents are interpreted on container startup globally or use the renamed local file for development:
```
<installation_directory>
	|
	|__app
		|
		|__Config
			|
			|__.env
```