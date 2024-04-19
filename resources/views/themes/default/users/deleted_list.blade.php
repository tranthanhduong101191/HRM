@extends(theme(TRUE).'.layout')

@section('title')
    Nhân viên đã nghỉ
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">Nhân viên đã nghỉ</h3>
                </div>
                <div class="box-body">
                    <table class="table table-bordered" id="datatable">
                        <thead>
                        <tr>
                            <th>Id</th>
                            <th>Tên</th>
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
                ajax: '{!! asset('config/users/deleted_data') !!}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'group', name: 'group.name' },
                    { data: 'branch', name: 'branch.name' },
                    { data: 'manage', name: 'manage'  , sortable:false, searchable: false}
                ]
            });
        });
    </script>
@endsection