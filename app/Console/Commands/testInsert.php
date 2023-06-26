<?php

namespace App\Console\Commands;

ini_set('memory_limit', '-1');

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use pcrov\JsonReader\JsonReader;

class testInsert extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:insert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected $spectraHeader = null;
    protected $lightCurveHeader = null;
    protected $dataHeader = null;
    protected $spectras = null;
    protected $lightCurves = null;
    protected $dataToInsert = null;

    public function mapKey($array, $key)
    {
        if (isset($array[strtoupper($key)])) {
            if (is_array($array[strtoupper($key)])) {
                return json_encode($array[strtoupper($key)]);
            }
            return $array[strtoupper($key)];
        }

        if (isset($array[strtolower($key)])) {
            if (is_array($array[strtolower($key)])) {
                return json_encode($array[strtolower($key)]);
            }
            return $array[strtolower($key)];
        }

        return '-';
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // $result = Schema::connection('timescale')->create('principal_data', function($table)
        // {
        //     $table->increments('id');
        //     $table->string('sn_name');
        //     $table->string('host_galaxy');
        //     $table->string('object_type');
        //     $table->string('catalog_name');
        //     $table->string('author_reference');
        // });

        // Schema::connection('timescale')->create('spectra', function($table)
        // {
        //     $table->increments('id');
        //     $table->string('file_name');
        //     $table->string('capture_date');
        //     $table->string('instruments');
        //     $table->string('catalog_name');
        //     $table->string('author_reference');
        //     $table->json('snx_wavelengths');
        //     $table->json('sny_flux');
        //     $table->string('z_redshift');
        //     $table->string('host_galaxy');
        //     $table->string('object_type');
        //     $table->json('snx_normalized');
        //     $table->json('sny_normalized');
        //     $table->unsignedBigInteger('event_id');
        // });

        // Schema::connection('timescale')->create('curve_light', function ($table) {
        //     $table->increments('id');
        //     $table->string('object_name');
        //     $table->json('time_mjd_band_u');
        //     $table->json('mag_band_u');
        //     $table->json('time_mjd_band_v');
        //     $table->json('mag_band_v');
        //     $table->json('time_mjd_band_b');
        //     $table->json('mag_band_b');
        //     $table->json('time_mjd_band_r');
        //     $table->json('mag_band_r');
        //     $table->json('time_mjd_band_i');
        //     $table->json('mag_band_i');
        //     $table->unsignedBigInteger('event_id');
        // });

        // dd($result);

        $path = storage_path() . '/part2.json';

        $this->spectras = collect([]);
        $this->lightCurves = collect([]);
        $reader = new JsonReader();
        $reader->open($path);

        $depth = $reader->depth();

        $this->dataToInsert = [];
        $reader->read();
        do {
            if ($reader->type() === JsonReader::ARRAY) {
                $datas = collect($reader->value());
                $datas->map(function ($data) {
                    $data = collect($data);
                    $data['id'] = $data['index1'] + 1;
                    $spectra = collect($data['SPECTRA'])->map(fn ($e) => collect($e));
                    $lightCurve = collect($data['LIGHT_CURVE'])->map(fn ($e) => collect($e));

                    unset($data['SPECTRA'], $data['LIGHT_CURVE'], $data['index1']);

                    if (!$this->dataHeader) {
                        $this->dataHeader = collect($data->keys()->map(fn ($e) => strtolower($e))->toArray());
                    }

                    if (!$this->spectraHeader) {
                        $this->spectraHeader = collect(array_merge($spectra->first()->keys()->map(fn ($e) => strtolower($e))->toArray(), ['event_id']));
                    }

                    if (!$this->lightCurveHeader) {
                        $this->lightCurveHeader = collect(array_merge($lightCurve->first()->keys()->map(fn ($e) => strtolower($e))->toArray(), ['event_id']));
                    }


                    $spectra->map(function ($value) use ($data) {
                        $mapped = $this->spectraHeader->map(function ($head) use ($value, $data) {
                            if ($head == 'event_id') {
                                return [$head => $data['id']];
                            }
                            return [$head => $this->mapKey($value, $head)];
                        });
                        $this->spectras->add($mapped->values()->mapWithKeys(function ($a) {
                            return $a;
                        })->toArray());
                    });

                    $lightCurve->map(function ($value) use ($data) {
                        $mapped = $this->lightCurveHeader->map(function ($head) use ($value, $data) {
                            if ($head == 'event_id') {
                                return [$head => $data['id']];
                            }
                            return [$head => $this->mapKey($value, $head)];
                        });
                        $this->lightCurves->add($mapped->values()->mapWithKeys(function ($a) {
                            return $a;
                        })->toArray());
                    });

                    $data = $this->dataHeader->map(function ($head) use ($data) {
                        return [$head => $this->mapKey($data, $head)];
                    })->values()->mapWithKeys(function ($a) {
                        return $a;
                    });

                    $this->dataToInsert[] = $data->all();
                });
            }
        } while ($reader->next() && $reader->depth() > $depth);

        // dd($this->dataToInsert, $this->dataHeader, 123, $this->spectraHeader, $this->lightCurveHeader);

        // DB::connection('timescale')->table('principal_data')->insert($this->dataToInsert);

        // DB::connection('timescale')->table('spectra')->insert($this->spectras->all());
        DB::connection('timescale')->table('curve_light')->insert($this->lightCurves->all());

        return 0;
    }
}
