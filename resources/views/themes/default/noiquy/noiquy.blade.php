@extends(theme(TRUE).'.layout')

@section('title')
    Danh sách nội quy
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-danger">
            <div class="box box-danger">
                <!-- <div class="box-header with-border">
                    <h3 class="box-title">Nội Quy</h3>

                    <div class="box-tools pull-right">
                        {!! a('config/noiquy/create', '', '<i class="fa fa-plus"></i> '.trans('system.add'), ['class'=>'btn btn-sm btn-primary'],'')  !!}
                    </div>
                </div> -->
                <!-- <iframe
    src="https://drive.google.com/viewer?embedded=true&url=http://chamcong.fastup.vn/uploads/noiquy.pdf#toolbar=0&scrollbar=0"
    frameBorder="0"
    scrolling="auto"
    width="100%" style="height:90vh"
></iframe> -->
<iframe src='https://view.officeapps.live.com/op/embed.aspx?src=http://nhansu.choxanh.com/uploads/noiquychoxanh.docx' width="100%" style="height:90vh" frameborder='0'>
    </iframe>
                <!-- <object type="application/pdf" data="http://chamcong.fastup.vn/uploads/noiquy.pdf?#zoom=85&scrollbar=0&toolbar=0&navpanes=0" width="100%" style="height:90vh"></object> -->
                <!-- <embed src="https://drive.google.com/viewerng/viewer?embedded=true&url=http://chamcong.fastup.vn/uploads/noiquy.pdf" width="100%" style="height:90vh"> -->
            <!-- <embed src="http://chamcong.fastup.vn/uploads/noiquy.pdf" width="100%" style="height:90vh" type="application/pdf"> -->
            </div>
        </div>
    </div>
@endsection