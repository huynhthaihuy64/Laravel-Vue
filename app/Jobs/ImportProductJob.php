<?php

namespace App\Jobs;

use App\Imports\ProductImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ImportProductJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        protected $file,
        protected $uploadService,
        protected $productService,
        protected $user
    ) {
        $this->file = $file;
        $this->uploadService = $uploadService;
        $this->productService = $productService;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        Auth::setUser($this->user);
        $import = new ProductImport(
            $this->file,
            $this->uploadService,
            $this->productService,
        );

        Excel::import($import, $this->file['file_path']);
    }
}
