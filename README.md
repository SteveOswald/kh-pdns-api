# kh-pdns-api
kh-pdns-api provides a PowerDNS compatible API for KeyHelp.

# Usage
- Import the SQL file "sql-scheme.sql" into a MySQL database.
- Adjust the database access data and the base URI in the file "config.php".
- Enter the KeyHelp server in the "server" table.
	- Enter the KeyHelp API key in the column "api_key".
	- If the server should respond to the PowerDNS ID "localhost", a "True" must be set in the column "localhost".
- Enter the available domains in the "domain" table.
- Enter the PowerDNS API keys in the "user" table.
- Control the access of the users to the domains via the table "user_domain".

# Legal
KeyHelp is a trademark of Keyweb AG 
PowerDNS is a trademark of PowerDNS.COM BV
