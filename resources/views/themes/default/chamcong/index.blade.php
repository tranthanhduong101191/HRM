@extends(theme(TRUE).'.layout')

@section('title')
    Tổng hợp công
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">Tổng hợp công</h3>

                    <div class="box-tools pull-right">

                    </div>
                </div>
                <div class="box-body">
                    <div class="col-md-12">
                        <form class="form-inline">
                            @if(auth()->user()->group_id <4)
                                <div class="form-group">
                                    <label >Mã nhân viên</label>
                                    <input type="text" class="form-control" id="user_id" placeholder="mã nhân viên" name="user_id" value="{{request('user_id')}}" />
                                </div>
                                <div class="form-group">
                                    <label >Tên (tìm gần đúng)</label>
                                    <input type="text" class="form-control" id="name" placeholder="tìm gần đúng" name="name" value="{{request('name')}}" />
                                </div>
                            @endif
                            <div class="form-group">
                                <label for="exampleInputEmail2">Từ ngày</label>
                                <input type="text" class="form-control" id="fromDate" name="fromDate" placeholder="ngày" value="{{request('fromDate')}}" autocomplete="off" />
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail2">đến ngày</label>
                                <input type="text" class="form-control" id="toDate" name="toDate" placeholder="ngày" value="{{request('toDate')}}" autocomplete="off" value=""/>
                            </div>

                            <button type="submit" class="btn btn-default"><i class="fa fa-search"></i> Tìm kiếm</button>
                            @if(auth()->user()->group_id <4)
                                <a class="btn btn-danger tinhlaitheongay"><i class="fa fa-refresh"></i> Tính lại theo ngày!</a>
                            @endif
                        </form>
                    </div>
                    <div class="col-md-12" style="margin-top: 20px">
                        {{$data->appends($_GET)->render()}}
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Nhân viên</th>
                                <th>Ngày</th>
                                <th>Ca</th>
                                <th>Chấm lần 1</th>
                                <th>Chấm lần 2</th>
                                <th>Chấm lần 3</th>
                                <th>Chấm lần 4</th>
                                <th>Công</th>
                                <th style="width: 200px" >Lỗi</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $item)
                                <?php
                                $data_cham  =   json_decode($item->data_cham, true);
                                $loi    =   json_decode($item->loi, true);
                                ?>
                                <tr>
                                    <td>{{$item->user->uid}} - {{$item->user->name}}</td>
                                    <td>{{\Carbon\Carbon::parse($item->ngay)->format('d/m/Y')}}</td>
                                    <td>{{$item->ca_name}}</td>
                                    <td>{{$vao1 = isset($data_cham['vao1'])? $data_cham['vao1']:''}}</td>
                                    <td>{{$vao1 = isset($data_cham['ra1'])? $data_cham['ra1']:''}}</td>
                                    <td>{{$vao1 = isset($data_cham['vao2'])? $data_cham['vao2']:''}}</td>
                                    <td>{{$vao1 = isset($data_cham['ra2'])? $data_cham['ra2']:''}}</td>
                                    <td>{{$item->cong}} {{$item->parttime == 1?'giờ':''}}</td>
                                    <td>
                                        {{!empty($loi)?implode(', ', $loi):''}}
                                        @if(!empty($item->giaitrinh()->count()))
                                            <a class="btn btn-info xemGiaitrinh btn-xs" data-id="{{$item->id}}">Xem giải trình</a>
                                        @endif
                                    </td>
                                    <!-- <td>
                                        @if(\Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($item->ngay))<=5)
                                        @if($item->user_id == auth()->user()->id  && !empty($loi))
                                            <a class="btn btn-xs btn-primary giaitrinh" data-id="{{$item->id}}">Giải trình</a>
                                        @endif
                                        @endif
                                        @if(auth()->user()->group_id == 1 || (auth()->user()->group_id == 2 && auth()->user()->branch_id == $item->user->group_id))
                                            <a class="btn btn-xs btn-danger tinhlai" data-id="{{$item->id}}" ><i class="fa fa-refresh"></i> Tính lại</a>
                                            <a class="btn btn-xs btn-info suacong" data-id="{{$item->id}}" data-cong="{{$item->cong}}">Sửa công</a>
                                        @endif
                                    </td> -->
                                    <td>
                                        @if(\Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($item->ngay))<=5)
                                        @if($item->user_id == auth()->user()->id)
                                            <a class="btn btn-xs btn-primary giaitrinh" data-id="{{$item->id}}">Giải trình</a>
                                        @endif
                                        @endif
                                        @if(auth()->user()->group_id == 1 || (auth()->user()->group_id == 2 && auth()->user()->branch_id == $item->user->group_id))
                                            <a class="btn btn-xs btn-danger tinhlai" data-id="{{$item->id}}" ><i class="fa fa-refresh"></i> Tính lại</a>
                                            <a class="btn btn-xs btn-info suacong" data-id="{{$item->id}}" data-cong="{{$item->cong}}">Sửa công</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {{$data->appends($_GET)->render()}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <link rel="stylesheet" href="{{asset('/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.standalone.min.css')}}" />
    <style type="text/css">
        .giaitrinhImg {
            max-width: 100%;
            /*max-height: 200px;*/
        }
    </style>
    @include(theme(TRUE).'.blocks.giaitrinhModal')
    @include(theme(TRUE).'.blocks.suacongModal')
    @include(theme(TRUE).'.blocks.tinhlaitheongayModal')
    @include(theme(TRUE).'.blocks.giaitrinhList')
@endsection

@section('js')
    <script src="{{asset('plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
    <script>
        $(document).ready(function(){
            $("#fromDate1").datepicker({format: "dd/mm/yyyy"});
            $("#fromDate").datepicker({format: "dd/mm/yyyy"});
            $("#toDate").datepicker({format: "dd/mm/yyyy"});
            $('.giaitrinh').click(function(){
                var id = $(this).data('id');
                $('#giaitrinh_chamcong_id').val(id);
                $('#giaitrinhModal').modal('show');
            });
            $('.suacong').click(function(){
                var id = $(this).data('id');
                var cong = $(this).data('cong');
                $('#suacong_chamcong_id').val(id);
                $('#suacong_congmoi').val(cong);
                $('#suacongModal').modal('show');
            });
            $('.tinhlaitheongay').click(function(){
                var id = $(this).data('id');
                var cong = $(this).data('cong');
                $('#suacong_chamcong_id').val(id);
                $('#suacong_congmoi').val(cong);
                $('#tinhlaitheongayModal').modal('show');
            });
            $('.xemGiaitrinh').click(function(){
                var id  =   $(this).data('id');
                $('#giaitrinhPanelList').html('');
                $.get('{{route('explaint.ajax')}}', {id}, function(r){
                    if(r.status == 0){
                        $.each(r.data, function(k,v){
                            console.log(v);
                            images  =   '';
                            $.each(v.images, function(i,j){
                                images+= '<img src="uploads/'+j+'" class="giaitrinhImg" />';
                            });
                            expanded = 'false';
                            inning = '';
                            if(k==0){
                                expanded='true';
                                inning = 'in';
                            }
                            if(v.status == 0)
                                approve =   '<a class="btn btn-xs btn-primary approve" data-id="'+v.id+'" data-cong="'+v.chamcong.cong+'">Phê duyệt</a> <a class="btn btn-xs btn-default no-approve" data-id="'+v.id+'">Không đồng ý</a>';
                            else if(v.status == 1)
                                approve = '<i class="fa fa-check">Đã duyệt</i>';
                            else if(v.status == -1)
                                approve = '<i class="fa fa-times">Đã huỷ</i>';
                            $('#giaitrinhPanelList').append('<div class="panel panel-default">\n' +
                                '                        <div class="panel-heading" role="tab" id="heading'+k+'">\n' +
                                '                            <h4 class="panel-title">\n' +
                                '                                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse'+k+'" aria-expanded="'+expanded+'" aria-controls="collapse'+k+'">\n' +
                                '                                    '+v.created_at+'</h3> ' + approve +
                                '                                </a>\n' +
                                '                            </h4>\n' +
                                '                        </div>\n' +
                                '                        <div id="collapse'+k+'" class="panel-collapse collapse '+inning+'" role="tabpanel" aria-labelledby="heading'+k+'">\n' +
                                '                            <div class="panel-body">\n' +
                                '' +v.content+images+
                                '                            </div>\n' +
                                '                        </div>\n' +
                                '                    </div>');

                        });
                    }
                });
                $('#giaitrinhListModal').modal('show');
            });
            $('#giaitrinhListModal').on('click', '.approve', function(){
                id =    $(this).data('id');
                cong    =   $(this).data('cong');
                bootbox.prompt({
                    title: "Nếu có thay đổi về công, điền công mới dưới đây:",
                    inputType: 'number',
                    value: cong,
                    callback: function (cong) {
                        $.post('{{route('explaint.approve')}}', {id, cong, status:1, _token:'{{csrf_token()}}'}, function(){
                            window.location.reload();
                        });
                    }
                });
            });
            $('#giaitrinhListModal').on('click', '.no-approve', function(){
                id =    $(this).data('id');
                $.post('{{route('explaint.approve')}}', {id, status:-1, _token:'{{csrf_token()}}'}, function(){
                    window.location.reload();
                });
            });
            $('.tinhlai').click(function(){
                id = $(this).data('id');
                $.get('{{route('tinhlai')}}', {id}, function(){
                    window.location.reload();
                });
            });
        });
    </script>

@endsection
