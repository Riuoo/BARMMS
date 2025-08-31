<?php

namespace App\Services;

use Phpml\Clustering\KMeans;

abstract class BaseClusteringService
{
    /**
     * Count items by purok from address
     */
    public function countByPurok($items, $addressField = 'address'): array
    {
        $counts = [];
        foreach ($items as $item) {
            $purok = $this->extractPurok(optional($item->resident)->{$addressField} ?? '');
            $counts[$purok] = ($counts[$purok] ?? 0) + 1;
        }
        arsort($counts);
        return $counts;
    }

    /**
     * Generic clustering method
     */
    protected function clusterItems($items, $features, $k = 3): array
    {
        $purokMap = [];
        $purokIndex = 0;
        $typeMap = [];
        $typeIndex = 0;
        $statusMap = [];
        $statusIndex = 0;
        $samples = [];
        $itemIds = [];

        foreach ($items as $item) {
            $purok = $this->extractPurok(optional($item->resident)->address ?? '');
            if (!isset($purokMap[$purok])) $purokMap[$purok] = $purokIndex++;
            if (!isset($typeMap[$features['type']($item)])) $typeMap[$features['type']($item)] = $typeIndex++;
            if (!isset($statusMap[$features['status']($item)])) $statusMap[$features['status']($item)] = $statusIndex++;

            $sample = [
                $purokMap[$purok],
                $typeMap[$features['type']($item)],
                $statusMap[$features['status']($item)],
            ];

            // Add additional features if provided
            if (isset($features['additional'])) {
                $sample = array_merge($sample, $features['additional']($item));
            }

            $samples[] = $sample;
            $itemIds[] = $item->id;
        }

        if (count($samples) < $k) {
            return [
                'clusters' => [],
                'error' => 'Not enough data points for clustering',
            ];
        }

        $kmeans = new KMeans($k);
        $clusters = $kmeans->cluster($samples);

        $result = [];
        $i = 0;
        foreach ($clusters as $clusterId => $clusterSamples) {
            $result[$clusterId] = [];
            foreach ($clusterSamples as $sample) {
                $result[$clusterId][] = $itemIds[$i++];
            }
        }

        return [
            'clusters' => $result,
            'purokMap' => array_flip($purokMap),
            'typeMap' => array_flip($typeMap),
            'statusMap' => array_flip($statusMap),
        ];
    }

    /**
     * Extract purok from address string
     */
    protected function extractPurok($address): string
    {
        if (preg_match('/Purok\\s*\\d+/i', $address, $matches)) {
            return $matches[0];
        }
        return 'Unknown';
    }
}
