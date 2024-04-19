@extends(theme(TRUE).'.layout')

@section('title')
    {{trans('page.createuser')}}
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">{{trans('page.createuser')}}</h3>

                    <div class="box-tools pull-right">
                        {!! a('config/users', '', '<i class="fa fa-arrow-left"></i> '.trans('system.back'), ['class'=>'btn btn-sm btn-success'],'')  !!}
                    </div>
                </div>
                <form class="form-horizontal" method="post">
                    {{csrf_field()}}
                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Mã nv</label>

                            <div class="col-sm-4">
                                <input class="form-control" value="{{\App\User::orderBy('id','desc')->first()->id + 1}}" name="uid"/>
                            </div>

                            <label class="col-sm-2 control-label">Lương cứng</label>

                            <div class="col-sm-4">
                                <input type="number" class="form-control" name="luongcung" placeholder="Lương cứng" autocomplete="off" />
                            </div>

                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">{{trans('users.name')}}</label>

                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="name" placeholder="{{trans('users.nameplacehold')}}" />
                            </div>

                            <label class="col-sm-2 control-label">Ngày sinh</label>

                            <div class="col-sm-4">
                                <input type="text" class="form-control datepicker" name="birthday" placeholder="Ngày sinh" autocomplete="off"/>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">CMND</label>

                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="cmnd" placeholder="CMND" />
                            </div>

                            <label class="col-sm-2 control-label">Địa chỉ</label>

                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="address" placeholder="Địa chỉ" />
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">{{trans('users.password')}}</label>

                            <div class="col-sm-4">
                                <input type="password" class="form-control" name="password" placeholder="{{trans('users.passwordplacehold')}}" />
                            </div>
                            <label class="col-sm-2 control-label">Số điện thoại</label>

                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="phone" placeholder="Số điện thoại" />
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">{{trans('users.group')}}</label>

                            <div class="col-sm-4">
                                <select class="form-control" name="group_id">
                                    @foreach(\App\Group::get() as $gr)
                                        <option value="{{$gr->id}}">{{$gr->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <label class="col-sm-2 control-label">{{trans('users.branch')}}</label>

                            <div class="col-sm-4">
                                <select class="form-control" name="branch_id">
                                    @foreach(\App\Branch::get() as $br)
                                        <option value="{{$br->id}}">{{$br->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <div class="col-sm-6">
                                <input type="checkbox" name="parttime" value="1" /> Nhân viên bán thời gian
                            </div>

                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button type="reset" class="btn btn-default">{{trans('system.cancel')}}</button>
                        <button type="submit" class="btn btn-info pull-right">{{trans('system.submit')}}</button>
                    </div>
                    <!-- /.box-footer -->
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(function() {
            $('.datepicker').datetimepicker({
                format: "d/m/YYYY",
                maxDate: 'now'
            });
        });
    </script>
@endsection
