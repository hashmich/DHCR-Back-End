# DHCR - Administrative Back-End
The applications currently consists of three sub-apps. 
This is the DHCR Back-End and meant to be used by contributors, moderators and admins. It currently holds legacy code from the former DHCR prototype, based on the 2.x branch of CakePhp, which should soon be integrated and migrated to the newer main app (Front-End) on CakePhp 3.x. The back-end can be used as stand-alone application, but is pulled in as a submodule from the front-end. 

## PHP Version
CakePhp 2.x is designed to work well with Php versions >=5.6.
The most recent Php version it doesn't have too much problems with is 7.0, while >=7.1 requires you to disable deprecations `E_ALL & E_DEPRECATED & E_USER_DEPRECATED`.
The test framework used with the CakePhp 2.x branch will break for Php versions >7.0.
Check the CakePhp handbook for mor limitations: 
[https://book.cakephp.org/2/en/installation.html]

## Setup
The CakePhp source code is pulled in using composer. 
After downloading/cloning the repository, cd to `app/` and run
```bash
composer update 
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
	|__Vendor
    	|
    	|__composer.json
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