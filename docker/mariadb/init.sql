-- Create test database if not exists
CREATE DATABASE IF NOT EXISTS rambert_test_db;

-- Drop user if it already exists
DROP USER IF EXISTS 'rambert_test_user'@'%';

-- Create user with mysql_native_password
CREATE USER 'rambert_test_user'@'%' IDENTIFIED BY 'rambert_test_password';

-- Grant privileges
GRANT ALL PRIVILEGES ON rambert_test_db.* TO 'rambert_test_user'@'%';
FLUSH PRIVILEGES;


-- Create main database if not exists
CREATE DATABASE IF NOT EXISTS rambert_db;

-- Drop user if it already exists
DROP USER IF EXISTS 'rambert_user'@'%';

-- Create user with mysql_native_password
CREATE USER 'rambert_user'@'%' IDENTIFIED BY 'rambert_password';

-- Grant privileges
GRANT ALL PRIVILEGES ON rambert_db.* TO 'rambert_user'@'%';
FLUSH PRIVILEGES;

-- Switch to the created database
USE rambert_db;