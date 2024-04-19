@extends(theme(TRUE).'.layout')

@section('title')
    Thay đổi mật khẩu
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">Thay đổi mật khẩu</h3>

                    <div class="box-tools pull-right">
                        {!! a('config/users', '', '<i class="fa fa-arrow-left"></i> '.trans('system.back'), ['class'=>'btn btn-sm btn-success'],'')  !!}
                    </div>
                </div>
                <form class="form-horizontal" method="post">
                    {{csrf_field()}}
                        <div class="box-body">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Mật khẩu cũ</label>

                                <div class="col-sm-9">
                                    <input type="password" class="form-control" name="old_password" placeholder="Mật khẩu cũ" />
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Mật khẩu mới</label>

                                <div class="col-sm-9">
                                    <input type="password" class="form-control" name="new_password" placeholder="Mật khẩu mới" minlength="6"/>
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Xác nhận mật khẩu mới</label>

                                <div class="col-sm-9">
                                    <input type="password" class="form-control" name="confirm_password" placeholder="Xác nhận mật khẩu mới" minlength="6"/>
                                </div>
                            </div>
                        </div>

                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button type="reset" class="btn btn-default">{{trans('system.cancel')}}</button>
                        <button type="submit" name="submitbutton" value="1" class="btn btn-info pull-right">{{trans('system.submit')}}</button>
                        {{--<button type="submit" name="submitbutton" value="1" class="btn btn-info pull-right">Lưu và thoát</button>--}}
                    </div>
                    <!-- /.box-footer -->
                </form>
            </div>
        </div>
    </div>
    <link rel="stylesheet" href="{{asset('plugins/jquery.tokenInput/token-input.css')}}" />

    <style type="text/css">
        li.token-input-token {
            max-width: 100% !important;
        }
    </style>
@endsection

@section('js')
    <script src="{{asset('plugins/moment-develop/moment.js')}}"></script>
    <script src="{{asset('plugins/bootstrap-datetimepicker-master/build/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script src="{{asset('plugins/jquery.tokenInput/jquery.tokeninput.js')}}" ></script>
    <script>
        $(document).ready(function() {
            $('.datepicker').datetimepicker({format: 'YYYY-MM-DD'});
            $('.signup_date').datetimepicker({format: 'YYYY-MM-DD'});
            $('.expire_date').datetimepicker({format: 'YYYY-MM-DD'});
            $('.spouce_birthday').datetimepicker({format: 'YYYY-MM-DD'});

            $('#referer_id').tokenInput("{{asset('/referer')}}", {
                queryParam: "term",
                zindex  :   1005,
                preventDuplicates   :   true,
                limitToken: 1
            });
        });
    </script>
@endsection