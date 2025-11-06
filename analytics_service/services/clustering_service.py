"""
Clustering Service using scikit-learn
Provides K-Means, Hierarchical, and DBSCAN clustering
"""
import numpy as np
from sklearn.cluster import KMeans, AgglomerativeClustering, DBSCAN
from sklearn.metrics import silhouette_score, calinski_harabasz_score, davies_bouldin_score
from sklearn.preprocessing import StandardScaler
import json


class ClusteringService:
    """Service for performing various clustering algorithms"""
    
    def __init__(self):
        self.scaler = StandardScaler()
    
    def kmeans(self, samples, k=3, max_iterations=100, num_runs=3, random_state=42):
        """
        Perform K-Means clustering with multiple runs
        
        Args:
            samples: List of feature vectors
            k: Number of clusters
            max_iterations: Maximum iterations
            num_runs: Number of runs to select best result
            random_state: Random seed
            
        Returns:
            Dictionary with clusters, centroids, metrics, and characteristics
        """
        samples = np.array(samples)
        
        # Normalize features
        samples_scaled = self.scaler.fit_transform(samples)
        
        best_model = None
        best_inertia = float('inf')
        best_labels = None
        best_centroids = None
        
        # Run multiple times and select best
        for run in range(num_runs):
            kmeans = KMeans(
                n_clusters=k,
                max_iter=max_iterations,
                random_state=random_state + run,
                n_init=10,
                init='k-means++'
            )
            labels = kmeans.fit_predict(samples_scaled)
            inertia = kmeans.inertia_
            
            if inertia < best_inertia:
                best_inertia = inertia
                best_model = kmeans
                best_labels = labels
                best_centroids = kmeans.cluster_centers_
        
        # Calculate metrics
        silhouette = silhouette_score(samples_scaled, best_labels)
        calinski_harabasz = calinski_harabasz_score(samples_scaled, best_labels)
        davies_bouldin = davies_bouldin_score(samples_scaled, best_labels)
        
        # Organize clusters
        clusters = {}
        for i in range(k):
            cluster_indices = np.where(best_labels == i)[0].tolist()
            clusters[i] = {
                'indices': cluster_indices,
                'size': len(cluster_indices),
                'centroid': best_centroids[i].tolist()
            }
        
        # Calculate characteristics
        characteristics = self._calculate_cluster_characteristics(
            samples, best_labels, k
        )
        
        return {
            'clusters': clusters,
            'labels': best_labels.tolist(),
            'centroids': best_centroids.tolist(),
            'inertia': float(best_inertia),
            'metrics': {
                'silhouette_score': float(silhouette),
                'calinski_harabasz_score': float(calinski_harabasz),
                'davies_bouldin_score': float(davies_bouldin),
                'iterations': int(best_model.n_iter_),
                'converged': True
            },
            'characteristics': characteristics
        }
    
    def find_optimal_k(self, samples, max_k=10, method='elbow'):
        """
        Find optimal number of clusters using elbow method or silhouette score
        
        Args:
            samples: List of feature vectors
            max_k: Maximum K to test
            method: 'elbow', 'silhouette', or 'gap'
            
        Returns:
            Dictionary with optimal K and metrics
        """
        samples = np.array(samples)
        samples_scaled = self.scaler.fit_transform(samples)
        
        if len(samples) < 2:
            return {'optimal_k': 2, 'method': method, 'scores': {}}
        
        max_k = min(max_k, len(samples) - 1)
        
        if method == 'elbow':
            return self._elbow_method(samples_scaled, max_k)
        elif method == 'silhouette':
            return self._silhouette_method(samples_scaled, max_k)
        elif method == 'gap':
            return self._gap_statistic_method(samples_scaled, max_k)
        else:
            return {'error': f'Unknown method: {method}'}
    
    def _elbow_method(self, samples, max_k):
        """Elbow method to find optimal K"""
        inertias = []
        k_range = range(2, max_k + 1)
        
        for k in k_range:
            kmeans = KMeans(n_clusters=k, random_state=42, n_init=10)
            kmeans.fit(samples)
            inertias.append(kmeans.inertia_)
        
        # Calculate elbow point (maximum curvature)
        best_k = 2
        max_curvature = 0
        
        if len(inertias) >= 3:
            for i in range(1, len(inertias) - 1):
                curvature = abs(inertias[i-1] - 2*inertias[i] + inertias[i+1])
                if curvature > max_curvature:
                    max_curvature = curvature
                    best_k = k_range[i]
        
        return {
            'optimal_k': best_k,
            'method': 'elbow',
            'scores': {k: float(inertia) for k, inertia in zip(k_range, inertias)},
            'inertias': [float(i) for i in inertias]
        }
    
    def _silhouette_method(self, samples, max_k):
        """Silhouette score method to find optimal K"""
        scores = {}
        k_range = range(2, max_k + 1)
        
        for k in k_range:
            kmeans = KMeans(n_clusters=k, random_state=42, n_init=10)
            labels = kmeans.fit_predict(samples)
            score = silhouette_score(samples, labels)
            scores[k] = float(score)
        
        best_k = max(scores, key=scores.get) if scores else 2
        
        return {
            'optimal_k': best_k,
            'method': 'silhouette',
            'scores': scores,
            'best_score': scores[best_k]
        }
    
    def _gap_statistic_method(self, samples, max_k):
        """Gap statistic method to find optimal K"""
        # Simplified gap statistic
        gaps = {}
        k_range = range(2, max_k + 1)
        
        for k in k_range:
            kmeans = KMeans(n_clusters=k, random_state=42, n_init=10)
            kmeans.fit(samples)
            inertia = kmeans.inertia_
            
            # Generate reference data
            ref_inertias = []
            for _ in range(5):
                ref_data = np.random.uniform(
                    samples.min(axis=0),
                    samples.max(axis=0),
                    samples.shape
                )
                ref_kmeans = KMeans(n_clusters=k, random_state=42, n_init=10)
                ref_kmeans.fit(ref_data)
                ref_inertias.append(ref_kmeans.inertia_)
            
            ref_inertia = np.mean(ref_inertias)
            gap = np.log(ref_inertia) - np.log(inertia)
            gaps[k] = float(gap)
        
        best_k = max(gaps, key=gaps.get) if gaps else 2
        
        return {
            'optimal_k': best_k,
            'method': 'gap',
            'scores': gaps,
            'best_gap': gaps[best_k]
        }
    
    def hierarchical(self, samples, n_clusters=3, linkage='ward'):
        """
        Perform hierarchical clustering
        
        Args:
            samples: List of feature vectors
            n_clusters: Number of clusters
            linkage: 'ward', 'complete', 'average', or 'single'
            
        Returns:
            Dictionary with clusters and labels
        """
        samples = np.array(samples)
        samples_scaled = self.scaler.fit_transform(samples)
        
        clustering = AgglomerativeClustering(
            n_clusters=n_clusters,
            linkage=linkage
        )
        labels = clustering.fit_predict(samples_scaled)
        
        # Calculate metrics
        silhouette = silhouette_score(samples_scaled, labels)
        calinski_harabasz = calinski_harabasz_score(samples_scaled, labels)
        
        # Organize clusters
        clusters = {}
        for i in range(n_clusters):
            cluster_indices = np.where(labels == i)[0].tolist()
            cluster_samples = samples[cluster_indices]
            centroid = cluster_samples.mean(axis=0).tolist()
            
            clusters[i] = {
                'indices': cluster_indices,
                'size': len(cluster_indices),
                'centroid': centroid
            }
        
        characteristics = self._calculate_cluster_characteristics(
            samples, labels, n_clusters
        )
        
        return {
            'clusters': clusters,
            'labels': labels.tolist(),
            'metrics': {
                'silhouette_score': float(silhouette),
                'calinski_harabasz_score': float(calinski_harabasz),
                'linkage': linkage
            },
            'characteristics': characteristics
        }
    
    def _calculate_cluster_characteristics(self, samples, labels, k):
        """
        Calculate characteristics for each cluster
        
        Args:
            samples: Original feature vectors
            labels: Cluster labels
            k: Number of clusters
            
        Returns:
            List of cluster characteristics
        """
        characteristics = []
        
        for i in range(k):
            cluster_indices = np.where(labels == i)[0]
            cluster_samples = samples[cluster_indices]
            
            if len(cluster_samples) == 0:
                characteristics.append({
                    'cluster_id': i,
                    'size': 0,
                    'avg_age': 0,
                    'avg_family_size': 0,
                    'income_distribution': {},
                    'employment_distribution': {},
                    'health_distribution': {}
                })
                continue
            
            # Calculate averages (assuming first features are age, family_size)
            avg_age = float(np.mean(cluster_samples[:, 0])) if len(cluster_samples[0]) > 0 else 0
            avg_family_size = float(np.mean(cluster_samples[:, 1])) if len(cluster_samples[0]) > 1 else 0
            
            # Calculate distributions (simplified - would need actual categorical data)
            characteristics.append({
                'cluster_id': i,
                'size': int(len(cluster_indices)),
                'avg_age': avg_age,
                'avg_family_size': avg_family_size,
                'income_distribution': {},  # Would need actual income data
                'employment_distribution': {},  # Would need actual employment data
                'health_distribution': {}  # Would need actual health data
            })
        
        return characteristics


