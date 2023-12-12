<div class="form-group">
    <label for="customer">Select Payment<span class="required"> *</span></label>
    {!! Form::select('payment_type_id', $paymentTypes, null, [
        'id' => 'payment_type_id',
        'class' => 'form-control select2',
        'required',
        'placeholder' => 'Select One',
    ]) !!}
</div>
<span>&nbsp;</span>
<input type="text" name="bank_name" id="bank_name" class="form-control" placeholder="Bank Name">
<span>&nbsp;</span>
<input type="text" name="cheque_number" id="cheque_number" class="form-control" placeholder="Cheque Number">
<span>&nbsp;</span>
<input type="text" name="transaction_number" id="transaction_number" class="form-control" placeholder="Transaction Number">
<input type="text" name="note" id="note" class="form-control" placeholder="Note">
<span>&nbsp;</span>
<input type="text" name="cheque_date" id="cheque_date" class="datepicker form-control" placeholder="Issue Deposit Date ">
