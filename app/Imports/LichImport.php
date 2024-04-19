<?php

namespace App\Imports;

use App\Saplichthang;
use Maatwebsite\Excel\Concerns\ToModel;

class LichImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Saplichthang([
            //
        ]);
    }
}
