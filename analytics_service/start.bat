@echo off
REM Start script for Python Analytics Service (Windows)

REM Check if virtual environment exists
if exist "venv\Scripts\activate.bat" (
    echo Activating virtual environment...
    call venv\Scripts\activate.bat
)

REM Check if .env exists
if not exist ".env" (
    echo Creating .env from .env.example...
    copy .env.example .env
)

REM Start the service
echo Starting Python Analytics Service...
python app.py


