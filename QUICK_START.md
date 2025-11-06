# Quick Start Guide - Python Analytics

## 🚀 Get Started in 3 Steps

### Step 1: Install Python Dependencies

```bash
cd analytics_service
pip install -r requirements.txt
```

**Or use a virtual environment (recommended):**
```bash
cd analytics_service
python -m venv venv
# Windows:
venv\Scripts\activate
# Linux/Mac:
source venv/bin/activate

pip install -r requirements.txt
```

### Step 2: Start Python Service

```bash
# From analytics_service directory
python app.py
```

You should see:
```
 * Running on http://0.0.0.0:5000
```

### Step 3: Configure Laravel

Add to your Laravel `.env` file (in the root directory):

```env
PYTHON_ANALYTICS_URL=http://localhost:5000
PYTHON_ANALYTICS_TIMEOUT=30
PYTHON_ANALYTICS_ENABLED=true
```

**Note:** Your Laravel app runs on `127.0.0.1:8000` (via `php artisan serve`), and the Python service runs on `localhost:5000`. They are separate services that communicate via HTTP.

## ✅ Verify It's Working

1. **Start Laravel (if not already running):**
   ```bash
   php artisan serve
   ```
   Laravel will run on: `http://127.0.0.1:8000`

2. **Test Python Service:**
   ```bash
   curl http://localhost:5000/health
   ```
   Should return: `{"status":"healthy","service":"analytics-api"}`

3. **Test in Laravel:**
   - Visit your admin panel: `http://127.0.0.1:8000/admin/clustering`
   - Or Decision Tree: `http://127.0.0.1:8000/admin/decision-tree`
   - Check Laravel logs: `storage/logs/laravel.log`
   - Look for "Python service" messages

## 🎯 What Happens Now?

- **Clustering Analysis**: Automatically uses Python (with PHP fallback)
- **Decision Tree Analysis**: Health Risk and Eligibility use Python (with PHP fallback)
- **Automatic Fallback**: If Python service is unavailable, PHP-ML is used automatically

## 🔧 Troubleshooting

### Python service not starting?
- Check Python version: `python --version` (need 3.8+)
- Check if port 5000 is in use
- Check error messages in terminal

### Laravel can't connect?
- Verify Python service is running: `curl http://localhost:5000/health`
- Check `.env` configuration
- Check firewall settings
- Review Laravel logs: `tail -f storage/logs/laravel.log`

### Missing dependencies?
```bash
pip install --upgrade pip
pip install -r requirements.txt --force-reinstall
```

## 📚 Next Steps

- Read `PYTHON_ANALYTICS_SETUP.md` for detailed setup
- Read `PYTHON_MIGRATION_SUMMARY.md` for architecture overview
- Check `analytics_service/README.md` for API documentation

## 💡 Tips

- **Development**: Use `python app.py` (auto-reloads on changes)
- **Production**: Use `gunicorn -w 4 -b 0.0.0.0:5000 app:app`
- **Disable Python**: Set `PYTHON_ANALYTICS_ENABLED=false` in `.env`
- **Change Port**: Set `PORT=5001` in `analytics_service/.env` and update Laravel `.env`

