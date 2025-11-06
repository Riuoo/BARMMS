"""
Decision Tree Service using scikit-learn
Provides machine learning decision tree algorithms for BARMMS
"""
import numpy as np
import pandas as pd
from sklearn.tree import DecisionTreeClassifier
from sklearn.ensemble import RandomForestClassifier
from sklearn.model_selection import train_test_split
from sklearn.metrics import accuracy_score, classification_report, confusion_matrix, precision_recall_fscore_support, roc_auc_score
from sklearn.preprocessing import LabelEncoder
import pickle
import json
from datetime import datetime
import os


class DecisionTreeService:
    """Service for training and using decision tree models"""
    
    def __init__(self):
        self.models = {}
        self.models_dir = 'models'
        self.label_encoders = {}
        os.makedirs(self.models_dir, exist_ok=True)
    
    def train(self, samples, labels, model_type='decision_tree', test_size=0.3, random_state=42, **kwargs):
        """
        Train a decision tree model
        
        Args:
            samples: Training samples (list of lists or numpy array)
            labels: Training labels (list or numpy array)
            model_type: 'decision_tree' or 'random_forest'
            test_size: Test set size ratio (0.0 to 1.0)
            random_state: Random seed for reproducibility
            **kwargs: Additional parameters for model configuration
            
        Returns:
            Dictionary with model info, metrics, and feature importance
        """
        try:
            samples = np.array(samples)
            labels = np.array(labels)
            
            if len(samples) == 0 or len(labels) == 0:
                return {'error': 'Empty dataset provided'}
            
            if len(samples) != len(labels):
                return {'error': 'Samples and labels must have the same length'}
            
            # Check if labels are strings and need encoding
            label_encoder = None
            if len(labels) > 0 and (labels.dtype == object or isinstance(labels[0], str)):
                label_encoder = LabelEncoder()
                labels = label_encoder.fit_transform(labels)
            
            # Split data
            if test_size > 0 and test_size < 1:
                X_train, X_test, y_train, y_test = train_test_split(
                    samples, labels, test_size=test_size, random_state=random_state
                )
            else:
                # Use all data for training if test_size is 0
                X_train, X_test = samples, samples
                y_train, y_test = labels, labels
            
            # Get model parameters from kwargs or use defaults
            max_depth = kwargs.get('max_depth', 10)
            min_samples_split = kwargs.get('min_samples_split', 5)
            min_samples_leaf = kwargs.get('min_samples_leaf', 2)
            
            # Train model
            if model_type == 'decision_tree':
                model = DecisionTreeClassifier(
                    max_depth=max_depth,
                    min_samples_split=min_samples_split,
                    min_samples_leaf=min_samples_leaf,
                    random_state=random_state,
                    criterion=kwargs.get('criterion', 'gini')
                )
            elif model_type == 'random_forest':
                n_estimators = kwargs.get('n_estimators', 100)
                model = RandomForestClassifier(
                    n_estimators=n_estimators,
                    max_depth=max_depth,
                    min_samples_split=min_samples_split,
                    min_samples_leaf=min_samples_leaf,
                    random_state=random_state,
                    n_jobs=-1,
                    criterion=kwargs.get('criterion', 'gini')
                )
            else:
                return {'error': f'Unknown model type: {model_type}. Supported types: decision_tree, random_forest'}
            
            model.fit(X_train, y_train)
            
            # Evaluate
            y_train_pred = model.predict(X_train)
            y_test_pred = model.predict(X_test)
            
            train_accuracy = accuracy_score(y_train, y_train_pred)
            test_accuracy = accuracy_score(y_test, y_test_pred)

            # Additional metrics (macro averages)
            precision, recall, f1, _ = precision_recall_fscore_support(
                y_test, y_test_pred, average='macro', zero_division=0
            )
            # ROC-AUC (only if probabilites available and binary/multiclass supported)
            roc_auc = None
            try:
                if hasattr(model, 'predict_proba'):
                    proba = model.predict_proba(X_test)
                    # Handle binary and multiclass with 'ovr'
                    roc_auc = roc_auc_score(y_test, proba, multi_class='ovr')
                    roc_auc = float(roc_auc)
            except Exception:
                roc_auc = None
            
            # Feature importance
            feature_importance = None
            if hasattr(model, 'feature_importances_'):
                feature_importance = model.feature_importances_.tolist()
            
            # Classification report
            class_report = classification_report(
                y_test, y_test_pred, output_dict=True, zero_division=0
            )
            
            # Confusion matrix
            cm = confusion_matrix(y_test, y_test_pred).tolist()
            
            # Save model
            model_id = f"{model_type}_{datetime.now().strftime('%Y%m%d_%H%M%S')}"
            model_path = os.path.join(self.models_dir, f"{model_id}.pkl")
            
            with open(model_path, 'wb') as f:
                pickle.dump(model, f)
            
            # Save label encoder if used
            encoder_path = None
            if label_encoder is not None:
                encoder_path = os.path.join(self.models_dir, f"{model_id}_encoder.pkl")
                with open(encoder_path, 'wb') as f:
                    pickle.dump(label_encoder, f)
            
            self.models[model_id] = {
                'model': model,
                'path': model_path,
                'type': model_type,
                'created_at': datetime.now().isoformat(),
                'label_encoder': label_encoder,
                'encoder_path': encoder_path
            }
            
            # Store encoder for later use
            if label_encoder is not None:
                self.label_encoders[model_id] = label_encoder
            
            return {
                'model_id': model_id,
                'model_type': model_type,
                'metrics': {
                    'train_accuracy': float(train_accuracy),
                    'test_accuracy': float(test_accuracy),
                    'test_precision': float(precision),
                    'test_recall': float(recall),
                    'test_f1_score': float(f1),
                    'roc_auc_score': roc_auc if roc_auc is not None else None,
                    'train_size': len(X_train),
                    'test_size': len(X_test)
                },
                'feature_importance': feature_importance,
                'classification_report': class_report,
                'confusion_matrix': cm,
                'created_at': datetime.now().isoformat()
            }
        except Exception as e:
            return {'error': f'Training error: {str(e)}'}
    
    def predict(self, model_id, samples):
        """
        Make predictions using a trained model
        
        Args:
            model_id: Model identifier
            samples: Samples to predict (list of lists or numpy array)
            
        Returns:
            Dictionary with predictions
        """
        try:
            if model_id not in self.models:
                # Try to load from disk
                model_path = os.path.join(self.models_dir, f"{model_id}.pkl")
                if not os.path.exists(model_path):
                    return {'error': f'Model {model_id} not found'}
                
                with open(model_path, 'rb') as f:
                    model = pickle.load(f)
                
                # Try to load label encoder if exists
                encoder_path = os.path.join(self.models_dir, f"{model_id}_encoder.pkl")
                label_encoder = None
                if os.path.exists(encoder_path):
                    with open(encoder_path, 'rb') as f:
                        label_encoder = pickle.load(f)
                
                self.models[model_id] = {
                    'model': model,
                    'path': model_path,
                    'label_encoder': label_encoder,
                    'encoder_path': encoder_path if label_encoder else None
                }
                
                if label_encoder is not None:
                    self.label_encoders[model_id] = label_encoder
            
            model = self.models[model_id]['model']
            samples = np.array(samples)
            
            if len(samples) == 0:
                return {'error': 'Empty samples provided'}
            
            predictions = model.predict(samples)
            
            # Decode predictions if label encoder exists
            label_encoder = self.models[model_id].get('label_encoder')
            if label_encoder is not None:
                predictions = label_encoder.inverse_transform(predictions)
            
            predictions = predictions.tolist()
            
            # Get prediction probabilities if available
            probabilities = None
            if hasattr(model, 'predict_proba'):
                try:
                    probabilities = model.predict_proba(samples).tolist()
                except:
                    probabilities = None
            
            return {
                'model_id': model_id,
                'predictions': predictions,
                'probabilities': probabilities,
                'sample_count': len(samples)
            }
        except Exception as e:
            return {'error': f'Prediction error: {str(e)}'}
    
    def get_feature_importance(self, model_id):
        """Get feature importance for a model"""
        try:
            if model_id not in self.models:
                model_path = os.path.join(self.models_dir, f"{model_id}.pkl")
                if not os.path.exists(model_path):
                    return {'error': f'Model {model_id} not found'}
                
                with open(model_path, 'rb') as f:
                    model = pickle.load(f)
                
                self.models[model_id] = {
                    'model': model,
                    'path': model_path
                }
            
            model = self.models[model_id]['model']
            
            if hasattr(model, 'feature_importances_'):
                return {
                    'model_id': model_id,
                    'feature_importance': model.feature_importances_.tolist()
                }
            
            return {'error': 'Model does not support feature importance'}
        except Exception as e:
            return {'error': f'Error getting feature importance: {str(e)}'}

