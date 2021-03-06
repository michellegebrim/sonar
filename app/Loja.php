<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Loja extends Model
{
    protected $fillable = [
        'pais_id',
        'descricao',
        'url'
    ];

    protected $table = 'lojas';

    public function pais()
    {
        return $this->beLongsTo('App\Pais');
    }
}
