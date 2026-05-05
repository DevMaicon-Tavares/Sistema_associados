<?php

namespace Tests\Feature;

use App\Models\Associado;
use App\Models\Reuniao;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssociadoTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
    }

    public function test_associados_index_requires_authentication()
    {
        $response = $this->get('/associados');

        $response->assertRedirect('/login');
    }

    public function test_associados_index_displays_associados()
    {
        $this->actingAs($this->user);

        Associado::create([
            'nome' => 'João Silva',
            'cpf' => '12345678901',
            'telefone' => '11987654321',
            'status_pagamento' => 'em dia',
        ]);

        $response = $this->get('/associados');

        $response->assertStatus(200);
        $response->assertViewIs('associados.index');
        $response->assertViewHas('associados');
    }

    public function test_associados_index_filters_by_status()
    {
        $this->actingAs($this->user);

        Associado::create([
            'nome' => 'João Silva',
            'cpf' => '12345678901',
            'status_pagamento' => 'em dia',
        ]);

        Associado::create([
            'nome' => 'Maria Silva',
            'cpf' => '12345678902',
            'status_pagamento' => 'atrasado',
        ]);

        $response = $this->get('/associados?status=em dia');

        $response->assertStatus(200);
        $response->assertViewHas('associados', function ($associados) {
            return $associados->count() == 1 && $associados->first()->nome == 'João Silva';
        });
    }

    public function test_associado_create_form()
    {
        $this->actingAs($this->user);

        $response = $this->get('/associados/create');

        $response->assertStatus(200);
        $response->assertViewIs('associados.create');
    }

    public function test_associado_store_creates_new_associado()
    {
        $this->actingAs($this->user);

        $data = [
            'nome' => 'João Silva',
            'cpf' => '12345678901',
            'telefone' => '11987654321',
            'status_pagamento' => 'em dia',
        ];

        $response = $this->post('/associados', $data);

        $response->assertRedirect('/associados');
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('associados', $data);
    }

    public function test_associado_store_validates_required_fields()
    {
        $this->actingAs($this->user);

        $response = $this->post('/associados', []);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['nome', 'cpf', 'status_pagamento']);
    }

    public function test_associado_store_validates_unique_cpf()
    {
        $this->actingAs($this->user);

        Associado::create([
            'nome' => 'João Silva',
            'cpf' => '12345678901',
            'status_pagamento' => 'em dia',
        ]);

        $response = $this->post('/associados', [
            'nome' => 'Maria Silva',
            'cpf' => '12345678901',
            'status_pagamento' => 'atrasado',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('cpf');
    }

    public function test_associado_show_displays_associado_and_reunioes()
    {
        $this->actingAs($this->user);

        $associado = Associado::create([
            'nome' => 'João Silva',
            'cpf' => '12345678901',
            'status_pagamento' => 'em dia',
        ]);

        Reuniao::create([
            'titulo' => 'Reunião Futura',
            'data' => now()->addDays(1),
            'horario' => '10:00',
        ]);

        Reuniao::create([
            'titulo' => 'Reunião Passada',
            'data' => now()->subDays(1),
            'horario' => '10:00',
        ]);

        $response = $this->get("/associados/{$associado->id}");

        $response->assertStatus(200);
        $response->assertViewIs('associados.show');
        $response->assertViewHas(['associado', 'reunioesFuturas', 'reunioesPassadas']);
    }

    public function test_update_status_updates_associado_status()
    {
        $this->actingAs($this->user);

        $associado = Associado::create([
            'nome' => 'João Silva',
            'cpf' => '12345678901',
            'status_pagamento' => 'em dia',
        ]);

        $response = $this->patch("/associados/{$associado->id}/status", [
            'status_pagamento' => 'atrasado',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertEquals('atrasado', $associado->fresh()->status_pagamento);
    }
}
