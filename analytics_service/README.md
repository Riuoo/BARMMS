# Analytics Service

Python microservice for advanced analytics and machine learning for BARMMS.

## Features

- **K-Means Clustering**: Advanced clustering with optimal K detection
- **Hierarchical Clustering**: Agglomerative clustering with multiple linkage methods
- **Decision Trees**: Standard, Random Forest, and XGBoost models
- **Health Risk Analysis**: ML-based health risk prediction
- **Service Eligibility**: Automated eligibility determination
- **Demographic Analysis**: Comprehensive demographic clustering

## Setup

1. Install Python 3.8+ and pip

2. Install dependencies:
```bash
pip install -r requirements.txt
```

3. Create `.env` file from `.env.example`:
```bash
cp .env.example .env
```

4. Run the service:
```bash
python app.py
```

Or with gunicorn for production:
```bash
gunicorn -w 4 -b 0.0.0.0:5000 app:app
```

## API Endpoints

### Health Check
- `GET /health` - Service health check

### Clustering
- `POST /api/clustering/kmeans` - K-Means clustering
- `POST /api/clustering/optimal-k` - Find optimal K value
- `POST /api/clustering/hierarchical` - Hierarchical clustering

### Decision Trees
- `POST /api/decision-tree/train` - Train a decision tree model
- `POST /api/decision-tree/predict` - Make predictions

### Analytics
- `POST /api/analytics/health-risk` - Health risk analysis
- `POST /api/analytics/service-eligibility` - Service eligibility analysis
- `POST /api/analytics/demographic` - Demographic analysis

## Example Request

```bash
curl -X POST http://localhost:5000/api/clustering/kmeans \
  -H "Content-Type: application/json" \
  -d '{
    "samples": [[1, 2], [2, 3], [3, 4]],
    "k": 2
  }'
```

## Integration with Laravel

The Laravel application communicates with this service via HTTP requests. See `app/Services/PythonAnalyticsService.php` in the Laravel codebase.


