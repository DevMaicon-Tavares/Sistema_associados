<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_fillable_attributes()
    {
        $user = new User();
        $this->assertEquals(['name', 'email', 'password'], $user->getFillable());
    }

    public function test_user_has_hidden_attributes()
    {
        $user = new User();
        $this->assertEquals(['password', 'remember_token'], $user->getHidden());
    }

    public function test_user_can_be_created()
    {
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ];

        $user = User::create($data);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('test@example.com', $user->email);
    }
}
