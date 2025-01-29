<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    use HasFactory;
		protected $fillable = [
			'document_id',
			'followup',
			'description'
		];

		public function document()
		{
			return $this->belongsTo(Document::class);
		}
}
