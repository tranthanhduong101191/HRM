@extends(theme(TRUE).'.layout')

@section('title')
    Sửa nội quy
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">Sửa nội quy</h3>

                    <div class="box-tools pull-right">
                        {!! a('config/noiquy', '', '<i class="fa fa-arrow-left"></i> '.trans('system.back'), ['class'=>'btn btn-sm btn-success'],'')  !!}
                    </div>
                </div>
                <form class="form-horizontal" method="post">
                    {{csrf_field()}}
                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Tiêu đề</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="title" value="{{old('name', $data->title)}}" placeholder="Nhập tên tiêu đề nội quy" required/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Nội dung</label>

                            <div class="col-sm-10">
                                <textarea type="text" class="form-control" name="description" placeholder="Nhập nội dung" required rows="5" cols="20">{{old('name', $data->description)}}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="reset" class="btn btn-default">Nhập lại</button>
                        <button type="submit" class="btn btn-info pull-right">{{trans('system.submit')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
