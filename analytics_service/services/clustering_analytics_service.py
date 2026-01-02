"""
Clustering Analytics Service for BARMMS
Handles data aggregation, analytics, and feature engineering for purok risk clustering
"""
import re
from typing import List, Dict, Any, Optional
from collections import Counter, defaultdict


class ClusteringAnalyticsService:
    """Service for clustering data aggregation and analytics"""
    
    def extract_purok_token(self, address: Optional[str]) -> str:
        """Extract purok token from address string"""
        if not address:
            return 'n/a'
        
        addr = address.strip().lower()
        if not addr:
            return 'n/a'
        
        # Common patterns
        patterns = [
            r'\bpurok\s*([0-9]+[a-z]?)',
            r'\bpurok\s*([ivxlcdm]+)',
            r'\bprk\s*[-]?\s*([0-9]+[a-z]?)',
            r'\bprk\.\s*([0-9]+[a-z]?)',
            r'\bzone\s*([0-9]+[a-z]?)',
            r'\bbrgy\s*([0-9]+[a-z]?)',
        ]
        
        for pattern in patterns:
            match = re.search(pattern, addr, re.IGNORECASE)
            if match:
                token = match.group(1).lower().strip()
                # Handle numeric tokens with optional letter
                num_match = re.match(r'^(\d+)([a-z]?)$', token)
                if num_match:
                    num = int(num_match.group(1))
                    letter = num_match.group(2) or ''
                    token = f"{num:02d}{letter}"
                return token
        
        return 'n/a'
    
    def calculate_demographic_score(self, residents: List[Dict]) -> float:
        """Calculate demographic risk score for a purok
        Higher score = higher need (younger population + larger families)
        """
        if not residents:
            return 0.0
        
        total_age = 0.0
        total_family_size = 0.0
        count = 0
        
        for resident in residents:
            age = float(resident.get('age', 0))
            family_size = float(resident.get('family_size', 1))
            
            total_age += age
            total_family_size += family_size
            count += 1
        
        if count == 0:
            return 0.0
        
        avg_age = total_age / count
        avg_family_size = total_family_size / count
        
        # Normalize: Younger age (0-100) and larger family size (1-20) indicate higher need
        # Invert age: younger = higher score
        age_score = (100 - min(100, avg_age)) / 100  # 0-1 scale, inverted
        family_size_score = min(20, avg_family_size) / 20  # 0-1 scale
        
        # Average the two scores
        return (age_score + family_size_score) / 2
    
    def aggregate_data_by_purok(
        self,
        residents: List[Dict],
        blotters: List[Dict],
        medical_records: List[Dict],
        medicine_transactions: List[Dict],
        medicine_requests: List[Dict],
        medicine_names: Dict[int, str] = None
    ) -> List[Dict[str, Any]]:
        """Aggregate all data by purok for combined risk clustering"""
        # Group residents by purok
        grouped_residents = defaultdict(list)
        resident_id_to_purok = {}
        
        for resident in residents:
            address = resident.get('address', '')
            purok_token = self.extract_purok_token(address)
            grouped_residents[purok_token].append(resident)
            resident_id_to_purok[resident.get('id')] = purok_token
        
        # Index data by resident ID for quick lookup
        blotters_by_resident = defaultdict(list)
        for blotter in blotters:
            respondent_id = blotter.get('respondent_id')
            if respondent_id:
                blotters_by_resident[respondent_id].append(blotter)
        
        medical_by_resident = defaultdict(list)
        for record in medical_records:
            resident_id = record.get('resident_id')
            if resident_id:
                medical_by_resident[resident_id].append(record)
        
        medicine_trans_by_resident = defaultdict(list)
        for trans in medicine_transactions:
            resident_id = trans.get('resident_id')
            if resident_id and trans.get('transaction_type') == 'OUT' and trans.get('medicine_id'):
                medicine_trans_by_resident[resident_id].append(trans)
        
        medicine_req_by_resident = defaultdict(list)
        for req in medicine_requests:
            resident_id = req.get('resident_id')
            if resident_id and req.get('medicine_id'):
                medicine_req_by_resident[resident_id].append(req)
        
        purok_data = []
        
        # Aggregate data for each purok
        for purok_token, purok_residents in grouped_residents.items():
            if not purok_residents:
                continue
            
            resident_ids = [r.get('id') for r in purok_residents if r.get('id')]
            purok_display = 'N/A' if purok_token == 'n/a' else f'Purok {purok_token.upper()}'
            
            # Count blotter incidents
            blotter_count = sum(
                len(blotters_by_resident.get(rid, []))
                for rid in resident_ids
            )
            
            # Count medical visits
            medical_count = sum(
                len(medical_by_resident.get(rid, []))
                for rid in resident_ids
            )
            
            # Count medicine dispenses (OUT transactions only, with valid medicine)
            medicine_count = sum(
                sum(trans.get('quantity', 0) for trans in medicine_trans_by_resident.get(rid, []))
                for rid in resident_ids
            )
            
            # Calculate demographic score
            demographic_score = self.calculate_demographic_score(purok_residents)
            
            # Compute per-purok analytics
            purok_incident_analytics = self.compute_incident_analytics(
                resident_ids, blotters_by_resident
            )
            purok_medical_analytics = self.compute_medical_analytics_for_purok(
                resident_ids, medical_by_resident, purok_display
            )
            purok_medicine_analytics = self.compute_medicine_analytics(
                resident_ids, medicine_req_by_resident, medicine_trans_by_resident, medicine_names or {}
            )
            
            purok_data.append({
                'purok_token': purok_token,
                'purok_display': purok_display,
                'resident_ids': resident_ids,
                'resident_count': len(purok_residents),
                'blotter_count': int(blotter_count),
                'demographic_score': demographic_score,
                'medical_count': int(medical_count),
                'medicine_count': int(medicine_count),
                'incident_analytics': purok_incident_analytics,
                'medical_analytics': purok_medical_analytics,
                'medicine_analytics': purok_medicine_analytics,
            })
        
        return purok_data
    
    def build_purok_risk_features(self, purok_data: List[Dict]) -> List[List[float]]:
        """Build feature samples for clustering
        Features: [blotter_count, demographic_score, medical_count, medicine_count]
        All features are normalized to 0-1 scale
        """
        if not purok_data:
            return []
        
        # Extract all values for normalization
        blotter_counts = [d['blotter_count'] for d in purok_data]
        demographic_scores = [d['demographic_score'] for d in purok_data]
        medical_counts = [d['medical_count'] for d in purok_data]
        medicine_counts = [d['medicine_count'] for d in purok_data]
        
        # Calculate min/max for each feature
        blotter_min = min(blotter_counts) if blotter_counts else 0
        blotter_max = max(blotter_counts) if blotter_counts else 1
        demographic_min = min(demographic_scores) if demographic_scores else 0
        demographic_max = max(demographic_scores) if demographic_scores else 1
        medical_min = min(medical_counts) if medical_counts else 0
        medical_max = max(medical_counts) if medical_counts else 1
        medicine_min = min(medicine_counts) if medicine_counts else 0
        medicine_max = max(medicine_counts) if medicine_counts else 1
        
        samples = []
        for data in purok_data:
            normalized_blotter = self.normalize_value(
                data['blotter_count'],
                blotter_min,
                blotter_max
            )
            normalized_demographic = self.normalize_value(
                data['demographic_score'],
                demographic_min,
                demographic_max
            )
            normalized_medical = self.normalize_value(
                data['medical_count'],
                medical_min,
                medical_max
            )
            normalized_medicine = self.normalize_value(
                data['medicine_count'],
                medicine_min,
                medicine_max
            )
            
            samples.append([
                float(normalized_blotter),
                float(normalized_demographic),
                float(normalized_medical),
                float(normalized_medicine),
            ])
        
        return samples
    
    def normalize_value(self, value: float, min_val: float, max_val: float) -> float:
        """Normalize a value to 0-1 scale using min-max normalization"""
        # Handle case where all values are the same
        if max_val - min_val == 0:
            return 0.5  # Return middle value
        
        return (value - min_val) / (max_val - min_val)
    
    def compute_incident_analytics(
        self,
        resident_ids: List[int],
        blotters_by_resident: Dict[int, List[Dict]]
    ) -> Dict[str, Any]:
        """Compute incident analytics for a cluster
        Returns case types ordered from most common to least common
        """
        if not resident_ids:
            return {'case_types': []}
        
        case_type_counts = Counter()
        
        for resident_id in resident_ids:
            for blotter in blotters_by_resident.get(resident_id, []):
                case_type = blotter.get('type')
                if case_type:
                    case_type_counts[case_type] += 1
        
        # Sort by count descending
        case_types = [
            {'type': case_type, 'count': count}
            for case_type, count in case_type_counts.most_common()
        ]
        
        return {'case_types': case_types}
    
    def compute_medical_analytics_for_purok(
        self,
        resident_ids: List[int],
        medical_by_resident: Dict[int, List[Dict]],
        purok_display: str
    ) -> Dict[str, Any]:
        """Compute medical analytics for a single purok"""
        if not resident_ids:
            return {
                'visits_by_purok': [],
                'illnesses': [],
            }
        
        visit_count = 0
        illness_counts = Counter()
        
        for resident_id in resident_ids:
            for record in medical_by_resident.get(resident_id, []):
                visit_count += 1
                diagnosis = record.get('diagnosis')
                if diagnosis:
                    diagnosis = diagnosis.strip()
                    if diagnosis:
                        illness_counts[diagnosis] += 1
        
        # Convert to sorted list
        illnesses = [
            {'illness': illness, 'count': count}
            for illness, count in illness_counts.most_common()
        ]
        
        return {
            'visits_by_purok': [{'purok': purok_display, 'count': visit_count}],
            'illnesses': illnesses,
        }
    
    def compute_medical_analytics(
        self,
        cluster_puroks: List[Dict],
        cluster_resident_ids: List[int],
        medical_by_resident: Dict[int, List[Dict]]
    ) -> Dict[str, Any]:
        """Compute medical analytics for a cluster
        Returns per-purok visit counts and illness frequencies
        """
        if not cluster_resident_ids:
            return {
                'visits_by_purok': [],
                'illnesses': [],
            }
        
        # Map resident IDs to puroks
        resident_to_purok = {}
        for purok in cluster_puroks:
            purok_display = purok.get('purok_display', 'Unknown')
            for resident_id in purok.get('resident_ids', []):
                resident_to_purok[resident_id] = purok_display
        
        # Count visits per purok and illnesses
        visits_by_purok = Counter()
        illness_counts = Counter()
        
        for resident_id in cluster_resident_ids:
            purok_display = resident_to_purok.get(resident_id, 'Unknown')
            
            for record in medical_by_resident.get(resident_id, []):
                visits_by_purok[purok_display] += 1
                
                diagnosis = record.get('diagnosis')
                if diagnosis:
                    diagnosis = diagnosis.strip()
                    if diagnosis:
                        illness_counts[diagnosis] += 1
        
        # Convert to sorted arrays
        visits_by_purok_array = [
            {'purok': purok, 'count': count}
            for purok, count in visits_by_purok.most_common()
        ]
        
        illnesses_array = [
            {'illness': illness, 'count': count}
            for illness, count in illness_counts.most_common()
        ]
        
        return {
            'visits_by_purok': visits_by_purok_array,
            'illnesses': illnesses_array,
        }
    
    def compute_medicine_analytics(
        self,
        cluster_resident_ids: List[int],
        medicine_req_by_resident: Dict[int, List[Dict]],
        medicine_trans_by_resident: Dict[int, List[Dict]],
        medicine_names: Dict[int, str]
    ) -> Dict[str, Any]:
        """Compute medicine analytics for a cluster
        Returns medicines requested/dispensed ordered from most common to least common
        """
        if not cluster_resident_ids:
            return {'medicines': []}
        
        # Combine and count medicines
        medicine_counts = defaultdict(lambda: {'name': '', 'requested': 0, 'dispensed': 0})
        
        # Count from requests
        for resident_id in cluster_resident_ids:
            for request in medicine_req_by_resident.get(resident_id, []):
                medicine_id = request.get('medicine_id')
                if medicine_id:
                    # Use medicine name from mapping, or fallback to ID if not found
                    if medicine_id in medicine_names:
                        medicine_name = medicine_names[medicine_id]
                    else:
                        # Fallback: use medicine_id as name if mapping is missing
                        medicine_name = f'Medicine ID {medicine_id}'
                    medicine_counts[medicine_name]['name'] = medicine_name
                    medicine_counts[medicine_name]['requested'] += request.get('quantity_requested', 1)
        
        # Count from transactions
        for resident_id in cluster_resident_ids:
            for transaction in medicine_trans_by_resident.get(resident_id, []):
                medicine_id = transaction.get('medicine_id')
                if medicine_id:
                    # Use medicine name from mapping, or fallback to ID if not found
                    if medicine_id in medicine_names:
                        medicine_name = medicine_names[medicine_id]
                    else:
                        # Fallback: use medicine_id as name if mapping is missing
                        medicine_name = f'Medicine ID {medicine_id}'
                    medicine_counts[medicine_name]['name'] = medicine_name
                    medicine_counts[medicine_name]['dispensed'] += transaction.get('quantity', 0)
        
        # Convert to array, only include medicines with dispensed > 0
        medicines_array = []
        for medicine_name, medicine_data in medicine_counts.items():
            if medicine_data['dispensed'] > 0:
                medicine_data['total'] = medicine_data['dispensed']
                medicines_array.append(medicine_data)
        
        # Sort by total (most common first)
        medicines_array.sort(key=lambda x: x['total'], reverse=True)
        
        return {'medicines': medicines_array}
    
    def label_clusters_by_risk(
        self,
        clusters: Dict[int, List[int]],
        purok_data: List[Dict]
    ) -> Dict[int, str]:
        """Label clusters as Low/Moderate/High risk based on centroid analysis
        Returns mapping: [originalClusterId => 'Low Risk'|'Moderate Risk'|'High Risk']
        """
        # Calculate average risk score for each cluster
        cluster_risk_scores = {}
        
        for cluster_id, purok_indices in clusters.items():
            total_risk = 0.0
            count = 0
            
            for index in purok_indices:
                if index < len(purok_data):
                    data = purok_data[index]
                    # Average all 4 normalized features as overall risk
                    risk = (
                        data['blotter_count'] +
                        data['demographic_score'] +
                        data['medical_count'] +
                        data['medicine_count']
                    ) / 4
                    total_risk += risk
                    count += 1
            
            avg_risk = total_risk / count if count > 0 else 0
            cluster_risk_scores[cluster_id] = avg_risk
        
        # Sort clusters by risk score
        sorted_cluster_ids = sorted(
            cluster_risk_scores.keys(),
            key=lambda x: cluster_risk_scores[x]
        )
        
        # Assign labels based on ranking
        labels = {}
        num_clusters = len(sorted_cluster_ids)
        
        for index, cluster_id in enumerate(sorted_cluster_ids):
            if num_clusters == 1:
                labels[cluster_id] = 'Moderate Risk'
            elif num_clusters == 2:
                labels[cluster_id] = 'Low Risk' if index == 0 else 'High Risk'
            else:
                # 3 or more clusters
                if index == 0:
                    labels[cluster_id] = 'Low Risk'
                elif index == num_clusters - 1:
                    labels[cluster_id] = 'High Risk'
                else:
                    labels[cluster_id] = 'Moderate Risk'
        
        return labels

