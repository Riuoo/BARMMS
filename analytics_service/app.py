"""
Flask API for Analytics Services
Provides machine learning and data analysis endpoints for BARMMS
"""
from flask import Flask, request, jsonify
from flask_cors import CORS
import os
from dotenv import load_dotenv

from services.clustering_service import ClusteringService

load_dotenv()

app = Flask(__name__)
CORS(app)  # Enable CORS for Laravel requests

# Initialize services
clustering_service = ClusteringService()


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


if __name__ == '__main__':
    port = int(os.getenv('PORT', 5000))
    debug = os.getenv('DEBUG', 'False').lower() == 'true'
    app.run(host='0.0.0.0', port=port, debug=debug)


