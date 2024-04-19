<div class="modal fade" tabindex="-1" role="dialog" id="addBonusModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Thêm thưởng - phạt</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" method="post" action="{{route('addBonus')}}">
                    {{csrf_field()}}
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Mã nhân viên</label>
                        <div class="col-sm-9">
                            <input type="number" class="form-control" step="1" placeholder="Mã nhân viên" name="uid">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Thời gian</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="issued_at" placeholder="ngày giờ" autocomplete="off">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Kiểu</label>
                        <div class="col-sm-9">
                            <select class="form-control" name="type">
                                <option value="1">Phạt</option>
                                <option value="2">Thưởng</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Số tiền</label>
                        <div class="col-sm-9">
                            <input type="number" class="form-control" name="amount" placeholder="VNĐ">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Lý do</label>
                        <div class="col-sm-9">
                            <textarea class="form-control" name="reason"></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-9">
                            <button type="submit" class="btn btn-primary">Lưu lại</button>
                        </div>
                    </div>
                </form>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
