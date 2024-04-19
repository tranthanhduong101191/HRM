@extends(theme(TRUE).'.layout')

@section('title')
    Cài đặt ca
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">Cài đặt ca</h3>

                </div>
                <form class="form-horizontal" method="post">
                    {{csrf_field()}}
                    <input type="text" class="form-control hidden" name="id" value="{{$id}}"/>
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th scope="col">Ca</th>
                            <th scope="col">Vào 1</th>
                            <th scope="col">Ra 1</th>
                            <th scope="col">Vào 2</th>
                            <th scope="col">Ra 2</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $item)
                            <tr>
                                <td><input type="text" class="form-control" name="cadaluu[{{$item->id}}]" value="{{$item->ca}}"/></td>
                                <td><input type="text" class="form-control" name="vao1daluu[{{$item->id}}]" value="{{$item->vao1}}"/></td>
                                <td><input type="text" class="form-control" name="ra1daluu[{{$item->id}}]" value="{{$item->ra1}}"/></td>
                                <td><input type="text" class="form-control" name="vao2daluu[{{$item->id}}]" value="{{$item->vao2}}"/></td>
                                <td><input type="text" class="form-control" name="ra2daluu[{{$item->id}}]" value="{{$item->ra2}}"/></td>
                            </tr>
                        @endforeach
                            <tr>
                                <td><input type="text" class="form-control" name="ca[]"/></td>
                                <td><input type="text" class="form-control" name="vao1[]"/></td>
                                <td><input type="text" class="form-control" name="ra1[]"/></td>
                                <td><input type="text" class="form-control" name="vao2[]"/></td>
                                <td><input type="text" class="form-control" name="ra2[]"/></td>
                            </tr>
                        </tbody>
                    </table>
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

        });
    </script>
@endsection