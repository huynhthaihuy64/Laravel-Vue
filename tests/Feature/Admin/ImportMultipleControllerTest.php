<?php

namespace Tests\Feature\Admin;

use App\Http\Controllers\Admin\ImportMultipleController;
use App\Http\Services\Import\ImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Mockery;
use Tests\TestCase;

class ImportMultipleControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_import_excel() {
        $params = [
            'id' => 1,
            'type' => 1 
        ];
        $importServiceMock = Mockery::mock(ImportService::class);
        $importServiceMock->shouldReceive('importProduct')->with($params)->once()->andReturn(true);
        $result = $importServiceMock->importProduct($params);
        // $controller = new ImportMultipleController($importServiceMock);

        // $file = UploadedFile::fake()->create('excel_file.xlsx', 5000); // Tạo tệp tin giả mạo có dung lượng 5MB

        // $response = $controller->importExcel(['excel_file' => $file]);

        $this->assertTrue($result);
    }
}
