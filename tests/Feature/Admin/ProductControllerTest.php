<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Http\Response;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    //Chạy trước mỗi method test. Sử dụng khi muốn khởi tạo môi trường test
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

        //Check tồn tại
        // $response->assertJson(fn (AssertableJson $json) => 
        //     $json->has('data')
        //         ->has('links')
        //         ->has('meta')
        // );

        //Check cấu trúc return Json
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['*' => [
                    'id',
                    'name',
                    'price',
                    'created_at',
                ]]
            ]);
    }
}
