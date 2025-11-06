# Python Analytics Service Setup Guide

This guide explains how to set up and use the Python analytics microservice for BARMMS.

## Overview

The Python analytics service provides advanced machine learning capabilities using scikit-learn, XGBoost, and other Python libraries. It runs as a separate microservice that communicates with Laravel via HTTP API.

## Features

- **Advanced Clustering**: K-Means with optimal K detection (elbow method, silhouette score, gap statistic)
- **Hierarchical Clustering**: Agglomerative clustering with multiple linkage methods
- **Decision Trees**: Standard, Random Forest, and XGBoost models
- **Health Risk Analysis**: ML-based health risk prediction
- **Service Eligibility**: Automated eligibility determination
- **Demographic Analysis**: Comprehensive demographic clustering

## Prerequisites

- Python 3.8 or higher
- pip (Python package manager)
- Laravel application (already set up)

## Installation

### 1. Install Python Dependencies

Navigate to the `analytics_service` directory:

```bash
cd analytics_service
pip install -r requirements.txt
```

Or use a virtual environment (recommended):

```bash
python -m venv venv
source venv/bin/activate  # On Windows: venv\Scripts\activate
pip install -r requirements.txt
```

### 2. Configure Environment

Create a `.env` file in the `analytics_service` directory:

```bash
cp .env.example .env
```

Edit `.env` and set:

```env
PORT=5000
DEBUG=True
FLASK_ENV=development
```

### 3. Configure Laravel

Add to your Laravel `.env` file:

```env
PYTHON_ANALYTICS_URL=http://localhost:5000
PYTHON_ANALYTICS_TIMEOUT=30
PYTHON_ANALYTICS_ENABLED=true
```

### 4. Start the Python Service

**Development mode:**
```bash
cd analytics_service
python app.py
```

**Production mode (using Gunicorn):**
```bash
cd analytics_service
gunicorn -w 4 -b 0.0.0.0:5000 app:app
```

The service will start on `http://localhost:5000` by default.

## Usage

### Automatic Fallback

The Laravel application automatically:
1. Checks if Python service is available
2. Uses Python service if available
3. Falls back to PHP-ML if Python service is unavailable

### Manual Control

You can disable Python service in Laravel `.env`:

```env
PYTHON_ANALYTICS_ENABLED=false
```

This will force Laravel to use PHP-ML only.

## API Endpoints

The Python service exposes the following endpoints:

### Health Check
```
GET /health
```

### Clustering
```
POST /api/clustering/kmeans
POST /api/clustering/optimal-k
POST /api/clustering/hierarchical
```

### Decision Trees
```
POST /api/decision-tree/train
POST /api/decision-tree/predict
```

### Analytics
```
POST /api/analytics/health-risk
POST /api/analytics/service-eligibility
POST /api/analytics/demographic
```

## Example Request

```bash
curl -X POST http://localhost:5000/api/clustering/kmeans \
  -H "Content-Type: application/json" \
  -d '{
    "samples": [[1, 2], [2, 3], [3, 4]],
    "k": 2,
    "max_iterations": 100,
    "num_runs": 3
  }'
```

## Troubleshooting

### Python Service Not Available

1. Check if Python service is running:
   ```bash
   curl http://localhost:5000/health
   ```

2. Check Laravel logs:
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. Verify configuration in `config/services.php`

### Performance Issues

- Increase timeout in Laravel `.env`: `PYTHON_ANALYTICS_TIMEOUT=60`
- Use Gunicorn with multiple workers for production
- Enable caching in Laravel (already implemented)

### Dependencies Issues

If you encounter dependency conflicts:

```bash
pip install --upgrade pip
pip install -r requirements.txt --force-reinstall
```

## Benefits Over PHP-ML

1. **Better Performance**: Faster execution for large datasets
2. **More Algorithms**: Access to advanced ML algorithms
3. **Better Metrics**: More comprehensive evaluation metrics
4. **Active Development**: Python ML libraries are actively maintained
5. **Ecosystem**: Access to vast Python data science ecosystem

## Migration Path

The system supports gradual migration:

1. **Phase 1**: Both services available, automatic fallback
2. **Phase 2**: Python service becomes primary
3. **Phase 3**: PHP-ML can be removed (optional)

## Production Deployment

For production, consider:

1. **Process Manager**: Use systemd, supervisor, or PM2
2. **Load Balancer**: Use nginx or Apache as reverse proxy
3. **Monitoring**: Add health checks and logging
4. **Security**: Use HTTPS and authentication tokens

Example systemd service (`/etc/systemd/system/python-analytics.service`):

```ini
[Unit]
Description=Python Analytics Service
After=network.target

[Service]
User=www-data
WorkingDirectory=/path/to/analytics_service
Environment="PATH=/path/to/venv/bin"
ExecStart=/path/to/venv/bin/gunicorn -w 4 -b 127.0.0.1:5000 app:app

[Install]
WantedBy=multi-user.target
```

## Support

For issues or questions:
1. Check logs in `storage/logs/laravel.log`
2. Check Python service logs
3. Verify network connectivity
4. Check firewall settings


