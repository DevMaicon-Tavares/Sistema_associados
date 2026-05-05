<?php

namespace Tests\Feature;

use App\Models\Reuniao;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReuniaoTest extends TestCase
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

    public function test_reunioes_index_requires_authentication()
    {
        $response = $this->get('/reunioes');

        $response->assertRedirect('/login');
    }

    public function test_reunioes_index_displays_reunioes()
    {
        $this->actingAs($this->user);

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

        $response = $this->get('/reunioes');

        $response->assertStatus(200);
        $response->assertViewIs('reunioes.index');
        $response->assertViewHas(['reunioesFuturas', 'reunioesPassadas']);
    }

    public function test_reunioes_index_filters_by_type_futuras()
    {
        $this->actingAs($this->user);

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

        $response = $this->get('/reunioes?type=futuras');

        $response->assertStatus(200);
        $response->assertViewHas('reunioesFuturas', function ($reunioes) {
            return $reunioes->count() == 1 && $reunioes->first()->titulo == 'Reunião Futura';
        });
    }

    public function test_reunioes_index_filters_by_type_passadas()
    {
        $this->actingAs($this->user);

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

        $response = $this->get('/reunioes?type=passadas');

        $response->assertStatus(200);
        $response->assertViewHas('reunioesPassadas', function ($reunioes) {
            return $reunioes->count() == 1 && $reunioes->first()->titulo == 'Reunião Passada';
        });
    }

    public function test_reunioes_index_filters_by_period_mes_atual()
    {
        $this->actingAs($this->user);

        Reuniao::create([
            'titulo' => 'Reunião Este Mês',
            'data' => now()->addDays(5),
            'horario' => '10:00',
        ]);

        Reuniao::create([
            'titulo' => 'Reunião Próximo Mês',
            'data' => now()->addMonth()->startOfMonth()->addDays(5),
            'horario' => '10:00',
        ]);

        $response = $this->get('/reunioes?period=mes_atual');

        $response->assertStatus(200);
        $response->assertViewHas('reunioesFuturas', function ($reunioes) {
            return $reunioes->count() == 1 && $reunioes->first()->titulo == 'Reunião Este Mês';
        });
    }

    public function test_reuniao_create_form()
    {
        $this->actingAs($this->user);

        $response = $this->get('/reunioes/create');

        $response->assertStatus(200);
        $response->assertViewIs('reunioes.create');
    }

    public function test_reuniao_store_creates_new_reuniao()
    {
        $this->actingAs($this->user);

        $data = [
            'titulo' => 'Reunião Mensal',
            'descricao' => 'Discussão sobre projetos',
            'data' => '2026-05-01',
            'horario' => '14:00',
        ];

        $response = $this->post('/reunioes', $data);

        $response->assertRedirect('/reunioes');
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('reunioes', [
            'titulo' => 'Reunião Mensal',
            'descricao' => 'Discussão sobre projetos',
            'data' => '2026-05-01 00:00:00',
            'horario' => '14:00',
        ]);
    }

    public function test_reuniao_store_validates_required_fields()
    {
        $this->actingAs($this->user);

        $response = $this->post('/reunioes', []);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['titulo', 'data', 'horario']);
    }
}
