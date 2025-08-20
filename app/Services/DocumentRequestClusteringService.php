<?php

namespace App\Services;

use App\Models\DocumentRequest;
use Phpml\Clustering\KMeans;

class DocumentRequestClusteringService
{
    public function countByPurok(): array
    {
        $requests = DocumentRequest::with('resident')->get();
        $counts = [];
        foreach ($requests as $r) {
            $purok = $this->extractPurok(optional($r->resident)->address ?? '');
            $counts[$purok] = ($counts[$purok] ?? 0) + 1;
        }
        arsort($counts);
        return $counts;
    }

    public function clusterRequests(int $k = 3): array
    {
        $requests = DocumentRequest::with('resident')->get();
        $purokMap = [];
        $purokIndex = 0;
        $typeMap = [];
        $typeIndex = 0;
        $statusMap = [];
        $statusIndex = 0;
        $samples = [];
        $requestIds = [];

        foreach ($requests as $r) {
            $purok = $this->extractPurok(optional($r->resident)->address ?? '');
            if (!isset($purokMap[$purok])) $purokMap[$purok] = $purokIndex++;
            if (!isset($typeMap[$r->document_type])) $typeMap[$r->document_type] = $typeIndex++;
            if (!isset($statusMap[$r->status])) $statusMap[$r->status] = $statusIndex++;

            $samples[] = [
                $purokMap[$purok],
                $typeMap[$r->document_type],
                $statusMap[$r->status],
                strlen($r->description ?? ''),
            ];
            $requestIds[] = $r->id;
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
                $result[$clusterId][] = $requestIds[$i++];
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
