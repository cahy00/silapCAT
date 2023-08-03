<?php

namespace App\Charts;

use App\Models\Document;
use Illuminate\Support\Facades\DB;
use ArielMejiaDev\LarapexCharts\LarapexChart;

class SuratMasukChart
{
    protected $chart;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

    public function build(): \ArielMejiaDev\LarapexCharts\PieChart
    {
				$suratMasuk = Document::get();
				$data = [
					$suratMasuk->where('jenis_surat', 'surat_masuk')->count()
				];

				$bulan = Document::select(DB::raw("MONTHNAME(created_at) as bulan"))
				->GroupBy(DB::raw("MONTHNAME(created_at)"))
				->pluck('bulan');
				
        return $this->chart->pieChart()
            ->setTitle('Top 3 scorers of the team.')
            ->setSubtitle('Season 2021.')
            ->addData($data)
            ->setLabels($bulan);
    }
}
