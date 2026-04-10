<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CltLayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $layups = DB::table('clt_layups')->get();

        foreach ($layups as $layup) {
            $isFiveLayer = str_contains($layup->name, '5 Layer');

            if ($isFiveLayer) {
                $layers = [
                    [1, 15, 140, 0],
                    [2, 15, 140, 90],
                    [3, 15, 140, 0],
                    [4, 15, 140, 90],
                    [5, 15, 140, 0],
                ];
            } else {
                $layers = [
                    [1, 20, 120, 0],
                    [2, 20, 120, 90],
                    [3, 20, 120, 0],
                ];
            }

            foreach ($layers as $layer) {
                DB::table('clt_layers')->insert([
                    'layup_id' => $layup->id,
                    'layer_order' => $layer[0],
                    'thickness' => $layer[1] + rand(-2, 2),
                    'width' => $layer[2] + rand(-5, 5),
                    'angle' => $layer[3],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
