@extends(theme(TRUE).'.layout')

@section('title')
    Lịch sử thưởng - phạt
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">Lịch sử thưởng - phạt</h3>

                    <div class="box-tools pull-right">
                        {!! a('thuong-phat/add','','Thêm thưởng - phạt', ['data-toggle'=>'modal', 'data-target'=>'#addBonusModal', 'class'=>'btn btn-sm btn-primary'],'','','post') !!}
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
                                <th>Ngày</th>
                                <th>Loại</th>
                                <th>Lý do</th>
                                <th>Số tiền</th>
                                <th>Quản lý</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
                ajax: '{!! route('bonusData', ['user_id'=>request('user_id'), 'fromDate'=>request('fromDate'), 'toDate'=>request('toDate')]) !!}',
                columns: [
                    { data: 'user', name: 'user' , sortable:false, searchable: false},
                    { data: 'issued_at', name: 'issued_at' },
                    { data: 'type', name: 'type'},
                    { data: 'reason', name: 'reason' },
                    { data: 'amount', name: 'amount'},
                    { data: 'manage', name: 'manage', sortable:false, searchable: false}
                ]
            });
            $("#fromDate").datepicker({format: "dd/mm/yyyy"});
            $("#toDate").datepicker({format: "dd/mm/yyyy"});
            $('input[name=issued_at]').datepicker({format: "dd/mm/yyyy"});
        });
    </script>

@endsection
