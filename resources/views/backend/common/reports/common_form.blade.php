<div class="col-2">
    <div class="form-group">
        <label>Select Store:*</label>
        <select class="form-control" name="store_id" id="store_id" autofocus>
            <option value="All">All Store</option>
            @if(count($stores))
                @foreach($stores as $store)
                    <option value="{{$store->id}}">{{$store->name}}</option>
                @endforeach
            @endif
        </select>
    </div>
</div>
<div class="col-2">
    <div class="form-group">
        <label>Start Date:*</label>
        {!! Form::date('start_date', date('Y-m-d'), ['class' => 'form-control', 'id' => 'myDatepicker', 'required']) !!}
    </div>
</div>
<div class="col-2">
    <div class="form-group">
        <label>End Date:*</label>
        {!! Form::date('end_date', date('Y-m-d'), ['class' => 'form-control', 'id' => 'myDatepicker', 'required']) !!}
    </div>
</div>
<div class="col-2 d-none">
    <label for="previewtype">
        <input type="radio" name="previewtype" value="htmlview" id="previewtype">
        Normal</label>
    <label for="pdfprintview">
        <input type="radio" name="previewtype" value="pdfview" checked id="printview"> Pdf
    </label>
</div>
