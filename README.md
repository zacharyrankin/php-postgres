# php-postgres

[![Build Status](https://travis-ci.org/zacharyrankin/php-postgres.svg?branch=master)](https://travis-ci.org/zacharyrankin/php-postgres)

php-postgres is a pure php postgres client designed to help developers understand what is happening when they send a query to Postgres.

## Usage

```php
$conn = new Postgres\Connection('postgres://localhost/zacharyrankin');
$conn->connect();

$sql = <<<SQL
SELECT
    datname,
    usename,
    application_name,
    query
FROM pg_stat_activity;
SQL;
$rows = $conn->query($sql);
```

### Testing

Run this SQL:

```sql
CREATE DATABASE php_postgres_testing;
CREATE USER php_postgres_no_passwd;
CREATE USER php_postgres_plaintext_passwd PASSWORD 'secret';
CREATE USER php_postgres_md5_passwd PASSWORD 'secret';
GRANT ALL PRIVILEGES ON DATABASE php_postgres_testing TO php_postgres_no_passwd;
```

Add this to the pg_hba.conf:

```sql
host  php_postgres_testing  php_postgres_no_passwd        localhost trust
host  php_postgres_testing  php_postgres_plaintext_passwd localhost password
host  php_postgres_testing  php_postgres_md5_passwd       localhost md5
```

### CLI Usage

```bash
bin/php-postgres play
# send startup message
> send LENGTH 3::int16 0::int16 "user" "zacharyrankin" "database" "postgres" NUL
# send startup message helper
> send_startup --user=zacharyrankin --database=postgres
# send query message
> send Q::ident LENGTH "SELECT 1"
# get 100 bytes from the stream
> get 100
# get the next message
> get_message
# get all the messages
> get_messages
```

## DSL

### Constants

#### LENGTH

`LENGTH` will automatically prepend the length of the message and convert it to int32.  For example, these two messages are the same:

- `send Q::ident LENGTH "SELECT 1"`
- `send Q::ident 13::int32 "SELECT 1"`

#### NUL

`NUL` will send a NUL character [https://en.wikipedia.org/wiki/Null_character](https://en.wikipedia.org/wiki/Null_character), which in php can be represented as `\0`.
