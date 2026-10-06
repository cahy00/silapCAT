<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Location;
use Carbon\Carbon;

class LocationMapWidget extends Widget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 4;

    protected string $view = 'filament.widgets.location-map';

    public function getMapLocationsProperty()
    {
        $today = Carbon::today();

        $locations = Location::with([
            'locationSurvey',
            'eventLocations.event' => function ($q) {
                $q->with('procurementType');
            },
            'eventLocations.eventLocationInstitutions.institution',
        ])->get();

        $cityCoordinates = [
            'manokwari' => [-0.861453, 134.062042],
            'sorong' => [-0.876176, 131.255806],
            'fakfak' => [-2.926888, 132.296561],
            'fak-fak' => [-2.926888, 132.296561],
            'kaimana' => [-3.662283, 133.771973],
            'bintuni' => [-2.127814, 133.522644],
            'teluk bintuni' => [-2.127814, 133.522644],
            'wondama' => [-2.700000, 134.500000],
            'teluk wondama' => [-2.700000, 134.500000],
            'raja ampat' => [-0.463287, 130.824280],
            'waisai' => [-0.463287, 130.824280],
            'sorong selatan' => [-1.4988, 132.0163],
            'teminabuan' => [-1.4988, 132.0163],
            'maybrat' => [-1.3069, 132.4285],
            'tambrauw' => [-0.7600, 132.4000],
            'pegunungan arfak' => [-1.3800, 133.9100],
            'manokwari selatan' => [-1.5000, 134.1700],
            'ransiki' => [-1.5000, 134.1700],
            'jayapura' => [-2.53371, 140.71813],
            'merauke' => [-8.49911, 140.40138],
            'timika' => [-4.5467, 136.8837],
            'mimika' => [-4.5467, 136.8837],
            'biak' => [-1.1787, 136.0847],
            'biak numfor' => [-1.1787, 136.0847],
            'nabire' => [-3.3664, 135.4967],
            'serui' => [-1.8833, 136.2333],
            'yapen' => [-1.8833, 136.2333],
            'wamena' => [-4.0975, 138.9442],
            'jayawijaya' => [-4.0975, 138.9442],
            'jakarta' => [-6.2088, 106.8456],
            'jakarta pusat' => [-6.1805, 106.8284],
            'makassar' => [-5.1476, 119.4327],
            'surabaya' => [-7.2575, 112.7521],
        ];

        $markers = [];

        foreach ($locations as $index => $loc) {
            $lat = $loc->latitude;
            $lng = $loc->longitude;

            // Coordinate lookup by city if not explicitly provided
            if (empty($lat) || empty($lng)) {
                $cityClean = strtolower(trim($loc->city ?? ''));
                $nameClean = strtolower(trim($loc->name ?? ''));

                $foundCoord = null;
                foreach ($cityCoordinates as $key => $coord) {
                    if (str_contains($cityClean, $key) || str_contains($nameClean, $key)) {
                        $foundCoord = $coord;
                        break;
                    }
                }

                if ($foundCoord) {
                    // Add slight deterministic jitter so multiple tiloks in the same city don't completely overlap
                    $jitterLat = (($index % 5) - 2) * 0.008;
                    $jitterLng = ((($index * 2) % 5) - 2) * 0.008;
                    $lat = $foundCoord[0] + $jitterLat;
                    $lng = $foundCoord[1] + $jitterLng;
                } else {
                    // Default to Manokwari region with slight offset
                    $lat = -0.861453 + ($index * 0.005);
                    $lng = 134.062042 + ($index * 0.005);
                }
            }

            // Check if there is an active event in this location today
            $hasActiveEvent = false;
            $activeEventNames = [];

            foreach ($loc->eventLocations as $el) {
                $event = $el->event;
                if (! $event) continue;

                $isOngoing = false;
                if ($el->start_date && $el->end_date && $today->between($el->start_date, $el->end_date)) {
                    $isOngoing = true;
                } elseif ($event->status === 'active') {
                    $isOngoing = true;
                }

                if ($isOngoing) {
                    $hasActiveEvent = true;
                    $activeEventNames[] = $event->name;
                }
            }

            $markers[] = [
                'id' => $loc->id,
                'name' => $loc->name,
                'type' => strtoupper(str_replace('_', ' ', $loc->type ?? 'BKN')),
                'city' => $loc->city ?? 'Papua Barat',
                'address' => $loc->address ?? 'Alamat belum diatur',
                'lat' => (float) $lat,
                'lng' => (float) $lng,
                'pc_count' => $loc->locationSurvey?->pc_count ?? 0,
                'room_count' => $loc->locationSurvey?->room_count ?? 0,
                'feasibility' => $loc->locationSurvey?->feasibility_status ?? 'feasible',
                'has_active_event' => $hasActiveEvent,
                'active_events' => $activeEventNames,
                'total_events_count' => $loc->eventLocations->count(),
            ];
        }

        return $markers;
    }
}
