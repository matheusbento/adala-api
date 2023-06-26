<?php

namespace App\Jobs;

use App\Http\Facades\CubeService;
use App\Models\Cube;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessLoadCubeByIdJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public Cube $cube;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Cube $cube)
    {
        $this->cube = $cube;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        CubeService::processByIds([$this->cube->id]);
    }
}
