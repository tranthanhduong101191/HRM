@extends(theme(TRUE).'.layout')

@section('title')
    Sắp lịch tháng
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">Sắp lịch tháng {{request('thang')}}</h3>

                </div>
                <div class="box-body" id="mainside" style="overflow-x:auto;">
                    <form class="form-horizontal" method="post">
                        {{csrf_field()}}
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th rowspan="2">Mã NV</th>
                                <th rowspan="2">Tên</th>
                                @for($k = 1; $k<=$days; $k++)
                                    <th scope="col">{{($dayofweek<7 && $dayofweek>0)?('T'.($dayofweek+1)):'CN'}}</th>
                                    @php
                                        $dayofweek++;
                                        if($dayofweek>7)
                                            $dayofweek = 1;
                                    @endphp

                                @endfor
                            </tr>
                            <tr>
                                @for($k = 1; $k<=$days; $k++)
                                    <th>{{$k}}</th>
                                @endfor
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $item)
                            @php
                                $shift = \App\SapLichThang::where('user_id',$item->id)->where('thang',request('thang'))->first();
                                if(!empty($shift))
                                    $general_shift = (array)json_decode($shift->general_shift,true);
                            @endphp
                            <tr>
                                <td scope="row">{{$item->uid}}</td>
                                <td>{{$item->name}}</td>
                                @for($k = 1; $k<=$days; $k++)
                                    <td><input class="form-control set-shift" name="ca[{{$k}}][{{$item->id}}]" id="shift-{{$item->id}}-{{$k}}" data-day="{{$k}}" data-id="{{$item->id}}" value="{{!empty($item->lich[$k])?$item->lich[$k]->ca_name:''}}"></td>
                                @endfor
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <style>
        .set-shift {
            padding: 0 0 0 3px !important;
            font-size: 13px;
            min-width: 25px;
        }
    </style>
@endsection

@section('js')
    <script>
        $(document).on('blur', '.set-shift', function (e) {
            var id = $(this).data('id');
            var ca = $(this).val();
            var day = $(this).data('day');
            $('#shift-'+id+'-'+day).css("color",'black');

            $.post('{{asset('save-shift')}}', {id,ca,day,month:'{{$thang}}',year:'{{$nam}}', _token:'{{csrf_token()}}'}, function(r){
                if(r.success == false){
                    $('#shift-'+id+'-'+day).css("color",'red');
                    // alert("Thành viên không tồn tại ca này!");
                }
            });
        });
    </script>
@endsection
