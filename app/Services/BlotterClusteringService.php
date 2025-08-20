<?php

namespace App\Services;

use App\Models\BlotterRequest;
use Phpml\Clustering\KMeans;

class BlotterClusteringService
{
    public function countByPurok(): array
    {
        $blotters = BlotterRequest::with('resident')->get();
        $counts = [];
        foreach ($blotters as $b) {
            $purok = $this->extractPurok(optional($b->resident)->address ?? '');
            $counts[$purok] = ($counts[$purok] ?? 0) + 1;
        }
        arsort($counts);
        return $counts;
    }

    public function clusterBlotters(int $k = 3): array
    {
        $blotters = BlotterRequest::with('resident')->get();
        $purokMap = [];
        $purokIndex = 0;
        $typeMap = [];
        $typeIndex = 0;
        $statusMap = [];
        $statusIndex = 0;
        $samples = [];
        $blotterIds = [];

        foreach ($blotters as $b) {
            $purok = $this->extractPurok(optional($b->resident)->address ?? '');
            if (!isset($purokMap[$purok])) $purokMap[$purok] = $purokIndex++;
            if (!isset($typeMap[$b->type])) $typeMap[$b->type] = $typeIndex++;
            if (!isset($statusMap[$b->status])) $statusMap[$b->status] = $statusIndex++;

            $samples[] = [
                $purokMap[$purok],
                $typeMap[$b->type],
                $statusMap[$b->status],
                strlen($b->description ?? ''),
                count($b->media ?? []),
            ];
            $blotterIds[] = $b->id;
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
                $result[$clusterId][] = $blotterIds[$i++];
            }
        }

        return [
            'clusters' => $result,
            'purokMap' => array_flip($purokMap),
            'typeMap' => array_flip($typeMap),
            'statusMap' => array_flip($statusMap),
        ];
    }

    private function extractPurok($address): string
    {
        if (preg_match('/Purok\\s*\\d+/i', $address, $matches)) {
            return $matches[0];
        }
        return 'Unknown';
    }
}
