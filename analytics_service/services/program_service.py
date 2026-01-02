"""
Program Service for Eligibility Evaluation and Recommendations
Handles all business logic for the Programs module
"""
import re
from typing import List, Dict, Any, Optional
from datetime import datetime, timedelta
from dateutil import parser as date_parser


class ProgramService:
    """Service for program eligibility evaluation and recommendations"""
    
    @staticmethod
    def now_naive() -> datetime:
        """Returns a timezone-naive datetime.now()"""
        return datetime.now().replace(tzinfo=None)
    
    def extract_purok(self, address: str) -> str:
        """Extract purok from address string"""
        if not address:
            return 'n/a'
        
        match = re.search(r'Purok\s*(\d+)', address, re.IGNORECASE)
        if match:
            return match.group(1).lower()
        
        return 'n/a'
    
    def get_blotter_metrics(self, blotters: List[Dict]) -> Dict[str, Any]:
        """Calculate blotter metrics for a resident"""
        if not blotters:
            return {
                'total_count': 0,
                'recent_count': 0,
                'types': [],
                'has_recent_incidents': False,
                'last_incident_date': None,
            }
        
        now = self.now_naive()
        twelve_months_ago = now - timedelta(days=365)
        
        recent_blotters = []
        for blotter in blotters:
            created_at = self.parse_date(blotter.get('created_at'))
            if created_at and created_at > twelve_months_ago:
                recent_blotters.append(blotter)
        
        last_incident = None
        for blotter in blotters:
            created_at = self.parse_date(blotter.get('created_at'))
            if created_at:
                if last_incident is None or created_at > last_incident:
                    last_incident = created_at
        
        return {
            'total_count': len(blotters),
            'recent_count': len(recent_blotters),
            'types': [b.get('type') for b in blotters if b.get('type')],
            'has_recent_incidents': len(recent_blotters) > 0,
            'last_incident_date': last_incident.strftime('%Y-%m-%d') if last_incident else None,
        }
    
    def get_medical_metrics(self, medical_records: List[Dict]) -> Dict[str, Any]:
        """Calculate medical metrics for a resident"""
        if not medical_records:
            return {
                'total_visits': 0,
                'recent_visits': 0,
                'has_recent_visits': False,
                'diagnoses': [],
                'chronic_conditions': [],
                'has_chronic_conditions': False,
                'last_visit_date': None,
            }
        
        now = self.now_naive()
        six_months_ago = now - timedelta(days=180)
        
        recent_records = []
        for record in medical_records:
            consultation_datetime = self.parse_date(record.get('consultation_datetime'))
            if consultation_datetime and consultation_datetime > six_months_ago:
                recent_records.append(record)
        
        diagnoses = []
        for record in medical_records:
            diagnosis = record.get('diagnosis')
            if diagnosis:
                diagnoses.append(diagnosis.lower())
        
        chronic_conditions = self.identify_chronic_conditions(diagnoses)
        
        last_visit = None
        for record in medical_records:
            consultation_datetime = self.parse_date(record.get('consultation_datetime'))
            if consultation_datetime:
                if last_visit is None or consultation_datetime > last_visit:
                    last_visit = consultation_datetime
        
        return {
            'total_visits': len(medical_records),
            'recent_visits': len(recent_records),
            'has_recent_visits': len(recent_records) > 0,
            'diagnoses': diagnoses,
            'chronic_conditions': chronic_conditions,
            'has_chronic_conditions': len(chronic_conditions) > 0,
            'last_visit_date': last_visit.strftime('%Y-%m-%d') if last_visit else None,
        }
    
    def identify_chronic_conditions(self, diagnoses: List[str]) -> List[str]:
        """Identify chronic conditions from diagnoses"""
        chronic_keywords = [
            'diabetes', 'hypertension', 'high blood pressure', 'asthma',
            'copd', 'heart disease', 'kidney disease', 'arthritis',
            'osteoporosis', 'cancer', 'stroke', 'epilepsy',
        ]
        
        chronic_conditions = []
        for diagnosis in diagnoses:
            for keyword in chronic_keywords:
                if keyword.lower() in diagnosis.lower():
                    chronic_conditions.append(diagnosis)
                    break
        
        return list(set(chronic_conditions))
    
    def aggregate_resident_data(
        self, 
        resident: Dict, 
        blotters: List[Dict], 
        medical_records: List[Dict]
    ) -> Dict[str, Any]:
        """Aggregate resident profile with demographics, blotter, and medical data"""
        blotter_metrics = self.get_blotter_metrics(blotters)
        medical_metrics = self.get_medical_metrics(medical_records)
        
        # Extract purok from address
        address = resident.get('address', '')
        purok = self.extract_purok(address)
        
        # Handle is_pwd - convert boolean to string if needed
        is_pwd = resident.get('is_pwd', False)
        if isinstance(is_pwd, bool):
            is_pwd_str = 'Yes' if is_pwd else 'No'
        else:
            is_pwd_str = str(is_pwd)
        
        return {
            'resident': resident,
            'demographics': {
                'age': resident.get('age'),
                'gender': resident.get('gender'),
                'marital_status': resident.get('marital_status'),
                'employment_status': resident.get('employment_status'),
                'income_level': resident.get('income_level'),
                'education_level': resident.get('education_level'),
                'family_size': resident.get('family_size'),
                'is_pwd': is_pwd_str,
                'occupation': resident.get('occupation'),
                'purok': purok,
            },
            'blotter': blotter_metrics,
            'medical': medical_metrics,
        }
    
    def get_all_puroks(self, residents_data: List[Dict]) -> List[Dict[str, Any]]:
        """Get all puroks with resident counts"""
        puroks = {}
        
        for resident in residents_data:
            address = resident.get('address', '')
            purok = self.extract_purok(address)
            
            if purok not in puroks:
                puroks[purok] = {
                    'name': 'N/A' if purok == 'n/a' else f'Purok {purok.upper()}',
                    'token': purok,
                    'resident_count': 0,
                }
            
            puroks[purok]['resident_count'] += 1
        
        return list(puroks.values())
    
    def parse_date(self, date_value: Any) -> Optional[datetime]:
        """Parse date from various formats and ensure it's timezone-naive."""
        if not date_value:
            return None
        
        if isinstance(date_value, datetime):
            # If it's timezone-aware, convert to UTC then make naive
            if date_value.tzinfo is not None:
                return date_value.astimezone(None).replace(tzinfo=None)
            return date_value
        
        if isinstance(date_value, str):
            try:
                dt = date_parser.parse(date_value)
                if dt.tzinfo is not None:
                    return dt.astimezone(None).replace(tzinfo=None)
                return dt
            except (ValueError, TypeError):
                return None
        
        return None
    
    def get_field_value(self, profile: Dict, field: str) -> Any:
        """Get field value from profile (supports nested fields like 'medical.has_chronic_conditions')"""
        # Handle nested fields like 'medical.has_chronic_conditions'
        if '.' in field:
            parts = field.split('.')
            current = profile
            
            for part in parts:
                if isinstance(current, dict) and part in current:
                    current = current[part]
                else:
                    return None
            
            return current
        
        # Direct field access
        if 'demographics' in profile and field in profile['demographics']:
            return profile['demographics'][field]
        
        if 'resident' in profile and field in profile['resident']:
            return profile['resident'][field]
        
        return None
    
    def compare_values(self, field_value: Any, operator: str, compare_value: Any) -> bool:
        """Compare values based on operator"""
        if field_value is None:
            return False
        
        if operator == 'equals':
            return field_value == compare_value
        
        if operator == 'not_equals':
            return field_value != compare_value
        
        if operator == 'in':
            if not isinstance(compare_value, list):
                compare_value = [compare_value]
            return field_value in compare_value
        
        if operator == 'not_in':
            if not isinstance(compare_value, list):
                compare_value = [compare_value]
            return field_value not in compare_value
        
        if operator == 'greater_than':
            try:
                return float(field_value) > float(compare_value)
            except (ValueError, TypeError):
                return False
        
        if operator == 'less_than':
            try:
                return float(field_value) < float(compare_value)
            except (ValueError, TypeError):
                return False
        
        if operator == 'greater_than_or_equal':
            try:
                return float(field_value) >= float(compare_value)
            except (ValueError, TypeError):
                return False
        
        if operator == 'less_than_or_equal':
            try:
                return float(field_value) <= float(compare_value)
            except (ValueError, TypeError):
                return False
        
        return False
    
    def evaluate_field_condition(self, profile: Dict, condition: Dict) -> bool:
        """Evaluate a single field condition"""
        field = condition.get('field')
        operator = condition.get('operator')
        value = condition.get('value')
        
        if not field or not operator:
            return False
        
        field_value = self.get_field_value(profile, field)
        return self.compare_values(field_value, operator, value)
    
    def evaluate_criteria(self, profile: Dict, criteria: Dict) -> bool:
        """Recursive method to evaluate decision tree criteria"""
        if 'operator' not in criteria:
            return False
        
        operator = criteria.get('operator', '').upper()
        conditions = criteria.get('conditions', [])
        
        if not conditions:
            return False
        
        results = []
        
        for condition in conditions:
            if 'field' in condition:
                # Simple field condition
                results.append(self.evaluate_field_condition(profile, condition))
            elif 'operator' in condition:
                # Nested condition
                results.append(self.evaluate_criteria(profile, condition))
        
        if not results:
            return False
        
        # Apply operator
        if operator == 'AND':
            return all(results)
        elif operator == 'OR':
            return any(results)
        
        return False
    
    def evaluate_resident(self, profile: Dict, program_data: Dict) -> bool:
        """Evaluate a single resident against program criteria"""
        criteria = program_data.get('criteria')
        if not criteria:
            return False
        
        return self.evaluate_criteria(profile, criteria)
    
    def get_eligible_residents(
        self, 
        residents_data: List[Dict], 
        program_data: Dict, 
        purok: Optional[str] = None
    ) -> List[Dict]:
        """Get all eligible residents for a program (optionally filtered by purok)"""
        eligible = []
        
        for resident_data in residents_data:
            profile = resident_data.get('profile', {})
            
            # Filter by purok if specified
            if purok is not None:
                resident_purok = profile.get('demographics', {}).get('purok', 'n/a')
                if resident_purok != purok.lower():
                    continue
            
            if self.evaluate_resident(profile, program_data):
                # Return the full resident data, not just the resident dict
                eligible.append(resident_data.get('resident', {}))
        
        return eligible
    
    def get_resident_programs(
        self, 
        resident_data: Dict, 
        programs_data: List[Dict]
    ) -> List[Dict]:
        """Get all programs a resident is eligible for"""
        profile = resident_data.get('profile', {})
        eligible_programs = []
        
        for program_data in programs_data:
            if program_data.get('is_active', True):
                if self.evaluate_resident(profile, program_data):
                    eligible_programs.append(program_data)
        
        return eligible_programs
    
    def get_purok_eligibility_stats(
        self, 
        residents_data: List[Dict], 
        program_data: Dict, 
        specific_purok: Optional[str] = None
    ) -> List[Dict[str, Any]]:
        """Returns statistics per purok (total residents, eligible count, percentage)"""
        # Get all puroks
        puroks = self.get_all_puroks([r.get('resident', {}) for r in residents_data])
        stats = []
        
        for purok_data in puroks:
            purok = purok_data['token']
            
            # Skip if specific purok is requested and doesn't match
            if specific_purok is not None and purok != specific_purok.lower():
                continue
            
            purok_residents = []
            eligible_count = 0
            
            for resident_data in residents_data:
                profile = resident_data.get('profile', {})
                resident_purok = profile.get('demographics', {}).get('purok', 'n/a')
                
                if resident_purok == purok:
                    purok_residents.append(resident_data)
                    
                    if self.evaluate_resident(profile, program_data):
                        eligible_count += 1
            
            total_residents = len(purok_residents)
            eligibility_percentage = (
                (eligible_count / total_residents * 100) 
                if total_residents > 0 
                else 0
            )
            
            stats.append({
                'purok': purok,
                'purok_display': purok_data['name'],
                'total_residents': total_residents,
                'eligible_count': eligible_count,
                'eligibility_percentage': round(eligibility_percentage, 2),
            })
        
        return stats
    
    def identify_target_puroks(
        self, 
        stats: List[Dict], 
        threshold: float = 0.5
    ) -> List[str]:
        """Identifies which puroks should be recommended based on eligibility percentage"""
        target_puroks = []
        
        for stat in stats:
            if stat.get('eligibility_percentage', 0) >= (threshold * 100):
                target_puroks.append(stat.get('purok'))
        
        return target_puroks
    
    def get_program_recommendations_by_purok(
        self, 
        residents_data: List[Dict], 
        program_data: Dict
    ) -> List[Dict[str, Any]]:
        """Groups eligible residents by purok and identifies target puroks"""
        stats = self.get_purok_eligibility_stats(residents_data, program_data)
        target_puroks = self.identify_target_puroks(stats)
        
        recommendations = []
        
        for purok_stat in stats:
            purok = purok_stat['purok']
            eligible_residents = self.get_eligible_residents(
                residents_data, 
                program_data, 
                purok
            )
            
            recommendations.append({
                'purok': purok_stat['purok_display'],
                'purok_token': purok,
                'total_residents': purok_stat['total_residents'],
                'eligible_count': purok_stat['eligible_count'],
                'eligibility_percentage': purok_stat['eligibility_percentage'],
                'is_recommended': purok in target_puroks,
                'eligible_residents': eligible_residents,
            })
        
        # Sort by eligibility percentage descending
        recommendations.sort(key=lambda x: x['eligibility_percentage'], reverse=True)
        
        return recommendations

