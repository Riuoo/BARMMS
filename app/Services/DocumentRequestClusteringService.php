<?php

namespace App\Services;

use App\Models\DocumentRequest;

class DocumentRequestClusteringService extends BaseClusteringService
{
    public function countByPurok($items = null, $addressField = 'address'): array
    {
        if ($items === null) {
            $items = DocumentRequest::with('resident')->get();
        }
        return parent::countByPurok($items, $addressField);
    }

    public function clusterRequests(int $k = 3): array
    {
        $requests = DocumentRequest::with('resident')->get();
        
        $features = [
            'type' => fn($item) => $item->document_type,
            'status' => fn($item) => $item->status,
            'additional' => fn($item) => [
                strlen($item->description ?? ''),
            ]
        ];

        return $this->clusterItems($requests, $features, $k);
    }
}
