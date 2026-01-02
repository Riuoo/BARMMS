"""
Analytics Service for BARMMS
Provides basic analytics for blotters, documents, and other data
1:1 conversion from PHP logic
"""
import re
from collections import Counter, defaultdict
from typing import List, Dict, Any, Optional
from datetime import datetime, timedelta
from dateutil import parser as date_parser
from dateutil.tz import UTC


class AnalyticsService:
    """Service for performing basic analytics operations"""
    
    @staticmethod
    def now_naive() -> datetime:
        """Get current datetime as timezone-naive (ensures consistency)"""
        now = datetime.now()
        # Ensure it's naive (remove timezone if present)
        return now.replace(tzinfo=None) if now.tzinfo else now
    
    @staticmethod
    def parse_date(date_value: Any) -> Optional[datetime]:
        """
        Parse date from various formats (ISO8601 string, datetime object, etc.)
        Returns timezone-naive datetime to match PHP behavior
        """
        if not date_value:
            return None
        
        dt = None
        
        if isinstance(date_value, datetime):
            dt = date_value
        elif isinstance(date_value, str):
            try:
                # Try ISO8601 format first
                dt = date_parser.parse(date_value)
            except:
                try:
                    # Fallback to fromisoformat
                    dt = datetime.fromisoformat(date_value.replace('Z', '+00:00'))
                except:
                    return None
        
        if dt is None:
            return None
        
        # Normalize to timezone-naive (remove timezone info)
        # This matches PHP behavior where dates are stored without timezone
        if dt.tzinfo is not None:
            # Convert to UTC first, then remove timezone info
            # This ensures we preserve the actual time value
            dt_utc = dt.astimezone(UTC)
            dt = dt_utc.replace(tzinfo=None)
        
        return dt
    
    @staticmethod
    def extract_purok(address: str) -> str:
        """
        Extract purok from address string
        Matches PHP regex: /Purok\s*\d+/i
        """
        if not address:
            return 'Unknown'
        
        match = re.search(r'Purok\s*\d+', address, re.IGNORECASE)
        if match:
            return match.group(0)
        
        return 'Unknown'
    
    def analyze_blotters(self, blotters: List[Dict[str, Any]]) -> Dict[str, Any]:
        """
        Analyze blotter data - 1:1 conversion from BlotterAnalysisService
        """
        if not blotters:
            return {
                'purokCounts': {},
                'respondentTypeCounts': {'registered': 0, 'unregistered': 0},
                'purokTypeBreakdown': {},
                'totalReports': 0,
                'totalPuroks': 0,
                'analysis': {}
            }
        
        # Count by purok
        purok_counts = Counter()
        respondent_type_counts = {'registered': 0, 'unregistered': 0}
        purok_type_breakdown = defaultdict(lambda: defaultdict(int))
        
        for blotter in blotters:
            # Determine purok
            if blotter.get('respondent_id'):
                # Registered respondent - use their address
                respondent = blotter.get('respondent', {})
                address = respondent.get('address', '') if respondent else ''
                purok = self.extract_purok(address)
            else:
                # Unregistered respondent
                purok = 'Unregistered'
            
            purok_counts[purok] += 1
            
            # Count by respondent type
            if blotter.get('respondent_id'):
                respondent_type_counts['registered'] += 1
            else:
                respondent_type_counts['unregistered'] += 1
            
            # Type breakdown by purok
            blotter_type = blotter.get('type', 'Unknown')
            purok_type_breakdown[purok][blotter_type] += 1
        
        # Sort purok counts (descending)
        purok_counts_sorted = dict(sorted(purok_counts.items(), key=lambda x: x[1], reverse=True))
        
        # Convert defaultdict to regular dict for JSON serialization
        purok_type_breakdown_dict = {
            purok: dict(types) 
            for purok, types in purok_type_breakdown.items()
        }
        
        total_reports = sum(purok_counts.values())
        total_puroks = len(purok_counts)
        
        # Generate insights
        analysis = self._generate_blotter_insights(
            purok_counts_sorted, 
            total_reports, 
            respondent_type_counts
        )
        
        return {
            'purokCounts': purok_counts_sorted,
            'respondentTypeCounts': respondent_type_counts,
            'purokTypeBreakdown': purok_type_breakdown_dict,
            'totalReports': total_reports,
            'totalPuroks': total_puroks,
            'analysis': analysis
        }
    
    def _generate_blotter_insights(
        self, 
        purok_counts: Dict[str, int], 
        total_reports: int, 
        respondent_type_counts: Dict[str, int]
    ) -> Dict[str, Any]:
        """Generate insights from purok data"""
        # Top 3 puroks
        top_3_puroks = dict(list(purok_counts.items())[:3])
        top_3_total = sum(top_3_puroks.values())
        top_3_percentage = round((top_3_total / total_reports * 100), 1) if total_reports > 0 else 0
        
        # Average per purok
        average_per_purok = round(total_reports / len(purok_counts), 1) if purok_counts else 0
        
        # Most active purok
        most_active_purok = list(purok_counts.keys())[0] if purok_counts else 'N/A'
        
        # Calculate distribution
        distribution = {}
        for purok, count in purok_counts.items():
            percentage = round((count / total_reports * 100), 1) if total_reports > 0 else 0
            distribution[purok] = {
                'count': count,
                'percentage': percentage
            }
        
        # Respondent type percentages
        registered_percentage = round(
            (respondent_type_counts['registered'] / total_reports * 100), 1
        ) if total_reports > 0 else 0
        unregistered_percentage = round(
            (respondent_type_counts['unregistered'] / total_reports * 100), 1
        ) if total_reports > 0 else 0
        
        return {
            'top3Puroks': top_3_puroks,
            'top3Percentage': top_3_percentage,
            'averagePerPurok': average_per_purok,
            'mostActivePurok': most_active_purok,
            'distribution': distribution,
            'respondentTypeAnalysis': {
                'registered': {
                    'count': respondent_type_counts['registered'],
                    'percentage': registered_percentage
                },
                'unregistered': {
                    'count': respondent_type_counts['unregistered'],
                    'percentage': unregistered_percentage
                }
            }
        }
    
    def analyze_documents(self, requests: List[Dict[str, Any]]) -> Dict[str, Any]:
        """
        Analyze document request data - 1:1 conversion from DocumentRequestAnalysisService
        """
        if not requests:
            return {
                'purokCounts': {},
                'purokTypeBreakdown': {},
                'totalRequests': 0,
                'totalPuroks': 0,
                'analysis': {}
            }
        
        # Count by purok
        purok_counts = Counter()
        purok_type_breakdown = defaultdict(lambda: defaultdict(int))
        
        for request in requests:
            # Extract purok from resident address
            resident = request.get('resident', {})
            address = resident.get('address', '') if resident else ''
            purok = self.extract_purok(address)
            
            purok_counts[purok] += 1
            
            # Type breakdown by purok
            document_type = request.get('document_type', 'Unknown')
            purok_type_breakdown[purok][document_type] += 1
        
        # Sort purok counts (descending)
        purok_counts_sorted = dict(sorted(purok_counts.items(), key=lambda x: x[1], reverse=True))
        
        # Convert defaultdict to regular dict for JSON serialization
        purok_type_breakdown_dict = {
            purok: dict(types) 
            for purok, types in purok_type_breakdown.items()
        }
        
        total_requests = sum(purok_counts.values())
        total_puroks = len(purok_counts)
        
        # Generate insights
        analysis = self._generate_document_insights(purok_counts_sorted, total_requests)
        
        return {
            'purokCounts': purok_counts_sorted,
            'purokTypeBreakdown': purok_type_breakdown_dict,
            'totalRequests': total_requests,
            'totalPuroks': total_puroks,
            'analysis': analysis
        }
    
    def _generate_document_insights(
        self, 
        purok_counts: Dict[str, int], 
        total_requests: int
    ) -> Dict[str, Any]:
        """Generate insights from document request data"""
        # Top 3 puroks
        top_3_puroks = dict(list(purok_counts.items())[:3])
        top_3_total = sum(top_3_puroks.values())
        top_3_percentage = round((top_3_total / total_requests * 100), 1) if total_requests > 0 else 0
        
        # Average per purok
        average_per_purok = round(total_requests / len(purok_counts), 1) if purok_counts else 0
        
        # Most active purok
        most_active_purok = list(purok_counts.keys())[0] if purok_counts else 'N/A'
        
        # Calculate distribution
        distribution = {}
        for purok, count in purok_counts.items():
            percentage = round((count / total_requests * 100), 1) if total_requests > 0 else 0
            distribution[purok] = {
                'count': count,
                'percentage': percentage
            }
        
        return {
            'top3Puroks': top_3_puroks,
            'top3Percentage': top_3_percentage,
            'averagePerPurok': average_per_purok,
            'mostActivePurok': most_active_purok,
            'distribution': distribution
        }
    
    @staticmethod
    def extract_purok_from_address(address: str) -> str:
        """Extract purok from address - handles Purok 1-9 and Other"""
        if not address:
            return 'Other'
        
        for i in range(1, 10):
            if f'Purok {i}' in address or f'purok {i}' in address:
                return f'Purok {i}'
        
        return 'Other'
    
    @staticmethod
    def get_age_bracket(age: Optional[int]) -> str:
        """Get age bracket from age"""
        if age is None:
            return 'Unknown'
        
        if age <= 12:
            return '0-12'
        elif age <= 17:
            return '13-17'
        elif age <= 35:
            return '18-35'
        elif age <= 60:
            return '36-60'
        else:
            return '61+'
    
    def analyze_health_report(self, data: Dict[str, Any]) -> Dict[str, Any]:
        """
        Analyze health report data - 1:1 conversion from HealthReportController::healthReport()
        """
        residents = data.get('residents', [])
        medical_records = data.get('medical_records', [])
        health_activities = data.get('health_activities', [])
        medicine_requests = data.get('medicine_requests', [])
        medicine_transactions = data.get('medicine_transactions', [])
        medicines = data.get('medicines', [])
        medicine_batches = data.get('medicine_batches', [])
        
        # Basic counts
        total_residents = len(residents)
        total_consultations = len(medical_records)
        total_activities = len(health_activities)
        
        # PWD distribution
        pwd_distribution = Counter()
        for resident in residents:
            is_pwd = resident.get('is_pwd', False)
            pwd_distribution[is_pwd] += 1
        
        pwd_distribution_list = [
            {'is_pwd': bool(k), 'count': v} 
            for k, v in pwd_distribution.items()
        ]
        
        # Monthly consultation trends (last 6 months)
        monthly_consultations = defaultdict(int)
        six_months_ago = self.now_naive() - timedelta(days=180)
        
        for record in medical_records:
            consultation_date = record.get('consultation_datetime')
            dt = self.parse_date(consultation_date)
            if dt and dt >= six_months_ago:
                month_key = dt.strftime('%Y-%m')
                monthly_consultations[month_key] += 1
        
        monthly_consultations_list = [
            {'month': k, 'count': v}
            for k, v in sorted(monthly_consultations.items())
        ]
        
        # BHW stats (consultations this month)
        current_month = self.now_naive().strftime('%Y-%m')
        bhw_consultations = 0
        for record in medical_records:
            dt = self.parse_date(record.get('consultation_datetime'))
            if dt and dt.strftime('%Y-%m') == current_month:
                bhw_consultations += 1
        
        # Medicine analytics (30-day window)
        thirty_days_ago = self.now_naive() - timedelta(days=30)
        now_dt = self.now_naive()
        
        # Top requested medicines (last 30 days)
        medicine_request_counts = Counter()
        for request in medicine_requests:
            request_date = request.get('request_date')
            dt = self.parse_date(request_date)
            if dt and thirty_days_ago <= dt <= now_dt:
                medicine_id = request.get('medicine_id')
                if medicine_id:
                    medicine_request_counts[medicine_id] += 1
        
        top_requested_medicines = []
        for medicine_id, count in medicine_request_counts.most_common(5):
            medicine = next((m for m in medicines if m.get('id') == medicine_id), None)
            if medicine:
                top_requested_medicines.append({
                    'medicine_id': medicine_id,
                    'requests': count,
                    'medicine': {
                        'id': medicine.get('id'),
                        'name': medicine.get('name')
                    }
                })
        
        # Top dispensed medicines (last 30 days)
        medicine_dispense_counts = defaultdict(int)
        for transaction in medicine_transactions:
            if transaction.get('transaction_type') == 'OUT':
                transaction_date = transaction.get('transaction_date')
                dt = self.parse_date(transaction_date)
                if dt and thirty_days_ago <= dt <= now_dt:
                    medicine_id = transaction.get('medicine_id')
                    quantity = transaction.get('quantity', 0)
                    if medicine_id:
                        medicine_dispense_counts[medicine_id] += int(quantity) if quantity else 0
        
        top_dispensed_medicines = []
        for medicine_id, total_qty in sorted(medicine_dispense_counts.items(), key=lambda x: x[1], reverse=True)[:5]:
            medicine = next((m for m in medicines if m.get('id') == medicine_id), None)
            if medicine:
                top_dispensed_medicines.append({
                    'medicine_id': medicine_id,
                    'total_qty': total_qty,
                    'medicine': {
                        'id': medicine.get('id'),
                        'name': medicine.get('name')
                    }
                })
        
        return {
            'totalResidents': total_residents,
            'totalConsultations': total_consultations,
            'totalActivities': total_activities,
            'pwdDistribution': pwd_distribution_list,
            'monthlyConsultations': monthly_consultations_list,
            'bhwStats': {
                'consultations': bhw_consultations
            },
            'topRequestedMedicines': top_requested_medicines,
            'topDispensedMedicines': top_dispensed_medicines
        }
    
    def analyze_medicine_report(
        self,
        data: Dict[str, Any],
        start_date: str,
        end_date: str
    ) -> Dict[str, Any]:
        """
        Analyze medicine report - 1:1 conversion from MedicineController::report()
        """
        medicine_requests = data.get('medicine_requests', [])
        medicine_transactions = data.get('medicine_transactions', [])
        medicines = data.get('medicines', [])
        residents = data.get('residents', [])
        
        start_dt = self.parse_date(start_date) or self.now_naive().replace(day=1)
        end_dt = self.parse_date(end_date) or self.now_naive()
        trend_start = (start_dt - timedelta(days=150)).replace(day=1)  # ~5 months before
        
        # Create lookup dictionaries
        medicine_lookup = {m.get('id'): m for m in medicines}
        resident_lookup = {r.get('id'): r for r in residents}
        
        # Filter transactions by date
        filtered_transactions = []
        for transaction in medicine_transactions:
            transaction_date = transaction.get('transaction_date')
            dt = self.parse_date(transaction_date)
            if dt and start_dt <= dt <= end_dt:
                filtered_transactions.append(transaction)
        
        # Top dispensed medicines
        dispense_counts = defaultdict(int)
        for transaction in filtered_transactions:
            if transaction.get('transaction_type') == 'OUT':
                medicine_id = transaction.get('medicine_id')
                quantity = transaction.get('quantity', 0)
                if medicine_id:
                    dispense_counts[medicine_id] += int(quantity) if quantity else 0
        
        top_dispensed = []
        for medicine_id, total_qty in sorted(dispense_counts.items(), key=lambda x: x[1], reverse=True)[:10]:
            medicine = medicine_lookup.get(medicine_id)
            if medicine:
                top_dispensed.append({
                    'medicine_id': medicine_id,
                    'total_qty': total_qty,
                    'medicine': {
                        'id': medicine.get('id'),
                        'name': medicine.get('name')
                    }
                })
        
        # Category distribution
        category_counts = Counter()
        for medicine in medicines:
            category = medicine.get('category')
            if category:
                category_counts[category] += 1
        
        category_counts_list = [
            {'category': k, 'count': v}
            for k, v in category_counts.most_common()
        ]
        
        # Monthly dispense trend (last 6 months)
        monthly_dispensed = defaultdict(int)
        for transaction in medicine_transactions:
            if transaction.get('transaction_type') == 'OUT':
                transaction_date = transaction.get('transaction_date')
                dt = self.parse_date(transaction_date)
                if dt and trend_start <= dt <= end_dt:
                    month_key = dt.strftime('%Y-%m')
                    quantity = transaction.get('quantity', 0)
                    monthly_dispensed[month_key] += int(quantity) if quantity else 0
        
        monthly_dispensed_list = [
            {'month': k, 'qty': v}
            for k, v in sorted(monthly_dispensed.items())
        ]
        
        # Requests by age bracket
        requests_by_age = defaultdict(int)
        for request in medicine_requests:
            request_date = request.get('request_date')
            dt = self.parse_date(request_date)
            if dt and start_dt <= dt <= end_dt:
                resident_id = request.get('resident_id')
                if resident_id:
                    resident = resident_lookup.get(resident_id)
                    if resident:
                        age = resident.get('age')
                        bracket = self.get_age_bracket(age)
                        requests_by_age[bracket] += 1
        
        requests_by_age_list = [
            {'bracket': k, 'count': v}
            for k, v in sorted(requests_by_age.items())
        ]
        
        # Top requested people by purok
        purok_people = defaultdict(set)
        for request in medicine_requests:
            request_date = request.get('request_date')
            dt = self.parse_date(request_date)
            if dt and start_dt <= dt <= end_dt:
                resident_id = request.get('resident_id')
                if resident_id:
                    resident = resident_lookup.get(resident_id)
                    if resident:
                        address = resident.get('address', '')
                        purok = self.extract_purok_from_address(address)
                        purok_people[purok].add(resident_id)
        
        top_requested_people_by_purok = [
            {'purok': k, 'people': len(v)}
            for k, v in sorted(purok_people.items())
        ]
        
        # Consolidated request analytics
        request_analytics = self._get_consolidated_request_analytics(
            medicine_requests, medicines, residents, start_dt, end_dt
        )
        
        return {
            'requestAnalytics': request_analytics,
            'topDispensed': top_dispensed,
            'categoryCounts': category_counts_list,
            'monthlyDispensed': monthly_dispensed_list,
            'requestsByAgeBracket': requests_by_age_list,
            'topRequestedPeopleByPurok': top_requested_people_by_purok
        }
    
    def _get_consolidated_request_analytics(
        self,
        medicine_requests: List[Dict[str, Any]],
        medicines: List[Dict[str, Any]],
        residents: List[Dict[str, Any]],
        start_dt: datetime,
        end_dt: datetime
    ) -> Dict[str, Any]:
        """Get consolidated medicine request analytics"""
        medicine_lookup = {m.get('id'): m for m in medicines}
        resident_lookup = {r.get('id'): r for r in residents}
        
        # Filter requests by date
        filtered_requests = []
        for request in medicine_requests:
            request_date = request.get('request_date')
            dt = self.parse_date(request_date)
            if dt and start_dt <= dt <= end_dt:
                filtered_requests.append(request)
        
        # Group by purok and medicine
        purok_medicine_counts = defaultdict(lambda: defaultdict(int))
        
        for request in filtered_requests:
            medicine_id = request.get('medicine_id')
            resident_id = request.get('resident_id')
            
            if medicine_id and resident_id:
                medicine = medicine_lookup.get(medicine_id)
                resident = resident_lookup.get(resident_id)
                
                if medicine and resident:
                    medicine_name = medicine.get('name', 'Unknown')
                    address = resident.get('address', '')
                    purok = self.extract_purok_from_address(address)
                    
                    purok_medicine_counts[purok][medicine_name] += 1
        
        # Format by purok (top 5 per purok)
        by_purok = {}
        for purok, medicine_counts in purok_medicine_counts.items():
            top_medicines = sorted(medicine_counts.items(), key=lambda x: x[1], reverse=True)[:5]
            by_purok[purok] = [
                {'medicine_name': name, 'requests': count}
                for name, count in top_medicines
            ]
        
        # Overall top requested medicines
        overall_counts = defaultdict(int)
        for request in filtered_requests:
            medicine_id = request.get('medicine_id')
            if medicine_id:
                medicine = medicine_lookup.get(medicine_id)
                if medicine:
                    medicine_name = medicine.get('name', 'Unknown')
                    overall_counts[medicine_name] += 1
        
        overall_top = [
            {
                'medicine': {'name': name},
                'requests': count
            }
            for name, count in sorted(overall_counts.items(), key=lambda x: x[1], reverse=True)[:10]
        ]
        
        return {
            'by_purok': by_purok,
            'overall': overall_top
        }
    
    def analyze_dashboard(self, data: Dict[str, Any]) -> Dict[str, Any]:
        """
        Analyze dashboard data - 1:1 conversion from AdminDashboardController::index()
        """
        residents = data.get('residents', [])
        document_requests = data.get('document_requests', [])
        
        # Age brackets
        age_brackets = Counter()
        for resident in residents:
            age = resident.get('age')
            if age is None:
                bracket = 'Unknown'
            elif age <= 17:
                bracket = '0-17'
            elif age <= 35:
                bracket = '18-35'
            elif age <= 50:
                bracket = '36-50'
            elif age <= 65:
                bracket = '51-65'
            else:
                bracket = '65+'
            
            age_brackets[bracket] += 1
        
        resident_demographics = [
            {'age_bracket': k, 'count': v}
            for k, v in age_brackets.items()
        ]
        
        # Monthly resident registration trends (current year)
        current_year = self.now_naive().year
        monthly_registrations = defaultdict(int)
        
        for resident in residents:
            created_at = resident.get('created_at')
            dt = self.parse_date(created_at)
            if dt and dt.year == current_year:
                month = dt.month
                monthly_registrations[month] += 1
        
        resident_trends = [
            {'month': k, 'count': v}
            for k, v in sorted(monthly_registrations.items())
        ]
        
        # Document request types distribution
        document_type_counts = Counter()
        for request in document_requests:
            doc_type = request.get('document_type')
            if doc_type:
                document_type_counts[doc_type] += 1
        
        document_request_types = [
            {'document_type': k, 'count': v}
            for k, v in document_type_counts.items()
        ]
        
        return {
            'residentDemographics': resident_demographics,
            'residentTrends': resident_trends,
            'documentRequestTypes': document_request_types
        }

