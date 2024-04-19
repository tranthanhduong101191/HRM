<?php

namespace App\Exports;

use App\Chamcong;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class ChamcongExport implements FromView
{
    public $data;

    public function __construct($data)
    {
        $this->data =   $data;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view() : View
    {
        return view('exports.chamcong', [
            'data' => $this->data['data'],
            'thang' => $this->data['thang'],
            'nam' => $this->data['nam'],
            'days' => $this->data['days'],
            'dayofweek' => $this->data['dayofweek']
        ]);
    }
}
