-- Grant privileges
GRANT ALL PRIVILEGES ON DATABASE insee_city TO insee;

-- Create test database
CREATE DATABASE insee_city_test OWNER insee;

-- Connect to test database
\c insee_city_test
GRANT ALL PRIVILEGES ON DATABASE insee_city_test TO insee;
