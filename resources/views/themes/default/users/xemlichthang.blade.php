@extends(theme(TRUE).'.layout')

@section('title')
    Xem lịch xếp
@endsection

@section('content')

    <pre>
<!--        --><?php
//        print_r($data_suacong);
//        ?>
    </pre>
    <div class="row">
        <div class="col-md-12">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">Xem lịch tháng {{request('thang')}}</h3>
                    @if(auth()->user()->group_id <4)
                    <a class="btn btn-success" style="float: right" href="#">Xem các lần đổi ca liên quan</a>
                    <a class="btn btn-default" style="float: right; margin-right: 5px" href="{{route('saplichthang',['thang'=>Carbon\Carbon::now()->format('m'),'nam'=>Carbon\Carbon::now()->format('Y')])}}">Sắp lịch làm việc</a>
                    @endif
                    @if(auth()->user()->group_id <3)
                    <a class="btn btn-info" style="float: right; margin-right: 5px" href="#" data-toggle="modal" data-target="#uploadExcel">Up lịch Excel</a>
                    @endif
                </div>
                <div class="box-body" id="mainside" style="overflow-x:auto;">
                    <form>
                        {{csrf_field()}}
                        <div class="panel">
                            <div class="box-body">
                                <div class="col-md-12">
                                    {{--<div class="col-md-2">--}}
                                        {{--<input class="form-control datepicker" name="month" value="{{old('month',request('month'))}}">--}}
                                        {{--@if(!empty(request('month')))--}}
                                            {{--<a class="btn btn-default" style="float: left; margin-right: 5px" href="{{route('xemlichthang',['thang'=>Carbon\Carbon::createFromFormat(request('month'),'MM/YYYY')->startOfMonth()->format('m'),'nam'=>Carbon\Carbon::createFromFormat(request('month'),'MM/YYYY')->startOfMonth()->format('Y')])}}">Xem</a>--}}
                                        {{--@else--}}
                                            {{--<a class="btn btn-default" style="float: left; margin-right: 5px" href="{{route('xemlichthang',['thang'=>Carbon\Carbon::now()->startOfMonth()->format('m'),'nam'=>Carbon\Carbon::now()->startOfMonth()->format('Y')])}}">Xem</a>--}}
                                        {{--@endif--}}
                                    {{--</div>--}}
                                    <a class="btn btn-default" style="float: left; margin-right: 5px" href="{{route('xemlichthang',['thang'=>Carbon\Carbon::now()->startOfMonth()->subMonth()->format('m'),'nam'=>Carbon\Carbon::now()->startOfMonth()->subMonth()->format('Y')])}}"><< Tháng trước</a>
                                    <a class="btn btn-primary" style="float: left; margin-right: 5px" href="{{route('xemlichthang',['thang'=>Carbon\Carbon::now()->format('m'),'nam'=>Carbon\Carbon::now()->format('Y')])}}">Tháng này</a>
                                    <a class="btn btn-default" style="float: left" href="{{route('xemlichthang',['thang'=>Carbon\Carbon::now()->startOfMonth()->addMonths(1)->format('m'),'nam'=>Carbon\Carbon::now()->startOfMonth()->addMonth()->format('Y')])}}">Tháng sau >></a>
                                </div>
                            </div>
                            @if(auth()->user()->group_id <4)
                            <div class="box-body">
                                <div class="col-md-2">
                                    <input class="form-control" name="uid" placeholder="Mã nhân viên" value="{{old('uid',request('uid'))}}">
                                </div>
                                <div class="col-md-2">
                                    <select class="form-control" name="branch_id">
                                        <option value="">--Tất cả bộ phận--</option>
                                        @foreach(\App\Branch::all() as $g)
                                            <option value="{{$g->id}}" @if(request('branch_id') == $g->id) selected @endif>{{$g->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="checkbox" name="excel" value="1" /> Xuất Excel
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-primary" id="search-button" type="submit"><i class="fa fa-search fa-fw"></i> Tìm kiếm</button>
                                </div>
                            </div>
                            @endif
                        </div>

                    </form>
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th rowspan="2" colspan="2">Tháng {{$thang}}</th>
                            @for($k = 1; $k<=$days; $k++)
                                <th>{{$k}}</th>
                            @endfor
                            <th rowspan="2">Tổng</th>
                        </tr>
                        <tr>
                            @for($k = 1; $k<=$days; $k++)
                                <th scope="col" @if($dayofweek==6 || $dayofweek==7 || $dayofweek==0) style="background-color: red" @endif>{{($dayofweek<7 && $dayofweek>0)?('T'.($dayofweek+1)):'CN'}}</th>
                                @php
                                    $dayofweek++;
                                    if($dayofweek>7)
                                        $dayofweek = 1;
                                @endphp
                            @endfor
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $item)
                            @php
                                $tongcong   =   0;
                            @endphp
                            <tr>
                                <td scope="row" rowspan="2">{{$item->uid}}</td>
                                <td rowspan="2">{{$item->name}}</td>
                                @for($k = 1; $k<=$days; $k++)
                                    <td>{{!empty($item->lich[$k])?$item->lich[$k]->ca_name:''}}</td>
                                    {{--<td>{{!empty($shift->$k)?$shift->$k:''}}</td>--}}
                                @endfor
                            </tr>
                            <tr>
                                @for($k = 1; $k<=$days; $k++)
                                    <?php
                                    $color  =   '#333';
                                    $bgcolor    =   '#fff';
                                        if(!empty($item->lich[$k])){
                                            if($item->lich[$k]->congmaycham != $item->lich[$k]->cong){
                                                $color  =   '#333';
                                                $bgcolor  =   '#f0ad4e';
                                            }
                                        }

                                    ?>

                                    <td class="hasModal" data-id="{{!empty($item->lich[$k])?$item->lich[$k]->id:''}}" data-loi="{{!empty($item->lich[$k])?$item->lich[$k]->loi:''}}" data-cong =   "{{!empty($item->lich[$k])?json_encode($item->lich[$k]):''}}" style="color: {{$color}}; background-color: {{$bgcolor}}">{{!empty($item->lich[$k])?$item->lich[$k]->cong:''}}</td>
                                    <?php if(!empty($item->lich[$k])) $tongcong += $item->lich[$k]->cong; ?>
                                @endfor
                                <td>{{$tongcong}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <style>
        table.table-bordered{
            border:1px solid #ccc;
            margin-top:20px;
        }
        table.table-bordered > thead > tr > th{
            border:1px solid #ccc;
        }
        table.table-bordered > tbody > tr > td{
            border:1px solid #ccc;
        }
        .set-shift {
            padding: 0 0 0 3px !important;
            font-size: 13px;
        }
        th {
            text-align: center;
            background-color: yellow;
        }
        td {
            text-align: center;
        }
        .hasModal {
            cursor: pointer;
        }
    </style>
    <div class="modal fade" id="uploadExcel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <form method="post" class="form-horizontal" action="{{route('uplichExcel')}}" enctype="multipart/form-data">
                {{csrf_field()}}
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Upload lịch bằng file Excel</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">File excel</label>
                            <div class="col-sm-10">
                                <input type="file" class="form-control" name="file" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Tháng/Năm</label>
                            <div class="col-sm-5">
                                <select name="thang" class="form-control">
                                    @for($i=1;$i<=12;$i++)
                                        <option value="{{$i}}" @if((($i-1)==\Carbon\Carbon::now()->month) || (\Carbon\Carbon::now()->month == 12 && $i==1))selected @endif>{{$i}}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-sm-5">
                                <input type="number" class="form-control" name="nam" value="{{\Carbon\Carbon::now()->month == 12?\Carbon\Carbon::now()->year+1:\Carbon\Carbon::now()->year}}" />
                            </div>
                        </div>
                        <div class="form-group">

                            <div class="col-sm-10 col-sm-offset-2">
                                <input type="checkbox" name="overwrite" /> Up đè lịch cũ
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a class="btn btn-info" href="{{asset('uploads/Lich.xlsx')}}"><i class="fa fa-download"></i> File lịch mẫu</a>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-upload"></i> Upload lịch</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
    <div class="modal fade" id="congDetailModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <form method="post" class="form-horizontal" action="{{route('uplichExcel')}}" enctype="multipart/form-data">
                {{csrf_field()}}
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Chi tiết chấm công</h4>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered">
                            <tr>
                                <th>Ngày: <i><b id="detailModalDate"></b></i></th>
                                <th>Công: <i><b id="detailModalCong"></b></i></th>
                                <th>Ca: <i><b id="detailModalCa"></b></i></th>
                            </tr>
                            <tr>
                                <th>Lượt chấm</th>
                                <td id="detailModalCham" colspan="2"></td>
                            </tr>
                            <tr>
                                <th>Lỗi</th>
                                <td id="detailModalLoi" colspan="2"></td>
                            </tr>
                        </table>
                        <div class="panel-group" id="accordion">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">
                                            Lịch sử sửa công</a>
                                    </h4>
                                </div>
                                <div id="collapse1" class="panel-collapse collapse in">
                                    <div class="panel-body" id="detailModalSuacong">

                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse2">
                                            Giải trình</a>
                                    </h4>
                                </div>
                                <div id="collapse2" class="panel-collapse collapse">
                                    <div class="panel-body" id="detailModalGiaitrinh">
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse3">
                                            Thưởng phạt</a>
                                    </h4>
                                </div>
                                <div id="collapse3" class="panel-collapse collapse">
                                    <div class="panel-body" id="detailModalThuongphat">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">


                    </div>
                </div>
            </form>

        </div>
    </div>
@endsection

@section('js')
    <script>
        $('.datepicker').datetimepicker({format: "MM/YYYY"});
        $(document).ready(function(){
            $('.hasModal').click(function(){
                id  =   $(this).data('id');
                $.get('{{route('getCongData')}}', {id}, function(r){
                    congDetail  =   r.data.cong;
                    $('#detailModalDate').html(congDetail.ngay);
                    $('#detailModalCong').html(congDetail.cong);
                    $('#detailModalCa').html(congDetail.ca_name);

                    $('#detailModalCham').html('');
                    $.each(JSON.parse(congDetail.data_cham), function(k,v){
                        $('#detailModalCham').append(v+' - ');
                    });
                    $('#detailModalLoi').html('');
                    $.each(JSON.parse(congDetail.loi), function(k,v){
                        $('#detailModalLoi').append(v);
                    });

                    giaitrinh   =   r.data.giaitrinh.data;
                    suacong =   r.data.suacong.data;
                    thuongphat = r.data.thuongphat.data;

                    if(giaitrinh.length !== 0){
                        $('#detailModalGiaitrinh').html('');
                    } else {
                        $('#detailModalGiaitrinh').html('<i>Không có dữ liệu</i>');
                    }

                    if(suacong.length !== 0){
                        $('#detailModalSuacong').html('');
                    } else {
                        $('#detailModalSuacong').html('<i>Không có dữ liệu</i>');
                    }
                    if(thuongphat.length !== 0){
                        $('#detailModalThuongphat').html('');
                    } else {
                        $('#detailModalThuongphat').html('<i>Không có dữ liệu</i>');
                    }
                    $.each(giaitrinh, function(k,v){
                        $('#detailModalGiaitrinh').append("<div class=\"alert alert-info\" role=\"alert\">\n" +
                            "      "+ 'Nội dung nguyên nhân: ' + v.content + '          - Phản hồi từ admin: ' + v.phanhoi +
                            "    </div>");
                    });
                    $.each(suacong, function(k,v){
                        $('#detailModalSuacong').append("<div class=\"alert alert-info\" role=\"alert\">\n" +
                            "      Sửa từ " + v.congcu+ " sang " + v.congmoi + " Lý do: "+v.reason+
                            "    </div>");
                    });
                    $.each(thuongphat, function(k,v){
                        if(v.type  == 1){
                            type = 'Phạt trừ ';
                        } else {
                            type = 'Thưởng cộng ';
                        }
                        $('#detailModalThuongphat').append("<div class=\"alert alert-info\" role=\"alert\">\n" +
                            "      " + type + v.amount + '. Lý do: '+v.reason+
                            "    </div>");
                    });

                    $('#congDetailModal').modal('show');
                });
            });
        });
    </script>
@endsection
