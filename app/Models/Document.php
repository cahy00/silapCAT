<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;
		protected $fillable = [
			'user_id',
			'type_id',
			'name',
			'date',
			'number',
			'file',
			'jenis_surat',
			'tgl_distribusi',
			'asal',
			'disposisi',
			'unit',
			'sifat'
			// 'exception'
		];

		public function user()
		{
			return $this->belongsTo(User::class);
		}

		public function type()
		{
			return $this->belongsTo(Type::class);
		}

		public function action()
		{
			return $this->hasMany(Action::class);
		}

		public function setTanggalSuratAttribute($value)
		{
			$this->attributes['tgl_distribusi'] = Carbon::parse($value);
		}
}
