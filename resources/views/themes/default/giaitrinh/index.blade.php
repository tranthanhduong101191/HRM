@extends(theme(TRUE).'.layout')

@section('title')
    Danh sách giải trình công
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">Danh sách giải trình công</h3>

                    <div class="box-tools pull-right">

                    </div>
                </div>
                <div class="box-body">
                    <div class="col-md-12">
                        <form class="form-inline">
                            <div class="form-group">
                                <label >Mã nhân viên</label>
                                <input type="text" class="form-control" id="user_id" placeholder="mã nhân viên" name="user_id" />
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail2">Từ ngày</label>
                                <input type="text" class="form-control" id="fromDate" name="fromDate" placeholder="ngày" autocomplete="off" />
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail2">đến ngày</label>
                                <input type="text" class="form-control" id="toDate" name="toDate" placeholder="ngày"  autocomplete="off" value=""/>
                            </div>
                            <button type="submit" class="btn btn-default"><i class="fa fa-search"></i> Tìm kiếm</button>
                        </form>
                    </div>
                    <div class="col-md-12">
                        <table class="table table-bordered" id="datatable">
                            <thead>
                            <tr>
                                <th>Nhân viên</th>
                                <th>Chấm công</th>
                                <th>Nội dung giải trình</th>
                                <th>Phản hồi từ admin</th>
                                <th>Hình ảnh</th>
                                <th>Trạng thái</th>
                                <th>Quản lý</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
    @include(theme(TRUE).'.blocks.addBonusModal')
    <link rel="stylesheet" href="{{asset('/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.standalone.min.css')}}" />
@endsection

@section('js')
    <script src="{{asset('plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
    <script>
        $(document).ready(function(){
            $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{!! route('explaint.data', ['user_id'=>request('user_id'), 'fromDate'=>request('fromDate'), 'toDate'=>request('toDate')]) !!}',
                columns: [
                    { data: 'user', name: 'user' , sortable:false, searchable: false},
                    { data: 'chamcong', name: 'chamcong' , sortable:false, searchable: false},
                    { data: 'content', name: 'content'},
                    { data: 'phanhoi', name: 'phanhoi'},
                    { data: 'hinhanh', name: 'hinhanh', sortable:false, searchable: false},
                    { data: 'status', name: 'status'},
                    { data: 'manage', name: 'manage', sortable:false, searchable: false}
                ]
            });
            $("#fromDate").datepicker({format: "dd/mm/yyyy"});
            $("#toDate").datepicker({format: "dd/mm/yyyy"});
            $('input[name=issued_at]').datepicker({format: "dd/mm/yyyy"});
            $(document).on('click', '.xemGiaitrinh', function () {
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
                                approve =   '<a class="btn btn-xs btn-primary approve" data-id="'+v.id+'" data-cong="'+v.chamcong+'">Phê duyệt</a> <a class="btn btn-xs btn-default no-approve" data-id="'+v.id+'">Không đồng ý</a>';
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
                phanhoi = $(this).data('phanhoi');
                bootbox.prompt({
                    title: "Hãy viết phản hồi cho nhân viên của bạn!",
                    inputType: 'text',
                    value: phanhoi,
                    callback: function (phanhoi) {
                        $.post('{{route('explaint.approve')}}', {id, phanhoi, status:-1, _token:'{{csrf_token()}}'}, function(){
                            window.location.reload();
                        });
                    }
                });
            });
        });
    </script>

@endsection
