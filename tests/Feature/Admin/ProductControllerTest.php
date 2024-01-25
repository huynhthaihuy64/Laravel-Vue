<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        if (!User::count()) {
            // Nếu không có, tạo người dùng mới
            $user = User::factory()->create();
        } else {
            // Nếu có, lấy người dùng đầu tiên từ cơ sở dữ liệu
            $user = User::first();
        }
        // Set up login
        $this->actingAs($user, 'api');
    }

    /** @test */
    public function getListProduct()
    {
        $response = $this->getJson(route('products.list'));
        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson(fn (AssertableJson $json) => 
            $json->has('data')
                ->has('links')
                ->has('meta')
        );
    }
}
