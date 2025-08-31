<?php

namespace App\Services;

use App\Models\BlotterRequest;

class BlotterClusteringService extends BaseClusteringService
{
    public function countByPurok($items = null, $addressField = 'address'): array
    {
        if ($items === null) {
            $items = BlotterRequest::with('resident')->get();
        }
        return parent::countByPurok($items, $addressField);
    }

    public function clusterBlotters(int $k = 3): array
    {
        $blotters = BlotterRequest::with('resident')->get();
        
        $features = [
            'type' => fn($item) => $item->type,
            'status' => fn($item) => $item->status,
            'additional' => fn($item) => [
                strlen($item->description ?? ''),
                count($item->media ?? []),
            ]
        ];

        return $this->clusterItems($blotters, $features, $k);
    }
}
