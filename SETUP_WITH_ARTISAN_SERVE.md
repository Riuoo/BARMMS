# Setup Guide for Laravel with `php artisan serve`

This guide is specifically for users running Laravel with `php artisan serve` on `127.0.0.1:8000`.

## Architecture Overview

```
┌─────────────────────┐         HTTP API          ┌─────────────────────┐
│   Laravel (PHP)     │ ────────────────────────► │  Python Analytics   │
│  127.0.0.1:8000     │                           │   localhost:5000     │
│  (artisan serve)    │ ◄──────────────────────── │  (Flask service)    │
└─────────────────────┘      JSON Response        └─────────────────────┘
```

## Setup Steps

### 1. Terminal 1: Start Laravel

```bash
# In your project root (BARMMS directory)
php artisan serve
```

You'll see:
```
INFO  Server running on [http://127.0.0.1:8000]
```

### 2. Terminal 2: Start Python Service

```bash
# Navigate to analytics_service directory
cd analytics_service

# Install dependencies (first time only)
pip install -r requirements.txt

# Start Python service
python app.py
```

You'll see:
```
 * Running on http://0.0.0.0:5000
```

### 3. Configure Laravel `.env`

Add these lines to your `.env` file in the project root:

```env
PYTHON_ANALYTICS_URL=http://localhost:5000
PYTHON_ANALYTICS_TIMEOUT=30
PYTHON_ANALYTICS_ENABLED=true
```

### 4. Configure Python Service (Optional)

Create `.env` file in `analytics_service` directory:

```bash
cd analytics_service
cp .env.example .env
```

The default `.env` should be:
```env
PORT=5000
DEBUG=True
FLASK_ENV=development
```

## Testing

### Test Python Service

Open a new terminal:
```bash
curl http://localhost:5000/health
```

Expected response:
```json
{"status":"healthy","service":"analytics-api"}
```

### Test Laravel Connection

1. Visit: `http://127.0.0.1:8000/admin/clustering`
2. The page should load and use Python service if available
3. Check logs: `tail -f storage/logs/laravel.log`

## Running Both Services

You need **two terminals**:

**Terminal 1:**
```bash
cd C:\xampp\htdocs\BARMMS
php artisan serve
```

**Terminal 2:**
```bash
cd C:\xampp\htdocs\BARMMS\analytics_service
python app.py
```

## Troubleshooting

### Laravel can't connect to Python?

1. **Verify Python service is running:**
   ```bash
   curl http://localhost:5000/health
   ```

2. **Check Laravel logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```
   Look for errors like "Connection refused" or "Timeout"

3. **Verify `.env` configuration:**
   ```env
   PYTHON_ANALYTICS_URL=http://localhost:5000
   ```
   Make sure it's exactly `localhost:5000` (not `127.0.0.1:5000`)

4. **Check if port 5000 is available:**
   ```bash
   # Windows PowerShell
   netstat -ano | findstr :5000
   ```

### Python service not starting?

1. **Check Python version:**
   ```bash
   python --version
   ```
   Need Python 3.8 or higher

2. **Install dependencies:**
   ```bash
   cd analytics_service
   pip install -r requirements.txt
   ```

3. **Check for port conflicts:**
   - Make sure nothing else is using port 5000
   - Change port in `analytics_service/.env` if needed:
     ```env
     PORT=5001
     ```
   - Then update Laravel `.env`:
     ```env
     PYTHON_ANALYTICS_URL=http://localhost:5001
     ```

## Quick Commands

### Start Everything (Windows)

**Terminal 1:**
```powershell
cd C:\xampp\htdocs\BARMMS
php artisan serve
```

**Terminal 2:**
```powershell
cd C:\xampp\htdocs\BARMMS\analytics_service
python app.py
```

### Stop Services

- **Laravel**: Press `Ctrl+C` in Terminal 1
- **Python**: Press `Ctrl+C` in Terminal 2

## Verification Checklist

- [ ] Laravel running on `http://127.0.0.1:8000`
- [ ] Python service running on `http://localhost:5000`
- [ ] `curl http://localhost:5000/health` returns success
- [ ] `.env` file has `PYTHON_ANALYTICS_URL=http://localhost:5000`
- [ ] Can access admin panel: `http://127.0.0.1:8000/admin/clustering`
- [ ] No errors in Laravel logs

## Next Steps

Once both services are running:
1. Visit the Clustering page in your admin panel
2. The system will automatically use Python service if available
3. If Python is unavailable, it falls back to PHP-ML automatically

## Production Deployment

For production, you'll want to:
1. Use a process manager (systemd, supervisor, PM2)
2. Run Python service as a background daemon
3. Use nginx/Apache as reverse proxy
4. Configure proper logging and monitoring

See `PYTHON_ANALYTICS_SETUP.md` for production deployment details.


