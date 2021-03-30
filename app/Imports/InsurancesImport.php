<?php

namespace App\Imports;

use App\Model\Insurance;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class InsurancesImport implements ToCollection,WithHeadingRow{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $payername = isset($row['payername'])?$row['payername']:'';
            $payer_code = isset($row['payer_code'])?$row['payer_code']:null;
            $ins = Insurance::firstOrCreate(['name' => $payername]);
            if($ins){
                $ins->carrier_code = $payer_code?$payer_code:$ins->carrier_code;
                $ins->save();
            }
        }
    }
}
