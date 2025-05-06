<?php

namespace App\Imports;

use App\Models\Report;
use Maatwebsite\Excel\Concerns\ToModel;

class ReportImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // \Log::info('Import row:', $row);
        if (!isset($row['event_id']) || empty($row['event_id'])) {
            return null; // skip baris kosong
        }
        
        return new Report([
            'event_id' => $row['event_id'],
            'tilok_id' => $row['tilok_id'],
            'instansi_name' => $row['instansi_name'],
            'exam_date' => $row['exam_date'], // pastikan format sesuai
            'session' => $row['session'],
            'participant_total' => $row['participant_total'],
            'participant_present' => $row['participant_present'],
            'participant_absent' => $row['participant_absent'],
            'highest_score' => $row['highest_score'],
            'lowest_score' => $row['lowest_score'],
            'average_score' => $row['average_score'],
        ]);

        // dd($row);
    }
}
