<?php

namespace App\Jobs;

use App\Http\Facades\SiloFileService;
use App\Models\SiloFile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PreProcessSiloFileByIdJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public SiloFile $siloFile;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(SiloFile $siloFile)
    {
        $this->siloFile = $siloFile;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        SiloFileService::preProcessByIds([$this->siloFile->id]);
    }
}
