"""
Analytics Service
Provides high-level analytics functions for health and demographic data
"""
import numpy as np
import pandas as pd
from .clustering_service import ClusteringService
from .decision_tree_service import DecisionTreeService


class AnalyticsService:
    """High-level analytics service combining clustering and ML"""
    
    def __init__(self):
        self.clustering_service = ClusteringService()
        self.decision_tree_service = DecisionTreeService()
    
    def analyze_health_risk(self, residents, model_type='random_forest'):
        """
        Analyze health risk for residents
        
        Args:
            residents: List of resident dictionaries with features
            model_type: Model type to use
            
        Returns:
            Analysis results with risk predictions
        """
        # Convert residents to feature vectors
        samples, labels, resident_refs = self._prepare_health_data(residents)
        
        if len(samples) < 10:
            return {
                'error': 'Insufficient data (minimum 10 residents required)',
                'sample_size': len(samples)
            }
        
        # Train model
        result = self.decision_tree_service.train(
            samples=samples,
            labels=labels,
            model_type=model_type,
            test_size=0.3
        )
        
        if 'error' in result:
            return result
        
        # Make predictions for all residents
        predictions = self.decision_tree_service.predict(
            model_id=result['model_id'],
            samples=samples
        )
        
        # Combine results
        risk_analysis = []
        for i, resident in enumerate(resident_refs):
            risk_analysis.append({
                'resident_id': resident.get('id'),
                'resident_name': resident.get('name'),
                'predicted_risk': predictions['predictions'][i],
                'probability': predictions['probabilities'][i] if predictions['probabilities'] else None,
                'actual_risk': labels[i]
            })
        
        return {
            'model_info': result,
            'risk_analysis': risk_analysis,
            'sample_size': len(samples),
            'model_type': model_type
        }
    
    def analyze_service_eligibility(self, residents, model_type='decision_tree'):
        """
        Analyze service eligibility for residents
        
        Args:
            residents: List of resident dictionaries
            model_type: Model type to use
            
        Returns:
            Eligibility analysis results
        """
        samples, labels, resident_refs = self._prepare_eligibility_data(residents)
        
        if len(samples) < 10:
            return {
                'error': 'Insufficient data (minimum 10 residents required)',
                'sample_size': len(samples)
            }
        
        # Train model (label encoding will be handled automatically by decision_tree_service)
        result = self.decision_tree_service.train(
            samples=samples,
            labels=labels,
            model_type=model_type,
            test_size=0.3
        )
        
        if 'error' in result:
            return result
        
        # Make predictions
        predictions = self.decision_tree_service.predict(
            model_id=result['model_id'],
            samples=samples
        )
        
        eligibility_analysis = []
        for i, resident in enumerate(resident_refs):
            # Get probability if available
            probability = None
            if predictions['probabilities']:
                prob_array = predictions['probabilities'][i]
                # For binary classification, get probability of class 1 (Eligible)
                if len(prob_array) > 1:
                    probability = prob_array[1]  # Probability of being Eligible
                else:
                    probability = prob_array[0]
            
            eligibility_analysis.append({
                'resident_id': resident.get('id'),
                'resident_name': resident.get('name'),
                'predicted_eligibility': predictions['predictions'][i],
                'probability': probability,
                'actual_eligibility': labels[i]
            })
        
        return {
            'model_info': result,
            'eligibility_analysis': eligibility_analysis,
            'sample_size': len(samples),
            'model_type': model_type
        }
    
    def analyze_demographics(self, residents):
        """
        Perform demographic clustering analysis
        
        Args:
            residents: List of resident dictionaries
            
        Returns:
            Demographic clustering results
        """
        samples = self._prepare_demographic_features(residents)
        
        if len(samples) < 3:
            return {
                'error': 'Insufficient data for clustering (minimum 3 residents required)',
                'sample_size': len(samples)
            }
        
        # Find optimal K
        optimal_k_result = self.clustering_service.find_optimal_k(
            samples=samples,
            max_k=min(10, len(samples) - 1),
            method='silhouette'
        )
        
        optimal_k = optimal_k_result.get('optimal_k', 3)
        
        # Perform clustering
        clustering_result = self.clustering_service.kmeans(
            samples=samples,
            k=optimal_k,
            num_runs=5
        )
        
        return {
            'optimal_k': optimal_k,
            'optimal_k_analysis': optimal_k_result,
            'clustering': clustering_result,
            'sample_size': len(samples)
        }
    
    def _prepare_health_data(self, residents):
        """Prepare health risk data from residents"""
        samples = []
        labels = []
        resident_refs = []
        
        for resident in residents:
            # Extract features
            features = [
                float(resident.get('age', 0)),
                float(resident.get('family_size', 0)),
                self._encode_education(resident.get('education_level', '')),
                self._encode_income(resident.get('income_level', '')),
                self._encode_employment(resident.get('employment_status', '')),
            ]
            
            # Extract label (PWD status)
            is_pwd = resident.get('is_pwd', False)
            label = 1.0 if (is_pwd == True or is_pwd == 1 or is_pwd == '1') else 0.0
            
            samples.append(features)
            labels.append(label)
            resident_refs.append(resident)
        
        return samples, labels, resident_refs
    
    def _prepare_eligibility_data(self, residents):
        """Prepare service eligibility data from residents"""
        samples = []
        labels = []
        resident_refs = []
        
        for resident in residents:
            features = [
                float(resident.get('age', 0)),
                float(resident.get('family_size', 0)),
                self._encode_education(resident.get('education_level', '')),
                self._encode_income(resident.get('income_level', '')),
                self._encode_employment(resident.get('employment_status', '')),
                1.0 if (resident.get('is_pwd', False) == True or resident.get('is_pwd', False) == 1 or resident.get('is_pwd', False) == '1') else 0.0
            ]
            
            # Determine eligibility based on income and age
            income_level = resident.get('income_level', '')
            age = float(resident.get('age', 0))
            
            if income_level in ['Low', 'Lower Middle'] or age >= 60:
                label = 'Eligible'
            else:
                label = 'Not Eligible'
            
            samples.append(features)
            labels.append(label)
            resident_refs.append(resident)
        
        return samples, labels, resident_refs
    
    def _prepare_demographic_features(self, residents):
        """Prepare demographic features for clustering"""
        samples = []
        
        for resident in residents:
            features = [
                float(resident.get('age', 0)),
                float(resident.get('family_size', 0)),
                self._encode_education(resident.get('education_level', '')),
                self._encode_income(resident.get('income_level', '')),
                self._encode_employment(resident.get('employment_status', '')),
                1.0 if (resident.get('is_pwd', False) == True or resident.get('is_pwd', False) == 1 or resident.get('is_pwd', False) == '1') else 0.0
            ]
            samples.append(features)
        
        return samples
    
    def _encode_education(self, education_level):
        """Encode education level to numeric"""
        mapping = {
            'Elementary': 1,
            'High School': 2,
            'College': 3,
            'Graduate': 4,
            '': 0
        }
        return mapping.get(education_level, 0)
    
    def _encode_income(self, income_level):
        """Encode income level to numeric"""
        mapping = {
            'Low': 1,
            'Lower Middle': 2,
            'Middle': 3,
            'Upper Middle': 4,
            'High': 5,
            '': 0
        }
        return mapping.get(income_level, 0)
    
    def _encode_employment(self, employment_status):
        """Encode employment status to numeric"""
        mapping = {
            'Unemployed': 0,
            'Part-time': 1,
            'Self-employed': 2,
            'Full-time': 3,
            '': 0
        }
        return mapping.get(employment_status, 0)
    
    def _encode_health_status(self, health_status):
        """Encode health status to numeric"""
        mapping = {
            'Critical': 0,
            'Poor': 1,
            'Fair': 2,
            'Good': 3,
            'Excellent': 4,
            '': 2
        }
        return mapping.get(health_status, 2)
    
    def analyze_health_condition(self, residents, model_type='decision_tree'):
        """
        Analyze health condition for residents
        
        Args:
            residents: List of resident dictionaries
            model_type: Model type to use
            
        Returns:
            Health condition analysis results
        """
        samples, labels, resident_refs = self._prepare_health_condition_data(residents)
        
        if len(samples) < 10:
            return {
                'error': 'Insufficient data (minimum 10 residents required)',
                'sample_size': len(samples)
            }
        
        # Train model
        result = self.decision_tree_service.train(
            samples=samples,
            labels=labels,
            model_type=model_type,
            test_size=0.3
        )
        
        if 'error' in result:
            return result
        
        # Make predictions
        predictions = self.decision_tree_service.predict(
            model_id=result['model_id'],
            samples=samples
        )
        
        condition_analysis = []
        for i, resident in enumerate(resident_refs):
            condition_analysis.append({
                'resident_id': resident.get('id'),
                'resident_name': resident.get('name'),
                'predicted': predictions['predictions'][i],
                'actual': labels[i],
                'correct': predictions['predictions'][i] == labels[i],
                'probability': predictions['probabilities'][i] if predictions['probabilities'] else None
            })
        
        return {
            'model_info': result,
            'predictions': condition_analysis,
            'testing_predictions': condition_analysis,
            'training_predictions': [],
            'sample_size': len(samples),
            'model_type': model_type
        }
    
    def analyze_program_recommendation(self, residents, model_type='random_forest'):
        """
        Analyze program recommendation for residents
        
        Args:
            residents: List of resident dictionaries
            model_type: Model type to use
            
        Returns:
            Program recommendation analysis results
        """
        samples, labels, resident_refs = self._prepare_program_recommendation_data(residents)
        
        if len(samples) < 10:
            return {
                'error': 'Insufficient data (minimum 10 residents required)',
                'sample_size': len(samples)
            }
        
        # Train model
        result = self.decision_tree_service.train(
            samples=samples,
            labels=labels,
            model_type=model_type,
            test_size=0.3
        )
        
        if 'error' in result:
            return result
        
        # Make predictions
        predictions = self.decision_tree_service.predict(
            model_id=result['model_id'],
            samples=samples
        )
        
        recommendation_analysis = []
        for i, resident in enumerate(resident_refs):
            recommendation_analysis.append({
                'resident_id': resident.get('id'),
                'resident_name': resident.get('name'),
                'predicted': predictions['predictions'][i],
                'actual': labels[i],
                'correct': predictions['predictions'][i] == labels[i],
                'probability': predictions['probabilities'][i] if predictions['probabilities'] else None
            })
        
        return {
            'model_info': result,
            'predictions': recommendation_analysis,
            'sample_size': len(samples),
            'model_type': model_type
        }
    
    def _prepare_health_condition_data(self, residents):
        """Prepare health condition data from residents"""
        samples = []
        labels = []
        resident_refs = []
        
        for resident in residents:
            features = [
                float(resident.get('age', 0)),
                float(resident.get('family_size', 0)),
                self._encode_education(resident.get('education_level', '')),
                self._encode_income(resident.get('income_level', '')),
                self._encode_employment(resident.get('employment_status', '')),
            ]
            
            # Extract label (PWD status) and categorize
            is_pwd = resident.get('is_pwd', False)
            if is_pwd == True or is_pwd == 1 or is_pwd == '1':
                label = 'PWD - Needs Support'
            else:
                label = 'Non-PWD - Standard Care'
            
            samples.append(features)
            labels.append(label)
            resident_refs.append(resident)
        
        return samples, labels, resident_refs
    
    def _prepare_program_recommendation_data(self, residents):
        """Prepare program recommendation data from residents"""
        samples = []
        labels = []
        resident_refs = []
        
        for resident in residents:
            features = [
                float(resident.get('age', 0)),
                float(resident.get('family_size', 0)),
                self._encode_education(resident.get('education_level', '')),
                self._encode_income(resident.get('income_level', '')),
                self._encode_employment(resident.get('employment_status', '')),
                1.0 if (resident.get('is_pwd', False) == True or resident.get('is_pwd', False) == 1 or resident.get('is_pwd', False) == '1') else 0.0
            ]
            
            # Determine program recommendation based on multiple factors
            age = float(resident.get('age', 0))
            income = resident.get('income_level', '')
            is_pwd = resident.get('is_pwd', False)
            employment = resident.get('employment_status', '')
            
            if age >= 60:
                label = 'Senior Care Program'
            elif age < 18 and income in ['Low', 'Lower Middle']:
                label = 'Youth Education Support'
            elif is_pwd == True or is_pwd == 1 or is_pwd == '1':
                label = 'PWD Support Program'
            elif employment == 'Unemployed':
                label = 'Employment Training Program'
            elif income in ['Low', 'Lower Middle']:
                label = 'Financial Assistance Program'
            else:
                label = 'General Community Program'
            
            samples.append(features)
            labels.append(label)
            resident_refs.append(resident)
        
        return samples, labels, resident_refs


