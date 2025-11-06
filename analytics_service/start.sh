#!/bin/bash
# Start script for Python Analytics Service

# Check if virtual environment exists
if [ -d "venv" ]; then
    echo "Activating virtual environment..."
    source venv/bin/activate
fi

# Check if .env exists
if [ ! -f ".env" ]; then
    echo "Creating .env from .env.example..."
    cp .env.example .env
fi

# Start the service
echo "Starting Python Analytics Service..."
if command -v gunicorn &> /dev/null; then
    echo "Using Gunicorn (production mode)..."
    gunicorn -w 4 -b 0.0.0.0:5000 app:app
else
    echo "Using Flask development server..."
    python app.py
fi


