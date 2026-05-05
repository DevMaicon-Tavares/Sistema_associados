<?php

namespace Tests\Unit;

use App\Models\Associado;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssociadoTest extends TestCase
{
    use RefreshDatabase;

    public function test_associado_has_fillable_attributes()
    {
        $associado = new Associado();
        $this->assertEquals(['nome', 'cpf', 'telefone', 'status_pagamento'], $associado->getFillable());
    }

    public function test_associado_can_be_created()
    {
        $data = [
            'nome' => 'João Silva',
            'cpf' => '12345678901',
            'telefone' => '11987654321',
            'status_pagamento' => 'em dia',
        ];

        $associado = Associado::create($data);

        $this->assertInstanceOf(Associado::class, $associado);
        $this->assertEquals('João Silva', $associado->nome);
        $this->assertEquals('12345678901', $associado->cpf);
        $this->assertEquals('11987654321', $associado->telefone);
        $this->assertEquals('em dia', $associado->status_pagamento);
    }

    public function test_associado_cpf_is_unique()
    {
        Associado::create([
            'nome' => 'João Silva',
            'cpf' => '12345678901',
            'telefone' => '11987654321',
            'status_pagamento' => 'em dia',
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);
        Associado::create([
            'nome' => 'Maria Silva',
            'cpf' => '12345678901',
            'telefone' => '11987654322',
            'status_pagamento' => 'atrasado',
        ]);
    }
}
