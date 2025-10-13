<?php

namespace Tests\Feature;

use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WarehouseApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_all_warehouses()
    {
        Warehouse::factory()->count(3)->create();

        $response = $this->getJson('/api/warehouses');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }

    public function test_can_create_warehouse()
    {
        $data = [
            'name' => 'Тестовый склад',
            'phone' => '+7 495 123-45-67',
            'address' => 'Москва, ул. Тестовая, д. 1',
            'postal_code' => '123456',
            'meta' => ['capacity' => 1000],
        ];

        $response = $this->postJson('/api/warehouses', $data);

        $response->assertStatus(201)
                 ->assertJson(['data' => ['name' => 'Тестовый склад']]);
        
        $this->assertDatabaseHas('warehouses', ['name' => 'Тестовый склад']);
    }

    public function test_can_update_warehouse()
    {
        $warehouse = Warehouse::factory()->create();

        $response = $this->putJson("/api/warehouses/{$warehouse->id}", [
            'name' => 'Обновленное название',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('warehouses', [
            'id' => $warehouse->id,
            'name' => 'Обновленное название',
        ]);
    }
}