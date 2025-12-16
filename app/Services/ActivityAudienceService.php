<?php

namespace App\Services;

use App\Models\Residents;

class ActivityAudienceService
{
    /**
     * Get residents for a given audience scope and Purok.
     *
     * @param string $scope 'all' or 'purok'
     * @param string|null $purok
     * @return \Illuminate\Support\Collection<\App\Models\Residents>
     */
    public function getAudienceResidents(string $scope, ?string $purok = null)
    {
        $query = Residents::query()
            ->where('active', true)
            ->whereNotNull('email')
            ->where('email', '!=', '');

        if ($scope === 'purok' && $purok) {
            $query->where('address', 'like', '%' . $purok . '%');
        }

        return $query->get();
    }
}


