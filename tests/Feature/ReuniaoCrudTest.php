<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Reuniao;
use App\Models\User;

class ReuniaoCrudTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_reuniao_edit_form()
    {
        $reuniao = Reuniao::factory()->create();

        $response = $this->actingAs($this->user)->get(route('reunioes.edit', $reuniao));

        $response->assertStatus(200);
        $response->assertViewIs('reunioes.edit');
        $response->assertViewHas('reuniao');
    }

    public function test_reuniao_update()
    {
        $reuniao = Reuniao::factory()->create([
            'titulo' => 'Reunião Original',
            'descricao' => 'Descrição original',
        ]);

        $updateData = [
            'titulo' => 'Reunião Atualizada',
            'descricao' => 'Descrição atualizada',
            'data' => '2026-05-10',
            'horario' => '15:00',
        ];

        $response = $this->actingAs($this->user)->put(route('reunioes.update', $reuniao), $updateData);

        $response->assertRedirect(route('reunioes.index'));
        $response->assertSessionHas('success');

        $reuniao->refresh();
        $this->assertEquals('Reunião Atualizada', $reuniao->titulo);
        $this->assertEquals('Descrição atualizada', $reuniao->descricao);
    }

    public function test_reuniao_destroy()
    {
        $reuniao = Reuniao::factory()->create();

        $response = $this->actingAs($this->user)->delete(route('reunioes.destroy', $reuniao));

        $response->assertRedirect(route('reunioes.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('reunioes', ['id' => $reuniao->id]);
    }

    public function test_reuniao_update_validation()
    {
        $reuniao = Reuniao::factory()->create();

        $response = $this->actingAs($this->user)->put(route('reunioes.update', $reuniao), [
            'titulo' => '', // Campo obrigatório vazio
            'data' => 'invalid-date',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['titulo', 'data']);
    }
}
