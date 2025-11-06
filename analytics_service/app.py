"""
Flask API for Analytics Services
Provides machine learning and data analysis endpoints for BARMMS
"""
from flask import Flask, request, jsonify
from flask_cors import CORS
import os
from dotenv import load_dotenv

from services.clustering_service import ClusteringService
from services.decision_tree_service import DecisionTreeService
from services.analytics_service import AnalyticsService

load_dotenv()

app = Flask(__name__)
CORS(app)  # Enable CORS for Laravel requests

# Initialize services
clustering_service = ClusteringService()
decision_tree_service = DecisionTreeService()
analytics_service = AnalyticsService()


@app.route('/health', methods=['GET'])
def health():
    """Health check endpoint"""
    return jsonify({'status': 'healthy', 'service': 'analytics-api'})


@app.route('/api/clustering/kmeans', methods=['POST'])
def kmeans_clustering():
    """K-Means clustering endpoint"""
    try:
        data = request.get_json()
        
        if not data or 'samples' not in data:
            return jsonify({'error': 'Missing required data: samples'}), 400
        
        samples = data['samples']
        k = data.get('k', 3)
        max_iterations = data.get('max_iterations', 100)
        num_runs = data.get('num_runs', 3)
        
        result = clustering_service.kmeans(
            samples=samples,
            k=k,
            max_iterations=max_iterations,
            num_runs=num_runs
        )
        
        return jsonify(result)
    except Exception as e:
        return jsonify({'error': str(e)}), 500


@app.route('/api/clustering/optimal-k', methods=['POST'])
def find_optimal_k():
    """Find optimal K value using elbow method and silhouette score"""
    try:
        data = request.get_json()
        
        if not data or 'samples' not in data:
            return jsonify({'error': 'Missing required data: samples'}), 400
        
        samples = data['samples']
        max_k = data.get('max_k', 10)
        method = data.get('method', 'elbow')  # 'elbow', 'silhouette', or 'gap'
        
        result = clustering_service.find_optimal_k(
            samples=samples,
            max_k=max_k,
            method=method
        )
        
        return jsonify(result)
    except Exception as e:
        return jsonify({'error': str(e)}), 500


@app.route('/api/clustering/hierarchical', methods=['POST'])
def hierarchical_clustering():
    """Hierarchical clustering endpoint"""
    try:
        data = request.get_json()
        
        if not data or 'samples' not in data:
            return jsonify({'error': 'Missing required data: samples'}), 400
        
        samples = data['samples']
        n_clusters = data.get('n_clusters', 3)
        linkage = data.get('linkage', 'ward')  # 'ward', 'complete', 'average'
        
        result = clustering_service.hierarchical(
            samples=samples,
            n_clusters=n_clusters,
            linkage=linkage
        )
        
        return jsonify(result)
    except Exception as e:
        return jsonify({'error': str(e)}), 500


@app.route('/api/decision-tree/train', methods=['POST'])
def train_decision_tree():
    """Train decision tree model"""
    try:
        data = request.get_json()
        
        if not data or 'samples' not in data or 'labels' not in data:
            return jsonify({'error': 'Missing required data: samples and labels'}), 400
        
        samples = data['samples']
        labels = data['labels']
        model_type = data.get('model_type', 'decision_tree')  # 'decision_tree', 'random_forest'
        test_size = data.get('test_size', 0.3)
        random_state = data.get('random_state', 42)
        
        # Additional parameters
        max_depth = data.get('max_depth', 10)
        min_samples_split = data.get('min_samples_split', 5)
        min_samples_leaf = data.get('min_samples_leaf', 2)
        
        result = decision_tree_service.train(
            samples=samples,
            labels=labels,
            model_type=model_type,
            test_size=test_size,
            random_state=random_state,
            max_depth=max_depth,
            min_samples_split=min_samples_split,
            min_samples_leaf=min_samples_leaf
        )
        
        return jsonify(result)
    except Exception as e:
        return jsonify({'error': str(e)}), 500


@app.route('/api/decision-tree/predict', methods=['POST'])
def predict_decision_tree():
    """Make predictions using trained model"""
    try:
        data = request.get_json()
        
        if not data or 'model_id' not in data or 'samples' not in data:
            return jsonify({'error': 'Missing required data: model_id and samples'}), 400
        
        model_id = data['model_id']
        samples = data['samples']
        
        result = decision_tree_service.predict(
            model_id=model_id,
            samples=samples
        )
        
        return jsonify(result)
    except Exception as e:
        return jsonify({'error': str(e)}), 500


@app.route('/api/decision-tree/feature-importance', methods=['POST'])
def get_feature_importance():
    """Get feature importance for a trained model"""
    try:
        data = request.get_json()
        
        if not data or 'model_id' not in data:
            return jsonify({'error': 'Missing required data: model_id'}), 400
        
        model_id = data['model_id']
        
        result = decision_tree_service.get_feature_importance(model_id=model_id)
        
        return jsonify(result)
    except Exception as e:
        return jsonify({'error': str(e)}), 500


@app.route('/api/analytics/health-risk', methods=['POST'])
def health_risk_analysis():
    """Health risk analysis endpoint"""
    try:
        data = request.get_json()
        
        if not data or 'residents' not in data:
            return jsonify({'error': 'Missing required data: residents'}), 400
        
        residents = data['residents']
        model_type = data.get('model_type', 'random_forest')
        
        result = analytics_service.analyze_health_risk(
            residents=residents,
            model_type=model_type
        )
        
        return jsonify(result)
    except Exception as e:
        return jsonify({'error': str(e)}), 500


@app.route('/api/analytics/service-eligibility', methods=['POST'])
def service_eligibility_analysis():
    """Service eligibility analysis endpoint"""
    try:
        data = request.get_json()
        
        if not data or 'residents' not in data:
            return jsonify({'error': 'Missing required data: residents'}), 400
        
        residents = data['residents']
        model_type = data.get('model_type', 'decision_tree')
        
        result = analytics_service.analyze_service_eligibility(
            residents=residents,
            model_type=model_type
        )
        
        return jsonify(result)
    except Exception as e:
        return jsonify({'error': str(e)}), 500


@app.route('/api/analytics/demographic', methods=['POST'])
def demographic_analysis():
    """Demographic analysis endpoint"""
    try:
        data = request.get_json()
        
        if not data or 'residents' not in data:
            return jsonify({'error': 'Missing required data: residents'}), 400
        
        residents = data['residents']
        
        result = analytics_service.analyze_demographics(residents=residents)
        
        return jsonify(result)
    except Exception as e:
        return jsonify({'error': str(e)}), 500


@app.route('/api/analytics/health-condition', methods=['POST'])
def health_condition_analysis():
    """Health condition analysis endpoint"""
    try:
        data = request.get_json()
        
        if not data or 'residents' not in data:
            return jsonify({'error': 'Missing required data: residents'}), 400
        
        residents = data['residents']
        model_type = data.get('model_type', 'decision_tree')
        
        result = analytics_service.analyze_health_condition(
            residents=residents,
            model_type=model_type
        )
        
        return jsonify(result)
    except Exception as e:
        return jsonify({'error': str(e)}), 500


@app.route('/api/analytics/program-recommendation', methods=['POST'])
def program_recommendation_analysis():
    """Program recommendation analysis endpoint"""
    try:
        data = request.get_json()
        
        if not data or 'residents' not in data:
            return jsonify({'error': 'Missing required data: residents'}), 400
        
        residents = data['residents']
        model_type = data.get('model_type', 'random_forest')
        
        result = analytics_service.analyze_program_recommendation(
            residents=residents,
            model_type=model_type
        )
        
        return jsonify(result)
    except Exception as e:
        return jsonify({'error': str(e)}), 500


if __name__ == '__main__':
    port = int(os.getenv('PORT', 5000))
    debug = os.getenv('DEBUG', 'False').lower() == 'true'
    app.run(host='0.0.0.0', port=port, debug=debug)


