<div class="modal fade" tabindex="-1" role="dialog" id="tinhlailuongtheothangModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Vui lòng chọn ngày tháng</h4>
            </div>
            <div class="modal-body">
                <form class="" method="post" action="{{route('tinhlailuongtheothang')}}">
                    {{csrf_field()}}
                    <div class="form-group">
                        <label for="exampleInputEmail2">Chọn</label>
                        <input type="text" class="form-control" id="fromDate1" name="fromDate1" placeholder="ngày" value="{{request('fromDate1')}}" autocomplete="off" required/>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-danger">Tính lại</button>
                    </div>
                </form>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
