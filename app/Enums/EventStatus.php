<?php

namespace App\Enums;

use Carbon\Carbon;

/**
 * Status kanonik event (nilai yang disimpan di database).
 */
enum EventStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function getLabel(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Active => 'Aktif',
            self::Completed => 'Selesai',
            self::Cancelled => 'Dibatalkan',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Active => 'success',
            self::Completed => 'info',
            self::Cancelled => 'danger',
        };
    }

    /** @return array<string,string> */
    public static function options(): array
    {
        $out = [];
        foreach (self::cases() as $case) {
            $out[$case->value] = $case->getLabel();
        }

        return $out;
    }

    /**
     * Normalisasi nilai lama (aktif/selesai) ke nilai kanonik.
     */
    public static function normalize(?string $value): ?string
    {
        return match ($value) {
            'aktif' => self::Active->value,
            'selesai' => self::Completed->value,
            'dibatalkan' => self::Cancelled->value,
            default => $value,
        };
    }

    /**
     * Hitung status efektif berdasarkan tanggal. Status "cancelled" tidak pernah ditimpa.
     */
    public static function resolve(?string $stored, $startDate, $endDate): ?string
    {
        $stored = self::normalize($stored);

        if ($stored === self::Cancelled->value || ! $startDate || ! $endDate) {
            return $stored;
        }

        $now = now()->startOfDay();
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->startOfDay();

        if ($now->gt($end)) {
            return self::Completed->value;
        }

        if ($now->between($start, $end)) {
            return self::Active->value;
        }

        return $stored ?: self::Draft->value;
    }
}
