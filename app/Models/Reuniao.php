<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reuniao extends Model
{
    use HasFactory;

    protected $table = 'reunioes';

    protected $fillable = [
        'titulo',
        'descricao',
        'data',
        'horario',
        'ata_path',
    ];

    protected $casts = [
        'data' => 'date',
        'horario' => 'string',
    ];

    public function isFuture(): bool
    {
        return $this->data->isFuture() || $this->data->isToday();
    }
}
