#!/bin/bash
set -e

# Navigate to backend directory
cd /var/www/backend

# Check if vendor directory exists, if not run composer install
if [ ! -d "vendor" ]; then
    echo "Vendor directory not found. Running composer install..."
    composer install --no-interaction --optimize-autoloader
fi

# Start the command passed from Dockerfile CMD in background
"$@" &
SUPERVISOR_PID=$!

# Wait for supervisor to start
sleep 10

# Now start the workers after dependencies are installed
supervisorctl start symfony_worker
supervisorctl start rabbitmq_cosume_worker

# Wait for supervisor process
wait $SUPERVISOR_PID