<div class="modal fade" tabindex="-1" role="dialog" id="giaitrinhModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Thêm giải trình công</h4>
            </div>
            <div class="modal-body">
                <form class="" method="post" action="{{route('explaint.add')}}" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <input type="hidden" name="chamcong_id" id="giaitrinh_chamcong_id" />
                    <div class="form-group">
                        <label>Nội dung giải trình</label>
                        <textarea name="content" class="form-control" ></textarea>
                    </div>
                    <div class="form-group">
                        <label>Hình ảnh</label>
                        <input type="file" name="files[]" multiple />
                        <i class="help-block">Chấp nhận file .jpg, .png và zip</i>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Gửi giải trình</button>
                    </div>
                </form>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
