<table class="table table-bordered">
    <thead>
    <tr>
        <th rowspan="2" colspan="2" style="background-color: yellow; border: 1px solid #000000">Tháng {{$thang}}</th>
        @for($k = 1; $k<=$days; $k++)
            <th style="background-color: yellow; border: 1px solid #000000">{{$k}}</th>
        @endfor
        <th rowspan="2" style="background-color: yellow; border: 1px solid #000000">Tổng</th>
        <th rowspan="2" style="background-color: yellow; border: 1px solid #000000">Bộ phận</th>
        <th rowspan="2" style="background-color: yellow; border: 1px solid #000000">Loại</th>
    </tr>
    <tr>
        @for($k = 1; $k<=$days; $k++)
            <th scope="col" @if($dayofweek==6 || $dayofweek==7 || $dayofweek==0) style="background-color: red; border: 1px solid #000000" @else style="background-color: yellow; border: 1px solid #000000" @endif>{{($dayofweek<7 && $dayofweek>0)?('T'.($dayofweek+1)):'CN'}}</th>
            @php
                $dayofweek++;
                if($dayofweek>7)
                    $dayofweek = 1;
            @endphp
        @endfor
    </tr>
    </thead>
    <tbody>
    @foreach($data as $item)
        @php
            $tongcong   =   0;
        @endphp
        <tr>
            <td scope="row" style="border: 1px solid #000000">{{$item->uid}}</td>
            <td style="width: 40px; border: 1px solid #000000">{{$item->name}}</td>
            @for($k = 1; $k<=$days; $k++)
                <td style="border: 1px solid #000000; width: 5px">{{!empty($item->lich[$k])?$item->lich[$k]->cong:''}}</td>
                <?php if(!empty($item->lich[$k])) $tongcong += $item->lich[$k]->cong; ?>
            @endfor
            <td style="border: 1px solid #000000">{{$tongcong}}</td>
            <td style="border: 1px solid #000000">{{$item->branch->name}}</td>
            <td style="border: 1px solid #000000">{{($item->partime == 1)?'Partime':'Chính thức'}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
<style type="text/css">
    td th {
        border: 1px solid #000000;
    }
</style>
