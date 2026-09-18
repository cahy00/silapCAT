<?php

namespace App\Services;

use App\Models\Employee;
use Illuminate\Support\Collection;

/**
 * Genetic Algorithm for optimizing officer (Petugas) assignment across test locations (Tilok).
 *
 * Rules:
 * - Koordinator: exactly 1 per tilok
 * - IT: max(1, ceil(pc_capacity / 50)) per tilok
 * - Pengawas: max(room_count, ceil(pc_capacity / 25), 1) per tilok
 * - No officer can be assigned to two different tiloks on the same date
 * - When employees are insufficient, allocate based on lowest PC capacity / participant count first
 * - Results are recommendations, editable by user
 */
class GeneticSchedulerService
{
    // GA Parameters
    protected int $populationSize = 50;

    protected int $maxGenerations = 100;

    protected float $crossoverRate = 0.8;

    protected float $mutationRate = 0.1;

    protected int $tournamentSize = 3;

    // Data pools by role
    protected array $koordinators = [];

    protected array $itStaff = [];

    protected array $pengawas = [];

    // Tilok items with calculated date ranges
    protected array $items = [];

    // Date ranges per item index (for conflict detection)
    protected array $dateRanges = [];

    /**
     * Run the Genetic Algorithm to recommend officer assignments.
     *
     * @param  array  $items  Spreadsheet items with: location_id, pc_capacity, room_count, participants_count, start_date, sessions_per_day, has_opening_day
     * @param  Collection  $employees  All employees with status (role) field
     * @param  array  $scheduleCalculations  Pre-calculated schedule data per item (with days/dates)
     * @return array  Recommended assignments: [itemIndex => ['koordinator_ids' => [...], 'it_ids' => [...], 'pengawas_ids' => [...]]]
     */
    public function run(array $items, Collection $employees, array $scheduleCalculations = []): array
    {
        $this->items = array_values($items);
        $this->categorizeEmployees($employees);
        $this->buildDateRanges($scheduleCalculations);

        // Edge case: no items
        if (empty($this->items)) {
            return [];
        }

        // Calculate requirements per tilok
        $requirements = [];
        foreach ($this->items as $idx => $item) {
            $requirements[$idx] = $this->calculateRequirements($item);
        }

        // Sort tiloks by pc_capacity / participants ascending — allocate smallest tiloks first when resources are scarce
        $sortedIndices = array_keys($this->items);
        usort($sortedIndices, function ($a, $b) {
            $pcA = (int) ($this->items[$a]['pc_capacity'] ?? 0);
            $pcB = (int) ($this->items[$b]['pc_capacity'] ?? 0);
            if ($pcA === $pcB) {
                return ($this->items[$a]['participants_count'] ?? 0) <=> ($this->items[$b]['participants_count'] ?? 0);
            }
            return $pcA <=> $pcB;
        });

        // Initialize population
        $population = $this->initializePopulation($requirements, $sortedIndices);

        // Evolution loop
        $bestChromosome = null;
        $bestFitness = -PHP_INT_MAX;

        for ($gen = 0; $gen < $this->maxGenerations; $gen++) {
            // Evaluate fitness
            $fitnesses = [];
            foreach ($population as $i => $chromosome) {
                $fitnesses[$i] = $this->evaluateFitness($chromosome, $requirements);
                if ($fitnesses[$i] > $bestFitness) {
                    $bestFitness = $fitnesses[$i];
                    $bestChromosome = $chromosome;
                }
            }

            // Early exit if perfect fitness
            if ($bestFitness >= 100) {
                break;
            }

            // Create next generation
            $newPopulation = [];

            // Elitism: keep the best
            $newPopulation[] = $bestChromosome;

            while (count($newPopulation) < $this->populationSize) {
                $parent1 = $this->tournamentSelection($population, $fitnesses);
                $parent2 = $this->tournamentSelection($population, $fitnesses);

                if (mt_rand(1, 100) / 100 <= $this->crossoverRate) {
                    [$child1, $child2] = $this->crossover($parent1, $parent2);
                } else {
                    $child1 = $parent1;
                    $child2 = $parent2;
                }

                $newPopulation[] = $this->mutate($child1, $requirements);
                if (count($newPopulation) < $this->populationSize) {
                    $newPopulation[] = $this->mutate($child2, $requirements);
                }
            }

            $population = $newPopulation;
        }

        return $this->formatResult($bestChromosome, $requirements);
    }

    /**
     * Categorize employees by their role(s) from the status field.
     */
    protected function categorizeEmployees(Collection $employees): void
    {
        $this->koordinators = [];
        $this->itStaff = [];
        $this->pengawas = [];

        foreach ($employees as $emp) {
            $roles = is_array($emp->status) ? $emp->status : [];
            foreach ($roles as $role) {
                $normalized = strtolower(trim($role));
                if ($normalized === 'koordinator') {
                    $this->koordinators[] = $emp->id;
                }
                if ($normalized === 'it') {
                    $this->itStaff[] = $emp->id;
                }
                if ($normalized === 'pengawas') {
                    $this->pengawas[] = $emp->id;
                }
            }
        }
    }

    /**
     * Build date ranges for each item based on schedule calculations.
     * Used for conflict detection between tiloks.
     */
    protected function buildDateRanges(array $scheduleCalculations): void
    {
        $this->dateRanges = [];

        foreach ($this->items as $idx => $item) {
            $dates = [];

            if (isset($scheduleCalculations[$idx]['days'])) {
                foreach ($scheduleCalculations[$idx]['days'] as $day) {
                    $dates[] = $day['date'];
                }
            } else {
                // Estimate date range from item data
                $participantsCount = max((int) ($item['participants_count'] ?? 0), 1);
                $pcCapacity = max((int) ($item['pc_capacity'] ?? 1), 1);
                $sessionsPerDay = max((int) ($item['sessions_per_day'] ?? 4), 1);
                $dailyCapacity = $pcCapacity * $sessionsPerDay;
                $totalDays = max((int) ceil($participantsCount / $dailyCapacity), 1);

                $startDate = \Carbon\Carbon::parse($item['start_date'] ?? now()->format('Y-m-d'));
                if (! empty($item['has_opening_day'])) {
                    $startDate = $startDate->copy()->addDay();
                }

                for ($d = 0; $d < $totalDays; $d++) {
                    $dates[] = $startDate->copy()->addDays($d)->format('Y-m-d');
                }
            }

            $this->dateRanges[$idx] = $dates;
        }
    }

    /**
     * Calculate required number of officers per role for a tilok item.
     *
     * Rules:
     * - Koordinator: 1 per tilok
     * - Tim IT: 1 per 50 PC (min 1)
     * - Pengawas: max(room_count, ceil(pc_capacity / 25), 1)
     */
    public function calculateRequirements(array $item): array
    {
        $pcCapacity = max((int) ($item['pc_capacity'] ?? 0), 0);
        $roomCount = max((int) ($item['room_count'] ?? 0), 1);

        // IT: 1 per 50 PC ujian
        $itNeeded = $pcCapacity > 0 ? (int) ceil($pcCapacity / 50) : 1;
        if ($itNeeded < 1) {
            $itNeeded = 1;
        }

        // Pengawas: 1 per 25 PC ujian, atau minimal 1 per ruangan ujian
        $pengawasByPc = $pcCapacity > 0 ? (int) ceil($pcCapacity / 25) : 1;
        $pengawasNeeded = max($pengawasByPc, $roomCount, 1);

        return [
            'koordinator' => 1,
            'it' => $itNeeded,
            'pengawas' => $pengawasNeeded,
        ];
    }

    /**
     * Initialize random population, respecting role constraints.
     */
    protected function initializePopulation(array $requirements, array $sortedIndices): array
    {
        $population = [];

        for ($p = 0; $p < $this->populationSize; $p++) {
            $chromosome = [];
            $usedByDate = []; // date => [empId => true]

            // Process items sorted by participants ascending (smallest first gets priority)
            foreach ($sortedIndices as $idx) {
                $req = $requirements[$idx];
                $dates = $this->dateRanges[$idx] ?? [];

                // Pick koordinator
                $koordinatorId = $this->pickAvailable($this->koordinators, $dates, $usedByDate);
                if ($koordinatorId) {
                    $this->markUsed($koordinatorId, $dates, $usedByDate);
                }

                // Pick IT staff
                $itIds = [];
                $shuffledIt = $this->itStaff;
                shuffle($shuffledIt);
                foreach ($shuffledIt as $empId) {
                    if (count($itIds) >= $req['it']) {
                        break;
                    }
                    if ($this->isAvailable($empId, $dates, $usedByDate)) {
                        $itIds[] = $empId;
                        $this->markUsed($empId, $dates, $usedByDate);
                    }
                }

                // Pick Pengawas
                $pengawasIds = [];
                $shuffledPengawas = $this->pengawas;
                shuffle($shuffledPengawas);
                foreach ($shuffledPengawas as $empId) {
                    if (count($pengawasIds) >= $req['pengawas']) {
                        break;
                    }
                    if ($this->isAvailable($empId, $dates, $usedByDate)) {
                        $pengawasIds[] = $empId;
                        $this->markUsed($empId, $dates, $usedByDate);
                    }
                }

                $chromosome[$idx] = [
                    'koordinator_ids' => $koordinatorId ? [$koordinatorId] : [],
                    'it_ids' => $itIds,
                    'pengawas_ids' => $pengawasIds,
                ];
            }

            // Ensure chromosome is sorted by original index
            ksort($chromosome);
            $population[] = $chromosome;
        }

        return $population;
    }

    /**
     * Pick a random available employee from a pool that doesn't conflict on the given dates.
     */
    protected function pickAvailable(array $pool, array $dates, array &$usedByDate): ?int
    {
        $shuffled = $pool;
        shuffle($shuffled);

        foreach ($shuffled as $empId) {
            if ($this->isAvailable($empId, $dates, $usedByDate)) {
                return $empId;
            }
        }

        // If no available one, return a random one (will incur penalty in fitness)
        return ! empty($pool) ? $pool[array_rand($pool)] : null;
    }

    /**
     * Check if an employee is available on all given dates.
     */
    protected function isAvailable(int $empId, array $dates, array &$usedByDate): bool
    {
        foreach ($dates as $date) {
            if (isset($usedByDate[$date][$empId])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Mark an employee as used on certain dates.
     */
    protected function markUsed(int $empId, array $dates, array &$usedByDate): void
    {
        foreach ($dates as $date) {
            $usedByDate[$date][$empId] = true;
        }
    }

    /**
     * Evaluate fitness of a chromosome.
     *
     * Higher = better. Max fitness = 100.
     * Penalties:
     * - Schedule conflicts across tiloks: -50 per conflict
     * - Unmet role requirements: -30 per missing slot
     * - Duplicate assignments (same person in multiple tiloks on same date): -10 per occurrence
     */
    protected function evaluateFitness(array $chromosome, array $requirements): float
    {
        $fitness = 100.0;
        $conflictPenalty = 0;
        $unmetPenalty = 0;

        // 1. Check schedule conflicts: same employee assigned to different tiloks on same date
        $employeeDateMap = []; // empId => [date => tilokIdx]

        foreach ($chromosome as $idx => $assignment) {
            $dates = $this->dateRanges[$idx] ?? [];
            $allEmpIds = array_merge(
                $assignment['koordinator_ids'] ?? [],
                $assignment['it_ids'] ?? [],
                $assignment['pengawas_ids'] ?? []
            );

            foreach ($allEmpIds as $empId) {
                foreach ($dates as $date) {
                    if (isset($employeeDateMap[$empId][$date]) && $employeeDateMap[$empId][$date] !== $idx) {
                        $conflictPenalty++;
                    }
                    $employeeDateMap[$empId][$date] = $idx;
                }
            }
        }

        // 2. Check role requirements met
        foreach ($chromosome as $idx => $assignment) {
            $req = $requirements[$idx];

            $koordinatorCount = count($assignment['koordinator_ids'] ?? []);
            $itCount = count($assignment['it_ids'] ?? []);
            $pengawasCount = count($assignment['pengawas_ids'] ?? []);

            // Unmet requirements
            $unmetPenalty += max(0, $req['koordinator'] - $koordinatorCount);
            $unmetPenalty += max(0, $req['it'] - $itCount);
            $unmetPenalty += max(0, $req['pengawas'] - $pengawasCount);
        }

        $fitness -= ($conflictPenalty * 50);
        $fitness -= ($unmetPenalty * 30);

        return $fitness;
    }

    /**
     * Tournament selection: pick the best individual from a random subset.
     */
    protected function tournamentSelection(array $population, array $fitnesses): array
    {
        $best = null;
        $bestFitness = -PHP_INT_MAX;

        for ($i = 0; $i < $this->tournamentSize; $i++) {
            $idx = array_rand($population);
            if ($fitnesses[$idx] > $bestFitness) {
                $bestFitness = $fitnesses[$idx];
                $best = $population[$idx];
            }
        }

        return $best;
    }

    /**
     * Uniform crossover: for each tilok, randomly pick assignment from parent1 or parent2.
     */
    protected function crossover(array $parent1, array $parent2): array
    {
        $child1 = [];
        $child2 = [];

        foreach (array_keys($this->items) as $idx) {
            if (mt_rand(0, 1) === 0) {
                $child1[$idx] = $parent1[$idx] ?? [];
                $child2[$idx] = $parent2[$idx] ?? [];
            } else {
                $child1[$idx] = $parent2[$idx] ?? [];
                $child2[$idx] = $parent1[$idx] ?? [];
            }
        }

        return [$child1, $child2];
    }

    /**
     * Mutation: randomly swap an officer in a random tilok with another valid officer.
     */
    protected function mutate(array $chromosome, array $requirements): array
    {
        foreach (array_keys($this->items) as $idx) {
            if (mt_rand(1, 100) / 100 > $this->mutationRate) {
                continue;
            }

            // Pick a random role to mutate
            $roles = ['koordinator', 'it', 'pengawas'];
            $role = $roles[array_rand($roles)];

            $pool = match ($role) {
                'koordinator' => $this->koordinators,
                'it' => $this->itStaff,
                'pengawas' => $this->pengawas,
            };

            if (empty($pool)) {
                continue;
            }

            $key = $role === 'koordinator' ? 'koordinator_ids' : ($role === 'it' ? 'it_ids' : 'pengawas_ids');
            $currentIds = $chromosome[$idx][$key] ?? [];

            if (! empty($currentIds)) {
                // Swap one existing officer with a random one from the pool
                $swapIdx = array_rand($currentIds);
                $newEmpId = $pool[array_rand($pool)];
                $currentIds[$swapIdx] = $newEmpId;
                $chromosome[$idx][$key] = array_values(array_unique($currentIds));
            } else {
                // Add a random officer
                $chromosome[$idx][$key] = [$pool[array_rand($pool)]];
            }
        }

        return $chromosome;
    }

    /**
     * Format the best chromosome result into a structured array.
     * Also produces warnings for insufficient staff.
     */
    protected function formatResult(array $chromosome, array $requirements): array
    {
        $result = [];
        $warnings = [];

        foreach ($chromosome as $idx => $assignment) {
            $req = $requirements[$idx];

            $koordinatorIds = array_values(array_unique($assignment['koordinator_ids'] ?? []));
            $itIds = array_values(array_unique($assignment['it_ids'] ?? []));
            $pengawasIds = array_values(array_unique($assignment['pengawas_ids'] ?? []));

            // Trim to exact requirement count
            $koordinatorIds = array_slice($koordinatorIds, 0, $req['koordinator']);
            $itIds = array_slice($itIds, 0, $req['it']);
            $pengawasIds = array_slice($pengawasIds, 0, $req['pengawas']);

            // Track warnings
            $itemWarnings = [];
            if (count($koordinatorIds) < $req['koordinator']) {
                $itemWarnings[] = 'Kekurangan ' . ($req['koordinator'] - count($koordinatorIds)) . ' Koordinator';
            }
            if (count($itIds) < $req['it']) {
                $itemWarnings[] = 'Kekurangan ' . ($req['it'] - count($itIds)) . ' Tim IT';
            }
            if (count($pengawasIds) < $req['pengawas']) {
                $itemWarnings[] = 'Kekurangan ' . ($req['pengawas'] - count($pengawasIds)) . ' Pengawas';
            }

            $result[$idx] = [
                'koordinator_ids' => $koordinatorIds,
                'it_ids' => $itIds,
                'pengawas_ids' => $pengawasIds,
                'requirements' => $req,
                'warnings' => $itemWarnings,
                'is_ga_recommendation' => true,
            ];

            if (! empty($itemWarnings)) {
                $warnings[$idx] = $itemWarnings;
            }
        }

        return [
            'assignments' => $result,
            'warnings' => $warnings,
            'has_conflicts' => $this->hasConflicts($chromosome),
        ];
    }

    /**
     * Check if the final solution has any schedule conflicts.
     */
    protected function hasConflicts(array $chromosome): bool
    {
        $employeeDateMap = [];

        foreach ($chromosome as $idx => $assignment) {
            $dates = $this->dateRanges[$idx] ?? [];
            $allEmpIds = array_merge(
                $assignment['koordinator_ids'] ?? [],
                $assignment['it_ids'] ?? [],
                $assignment['pengawas_ids'] ?? []
            );

            foreach ($allEmpIds as $empId) {
                foreach ($dates as $date) {
                    if (isset($employeeDateMap[$empId][$date]) && $employeeDateMap[$empId][$date] !== $idx) {
                        return true;
                    }
                    $employeeDateMap[$empId][$date] = $idx;
                }
            }
        }

        return false;
    }

    /**
     * Get employee pools for external use (e.g., displaying filtered selects).
     */
    public function getEmployeePools(Collection $employees): array
    {
        $this->categorizeEmployees($employees);

        return [
            'koordinator_ids' => $this->koordinators,
            'it_ids' => $this->itStaff,
            'pengawas_ids' => $this->pengawas,
        ];
    }
}
