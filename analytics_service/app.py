"""
Flask API for Analytics Services
Provides machine learning and data analysis endpoints for BARMMS
"""
from flask import Flask, request, jsonify
from flask_cors import CORS
import os
from dotenv import load_dotenv

from services.clustering_service import ClusteringService
from services.analytics_service import AnalyticsService
from services.program_service import ProgramService
from services.clustering_analytics_service import ClusteringAnalyticsService

load_dotenv()

app = Flask(__name__)
CORS(app)  # Enable CORS for Laravel requests

# Initialize services
clustering_service = ClusteringService()
analytics_service = AnalyticsService()
program_service = ProgramService()
clustering_analytics_service = ClusteringAnalyticsService()


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


@app.route('/api/analytics/blotter', methods=['POST'])
def blotter_analysis():
    """Blotter analysis endpoint - 1:1 conversion from PHP"""
    try:
        data = request.get_json()
        
        if not data or 'blotters' not in data:
            return jsonify({'error': 'Missing required data: blotters'}), 400
        
        blotters = data['blotters']
        result = analytics_service.analyze_blotters(blotters)
        
        return jsonify(result)
    except Exception as e:
        return jsonify({'error': str(e)}), 500


@app.route('/api/analytics/documents', methods=['POST'])
def document_analysis():
    """Document request analysis endpoint - 1:1 conversion from PHP"""
    try:
        data = request.get_json()
        
        if not data or 'requests' not in data:
            return jsonify({'error': 'Missing required data: requests'}), 400
        
        requests = data['requests']
        result = analytics_service.analyze_documents(requests)
        
        return jsonify(result)
    except Exception as e:
        return jsonify({'error': str(e)}), 500


@app.route('/api/analytics/health-report', methods=['POST'])
def health_report_analysis():
    """Health report analysis endpoint - 1:1 conversion from PHP"""
    try:
        data = request.get_json()
        
        if not data:
            return jsonify({'error': 'Missing required data'}), 400
        
        result = analytics_service.analyze_health_report(data)
        
        return jsonify(result)
    except Exception as e:
        return jsonify({'error': str(e)}), 500


@app.route('/api/analytics/medicine-report', methods=['POST'])
def medicine_report_analysis():
    """Medicine report analysis endpoint - 1:1 conversion from PHP"""
    try:
        data = request.get_json()
        
        if not data or 'start_date' not in data or 'end_date' not in data:
            return jsonify({'error': 'Missing required data: start_date, end_date'}), 400
        
        result = analytics_service.analyze_medicine_report(
            data,
            data['start_date'],
            data['end_date']
        )
        
        return jsonify(result)
    except Exception as e:
        return jsonify({'error': str(e)}), 500


@app.route('/api/analytics/dashboard', methods=['POST'])
def dashboard_analysis():
    """Dashboard analysis endpoint - 1:1 conversion from PHP"""
    try:
        data = request.get_json()
        
        if not data:
            return jsonify({'error': 'Missing required data'}), 400
        
        result = analytics_service.analyze_dashboard(data)
        
        return jsonify(result)
    except Exception as e:
        return jsonify({'error': str(e)}), 500


# Program Endpoints
@app.route('/api/programs/evaluate-resident', methods=['POST'])
def evaluate_resident():
    """Evaluate single resident eligibility against program criteria"""
    try:
        data = request.get_json()
        
        if not data or 'profile' not in data or 'program' not in data:
            return jsonify({'error': 'Missing required data: profile and program'}), 400
        
        profile = data['profile']
        program_data = data['program']
        
        result = program_service.evaluate_resident(profile, program_data)
        
        return jsonify({'eligible': result})
    except Exception as e:
        return jsonify({'error': str(e)}), 500


@app.route('/api/programs/eligible-residents', methods=['POST'])
def get_eligible_residents():
    """Get all eligible residents for a program"""
    try:
        data = request.get_json()
        
        if not data or 'residents' not in data or 'program' not in data:
            return jsonify({'error': 'Missing required data: residents and program'}), 400
        
        residents_data = data['residents']
        program_data = data['program']
        purok = data.get('purok')
        
        result = program_service.get_eligible_residents(residents_data, program_data, purok)
        
        return jsonify({'eligible_residents': result})
    except Exception as e:
        return jsonify({'error': str(e)}), 500


@app.route('/api/programs/resident-programs', methods=['POST'])
def get_resident_programs():
    """Get all programs a resident is eligible for"""
    try:
        data = request.get_json()
        
        if not data or 'resident' not in data or 'programs' not in data:
            return jsonify({'error': 'Missing required data: resident and programs'}), 400
        
        resident_data = data['resident']
        programs_data = data['programs']
        
        result = program_service.get_resident_programs(resident_data, programs_data)
        
        return jsonify({'eligible_programs': result})
    except Exception as e:
        return jsonify({'error': str(e)}), 500


@app.route('/api/programs/recommendations-by-purok', methods=['POST'])
def get_recommendations_by_purok():
    """Get program recommendations grouped by purok"""
    try:
        data = request.get_json()
        
        if not data or 'residents' not in data or 'program' not in data:
            return jsonify({'error': 'Missing required data: residents and program'}), 400
        
        residents_data = data['residents']
        program_data = data['program']
        
        result = program_service.get_program_recommendations_by_purok(residents_data, program_data)
        
        return jsonify({'recommendations': result})
    except Exception as e:
        return jsonify({'error': str(e)}), 500


@app.route('/api/programs/purok-eligibility-stats', methods=['POST'])
def get_purok_eligibility_stats():
    """Get eligibility statistics per purok"""
    try:
        data = request.get_json()
        
        if not data or 'residents' not in data or 'program' not in data:
            return jsonify({'error': 'Missing required data: residents and program'}), 400
        
        residents_data = data['residents']
        program_data = data['program']
        purok = data.get('purok')
        
        result = program_service.get_purok_eligibility_stats(residents_data, program_data, purok)
        
        return jsonify({'stats': result})
    except Exception as e:
        return jsonify({'error': str(e)}), 500


@app.route('/api/programs/aggregate-resident-data', methods=['POST'])
def aggregate_resident_data():
    """Aggregate resident profile data"""
    try:
        data = request.get_json()
        
        if not data or 'resident' not in data:
            return jsonify({'error': 'Missing required data: resident'}), 400
        
        resident = data['resident']
        blotters = data.get('blotters', [])
        medical_records = data.get('medical_records', [])
        
        result = program_service.aggregate_resident_data(resident, blotters, medical_records)
        
        return jsonify({'profile': result})
    except Exception as e:
        return jsonify({'error': str(e)}), 500


@app.route('/api/programs/all-puroks', methods=['POST'])
def get_all_puroks():
    """Get all puroks with resident counts"""
    try:
        data = request.get_json()
        
        if not data or 'residents' not in data:
            return jsonify({'error': 'Missing required data: residents'}), 400
        
        residents_data = data['residents']
        
        result = program_service.get_all_puroks(residents_data)
        
        return jsonify({'puroks': result})
    except Exception as e:
        return jsonify({'error': str(e)}), 500


# Clustering Analytics Endpoints
@app.route('/api/clustering/aggregate-purok-data', methods=['POST'])
def aggregate_purok_data():
    """Aggregate all data by purok for clustering"""
    try:
        data = request.get_json()
        
        if not data or 'residents' not in data:
            return jsonify({'error': 'Missing required data: residents'}), 400
        
        residents = data['residents']
        blotters = data.get('blotters', [])
        medical_records = data.get('medical_records', [])
        medicine_transactions = data.get('medicine_transactions', [])
        medicine_requests = data.get('medicine_requests', [])
        medicine_names = data.get('medicine_names', {})  # {medicine_id: name}
        
        result = clustering_analytics_service.aggregate_data_by_purok(
            residents,
            blotters,
            medical_records,
            medicine_transactions,
            medicine_requests,
            medicine_names
        )
        
        return jsonify({'purok_data': result})
    except Exception as e:
        return jsonify({'error': str(e)}), 500


@app.route('/api/clustering/build-purok-features', methods=['POST'])
def build_purok_features():
    """Build feature samples for clustering"""
    try:
        data = request.get_json()
        
        if not data or 'purok_data' not in data:
            return jsonify({'error': 'Missing required data: purok_data'}), 400
        
        purok_data = data['purok_data']
        
        result = clustering_analytics_service.build_purok_risk_features(purok_data)
        
        return jsonify({'samples': result})
    except Exception as e:
        return jsonify({'error': str(e)}), 500


@app.route('/api/clustering/compute-incident-analytics', methods=['POST'])
def compute_incident_analytics():
    """Compute incident analytics for a cluster"""
    try:
        data = request.get_json()
        
        if not data or 'resident_ids' not in data or 'blotters' not in data:
            return jsonify({'error': 'Missing required data: resident_ids and blotters'}), 400
        
        resident_ids = data['resident_ids']
        blotters = data['blotters']
        
        # Build blotters_by_resident dict
        blotters_by_resident = {}
        for blotter in blotters:
            respondent_id = blotter.get('respondent_id')
            if respondent_id:
                if respondent_id not in blotters_by_resident:
                    blotters_by_resident[respondent_id] = []
                blotters_by_resident[respondent_id].append(blotter)
        
        result = clustering_analytics_service.compute_incident_analytics(
            resident_ids,
            blotters_by_resident
        )
        
        return jsonify(result)
    except Exception as e:
        return jsonify({'error': str(e)}), 500


@app.route('/api/clustering/compute-medical-analytics', methods=['POST'])
def compute_medical_analytics():
    """Compute medical analytics for a cluster"""
    try:
        data = request.get_json()
        
        if not data or 'cluster_puroks' not in data or 'cluster_resident_ids' not in data or 'medical_records' not in data:
            return jsonify({'error': 'Missing required data'}), 400
        
        cluster_puroks = data['cluster_puroks']
        cluster_resident_ids = data['cluster_resident_ids']
        medical_records = data['medical_records']
        
        # Build medical_by_resident dict
        medical_by_resident = {}
        for record in medical_records:
            resident_id = record.get('resident_id')
            if resident_id:
                if resident_id not in medical_by_resident:
                    medical_by_resident[resident_id] = []
                medical_by_resident[resident_id].append(record)
        
        result = clustering_analytics_service.compute_medical_analytics(
            cluster_puroks,
            cluster_resident_ids,
            medical_by_resident
        )
        
        return jsonify(result)
    except Exception as e:
        return jsonify({'error': str(e)}), 500


@app.route('/api/clustering/compute-medicine-analytics', methods=['POST'])
def compute_medicine_analytics():
    """Compute medicine analytics for a cluster"""
    try:
        data = request.get_json()
        
        if not data or 'cluster_resident_ids' not in data:
            return jsonify({'error': 'Missing required data: cluster_resident_ids'}), 400
        
        cluster_resident_ids = data['cluster_resident_ids']
        medicine_requests = data.get('medicine_requests', [])
        medicine_transactions = data.get('medicine_transactions', [])
        medicine_names = data.get('medicine_names', {})  # {medicine_id: name}
        
        # Build dicts
        medicine_req_by_resident = {}
        for req in medicine_requests:
            resident_id = req.get('resident_id')
            if resident_id:
                if resident_id not in medicine_req_by_resident:
                    medicine_req_by_resident[resident_id] = []
                medicine_req_by_resident[resident_id].append(req)
        
        medicine_trans_by_resident = {}
        for trans in medicine_transactions:
            resident_id = trans.get('resident_id')
            if resident_id and trans.get('transaction_type') == 'OUT' and trans.get('medicine_id'):
                if resident_id not in medicine_trans_by_resident:
                    medicine_trans_by_resident[resident_id] = []
                medicine_trans_by_resident[resident_id].append(trans)
        
        result = clustering_analytics_service.compute_medicine_analytics(
            cluster_resident_ids,
            medicine_req_by_resident,
            medicine_trans_by_resident,
            medicine_names
        )
        
        return jsonify(result)
    except Exception as e:
        return jsonify({'error': str(e)}), 500


@app.route('/api/clustering/label-clusters-by-risk', methods=['POST'])
def label_clusters_by_risk():
    """Label clusters as Low/Moderate/High risk"""
    try:
        data = request.get_json()
        
        if not data or 'clusters' not in data or 'purok_data' not in data:
            return jsonify({'error': 'Missing required data: clusters and purok_data'}), 400
        
        clusters = data['clusters']  # {cluster_id: [purok_indices]}
        purok_data = data['purok_data']
        
        result = clustering_analytics_service.label_clusters_by_risk(clusters, purok_data)
        
        return jsonify({'labels': result})
    except Exception as e:
        return jsonify({'error': str(e)}), 500


if __name__ == '__main__':
    port = int(os.getenv('PORT', 5000))
    debug = os.getenv('DEBUG', 'False').lower() == 'true'
    app.run(host='0.0.0.0', port=port, debug=debug)


