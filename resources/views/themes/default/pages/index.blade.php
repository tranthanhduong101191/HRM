@extends(theme(TRUE).'.layout')

@section('title')
    {{trans('page.index')}}
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title">{{trans('page.index')}}</h3>

                <div class="box-tools pull-right">

                </div>
            </div>
            <div class="box-body" id="mainside">
                <h4><b>Nhân sự</b></h4>
                <a type="button" href="/" class="btn btn-default btn-md text-center"><i class="fa fa-home fa-3x"></i><br>Trang chủ</a>
                <a type="button" href="{{route('xemlichthang')}}" class="btn btn-default btn-md text-center"><i class="fa fa-calendar fa-3x"></i><br>Lịch làm việc</a>
                <a type="button" href="{{route('tonghopcong')}}" class="btn btn-default btn-md text-center"><i class="fa fa-list fa-3x"></i><br>Tổng hợp công</a>
                <a type="button" href="{{route('checkinHistory')}}" class="btn btn-default btn-md text-center"><i class="fa fa-calendar-check-o fa-3x"></i><br>Lịch sử chấm công</a>
                <a type="button" href="{{route('bonus')}}" class="btn btn-default btn-md text-center"><i class="fa fa-heart-o fa-3x"></i><br>Thưởng phạt</a>                
                <a type="button" href="{{route('noiquy')}}" class="btn btn-default btn-md text-center"><i class="fa fa-book fa-3x"></i><br>Nội quy</a>
                <hr/>
                @if(auth()->user()->branch_id < 3)
                <h4><b>Quản lý</b></h4>
                    <a type="button" href="{{route('saplichthang', ['thang'=>\Carbon\Carbon::now()->month, 'nam'=>\Carbon\Carbon::now()->year])}}" class="btn btn-default btn-md text-center"><i class="fa fa-calendar fa-3x"></i><br>Sắp lịch</a>
                    <a type="button" href="{{route('saplichSetting')}}" class="btn btn-default btn-md text-center"><i class="fa fa-clock-o fa-3x"></i><br>Cài đặt giờ làm</a>
                    <a type="button" href="{{route('tongketluong')}}" class="btn btn-default btn-md text-center"><i class="fa fa-list-ol fa-3x"></i><br>Tổng kết lương</a>
                @endif
                <hr/>
                <hr/>
                <h4><b>Order - Đặt bàn</b></h4>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function(){
    @foreach(\App\Widget::orderBy('order','ASC')->where('position','mainside')->get() as $item)
        @if(p($item->source,'post'))
            load_widget('#mainside', '{{asset($item->source)}}');
        @endif
    @endforeach
    });
	</script>
@endsection
