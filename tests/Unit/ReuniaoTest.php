<?php

namespace Tests\Unit;

use App\Models\Reuniao;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReuniaoTest extends TestCase
{
    use RefreshDatabase;

    public function test_reuniao_has_fillable_attributes()
    {
        $reuniao = new Reuniao();
        $this->assertEquals(['titulo', 'descricao', 'data', 'horario'], $reuniao->getFillable());
    }

    public function test_reuniao_has_correct_table_name()
    {
        $reuniao = new Reuniao();
        $this->assertEquals('reunioes', $reuniao->getTable());
    }

    public function test_reuniao_casts()
    {
        $reuniao = new Reuniao();
        $casts = $reuniao->getCasts();
        $this->assertEquals('date', $casts['data']);
        $this->assertEquals('string', $casts['horario']);
    }

    public function test_reuniao_can_be_created()
    {
        $data = [
            'titulo' => 'Reunião Mensal',
            'descricao' => 'Discussão sobre projetos',
            'data' => '2026-05-01',
            'horario' => '14:00',
        ];

        $reuniao = Reuniao::create($data);

        $this->assertInstanceOf(Reuniao::class, $reuniao);
        $this->assertEquals('Reunião Mensal', $reuniao->titulo);
        $this->assertEquals('Discussão sobre projetos', $reuniao->descricao);
        $this->assertInstanceOf(Carbon::class, $reuniao->data);
        $this->assertEquals('14:00', $reuniao->horario);
    }

    public function test_is_future_returns_true_for_future_date()
    {
        $reuniao = Reuniao::create([
            'titulo' => 'Reunião Futura',
            'data' => Carbon::tomorrow(),
            'horario' => '10:00',
        ]);

        $this->assertTrue($reuniao->isFuture());
    }

    public function test_is_future_returns_true_for_today()
    {
        $reuniao = Reuniao::create([
            'titulo' => 'Reunião Hoje',
            'data' => Carbon::today(),
            'horario' => '10:00',
        ]);

        $this->assertTrue($reuniao->isFuture());
    }

    public function test_is_future_returns_false_for_past_date()
    {
        $reuniao = Reuniao::create([
            'titulo' => 'Reunião Passada',
            'data' => Carbon::yesterday(),
            'horario' => '10:00',
        ]);

        $this->assertFalse($reuniao->isFuture());
    }
}
