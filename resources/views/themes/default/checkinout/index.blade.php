@extends(theme(TRUE).'.layout')

@section('title')
    Lịch sử chấm công
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">Lịch sử chấm công</h3>

                    <div class="box-tools pull-right">

                    </div>
                </div>
                <div class="box-body">
                    <div class="col-md-12">
                        <form class="form-inline">
                            @if(auth()->user()->group_id != 4)
                            <div class="form-group">
                                <label >Mã nhân viên</label>
                                <input type="text" class="form-control" id="user_id" placeholder="mã nhân viên" name="user_id" />
                            </div>
                            @endif
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
                                <th>Mã NV</th>
                                <th>Tên NV</th>
                                <th>Ngày</th>
                                <th>Giờ chấm</th>
                                <th>Chấm tại máy</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <link rel="stylesheet" href="{{asset('/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.standalone.min.css')}}" />
@endsection

@section('js')
    <script src="{{asset('plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
    <script>
        $(document).ready(function(){
            $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{!! route('checkinHistoryData', ['user_id'=>request('user_id'), 'fromDate'=>request('fromDate'), 'toDate'=>request('toDate')]) !!}',
                columns: [
                    { data: 'MaChamCong', name: 'MaChamCong' },
                    { data: 'username', name: 'username', sortable:false, searchable: false },
                    { data: 'NgayCham', name: 'NgayCham' },
                    { data: 'GioCham', name: 'GioCham' },
                    { data: 'machinename', name: 'machinename', sortable:false, searchable: false}
                ]
            });
            $("#fromDate").datepicker({format: "dd/mm/yyyy"});
            $("#toDate").datepicker({format: "dd/mm/yyyy"});
        });
    </script>

@endsection
