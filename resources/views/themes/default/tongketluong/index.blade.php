@extends(theme(TRUE).'.layout')

@section('title')
    Tổng kết lương
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">Tổng kết lương</h3>

                    @if(auth()->user()->group_id <4)
                        <a class="btn btn-danger tinhlaitheongay"><i class="fa fa-refresh"></i> Tính lại lương theo tháng!</a>
                    @endif
                </div>
                <div class="box-body">
                @if(auth()->user()->group_id <4)
                    <div class="col-md-12">
                        <form class="form-inline">
                            <div class="form-group">
                                <label >Mã nhân viên</label>
                                <input type="text" class="form-control" id="user_id" placeholder="mã nhân viên" name="user_id" />
                            </div>
                            <button type="submit" class="btn btn-default"><i class="fa fa-search"></i> Tìm kiếm</button>
                        </form>
                    </div>
                    @endif
                    <div class="col-md-12">
                        <table class="table table-bordered" id="datatable">
                            <thead>
                            <tr>
                                <th>Nhân Viên</th>
                                <th>Tháng</th>
                                <th>Luơng cứng</th>
                                <th>Công thực tế</th>
                                <th>Công chuẩn</th>
                                <th>Thưởng</th>
                                <th>Phạt</th>
                                <th>Lương nhận được</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include(theme(TRUE).'.blocks.tinhlailuongtheothang')
    <link rel="stylesheet" href="{{asset('/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.standalone.min.css')}}" />
@endsection

@section('js')
    <script src="{{asset('plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
    <script>
        $("#fromDate1").datepicker({format: "dd/mm/yyyy"});
        $('.tinhlaitheongay').click(function(){
            // var id = $(this).data('id');
            // var cong = $(this).data('cong');
            // $('#suacong_chamcong_id').val(id);
            // $('#suacong_congmoi').val(cong);
            $('#tinhlailuongtheothangModal').modal('show');
        });
        $(document).ready(function(){
            $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{!! route('tongketluongData', ['user_id'=>request('user_id')]) !!}',
                columns: [
                    { data: 'user', name: 'user' , sortable:false, searchable: false},
                    { data: 'month', name: 'month' },
                    { data: 'luongcung', name: 'luongcung' },
                    { data: 'congthucte', name: 'congthucte'},
                    { data: 'congchuan', name: 'congchuan' },
                    { data: 'thuong', name: 'thuong'},
                    { data: 'phat', name: 'phat'},
                    { data: 'tongluongnhan', name: 'tongluongnhan'}
                ]
            });
        });
    </script>
@endsection
