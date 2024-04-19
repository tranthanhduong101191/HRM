@extends(theme(TRUE).'.layout')

@section('title')
    {{trans('page.userlist')}}
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">{{trans('page.userlist')}}</h3>

                    <div class="box-tools pull-right">
                        {!! a('config/deleted-users', '', ' Danh sách nhân viên nghỉ ', ['class'=>'btn btn-sm btn-info'],'')  !!}
                        {!! a('config/users/create', '', '<i class="fa fa-plus"></i> '.trans('system.add'), ['class'=>'btn btn-sm btn-primary'],'')  !!}
                    </div>
                </div>
                <div class="box-body">
                    <table class="table table-bordered" id="datatable">
                        <thead>
                        <tr>
                            <th style="width: 100px">Mã chấm công</th>
                            <th>Tên</th>
                            <th style="width: 130px">Lương cứng (VNĐ)</th>
                            <th>Email</th>
                            <th>Nhóm</th>
                            <th>Chi nhánh</th>
                            <th>Quản lý</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(function() {
            $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{!! asset('config/users/data') !!}',
                columns: [
                    { data: 'uid', name: 'uid' },
                    { data: 'name', name: 'name' },
                    { data: 'luongcung', name: 'luongcung' },
                    { data: 'email', name: 'email' },
                    { data: 'group', name: 'group.name' },
                    { data: 'branch', name: 'branch.name' },
                    { data: 'manage', name: 'manage'  , sortable:false, searchable: false}
                ]
            });
        });
    </script>
@endsection