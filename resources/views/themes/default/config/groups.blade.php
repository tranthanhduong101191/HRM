@extends(theme(TRUE).'.layout')

@section('title')
    {{trans('page.rolelist')}}
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">{{trans('page.rolelist')}}</h3>

                    <div class="box-tools pull-right">
                        {!! a('config/groups/create', '', '<i class="fa fa-plus"></i> '.trans('system.add'), ['class'=>'btn btn-sm btn-primary'],'')  !!}
                    </div>
                </div>
                <div class="box-body">
                    <table class="table table-bordered" id="datatable">
                        <thead>
                        <tr>
                            <th>Id</th>
                            <th>Tên</th>
                            <th style="width: 100px">Công chuẩn</th>
                            <th>Số thành viên</th>
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
                ajax: '{!! asset('config/groups/data') !!}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    { data: 'congchuan', name: 'congchuan' },
                    { data: 'count', name: 'count' , sortable:false, searchable: false },
                    { data: 'manage', name: 'manage'  , sortable:false, searchable: false}
                ]
            });
        });
    </script>
@endsection