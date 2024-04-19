<div class="modal fade" tabindex="-1" role="dialog" id="suacongModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Thêm giải trình công</h4>
            </div>
            <div class="modal-body">
                <form class="" method="post" action="{{route('updateCong')}}">
                    {{csrf_field()}}
                    <input type="hidden" name="chamcong_id" id="suacong_chamcong_id" />
                    <div class="form-group">
                        <label>Công mới</label>
                        <input type="number" name="congmoi" id="suacong_congmoi" step="any" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Lý do sửa</label>
                        <textarea name="reason" class="form-control" ></textarea>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Cập nhật công</button>
                    </div>
                </form>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
