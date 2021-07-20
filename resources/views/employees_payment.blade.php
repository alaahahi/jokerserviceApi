@extends('voyager::master')


@section('content')

<h1></h1>
<div class="container">
<div class="row">
<div class="col-md-12">
<label for="phone" class="col-form-label">Employee:</label>
            <span id="card_type">
            <select class="form-control select2-ajax select2-hidden-accessible" name="employee_id" data-get-items-route="http://localhost/jokerserviceApi/public/admin/order/relation" data-get-items-field="order_belongsto_employee_relationship" data-method="add" data-select2-id="4" tabindex="-1" aria-hidden="true">
                    
                    <option value="" data-select2-id="6">None</option>

            </select>
            </span>

</div>
<div class="col-md-12">
<table class="table table-bordered data-table">
                <thead>
                    <tr>
                        <th>name</th>
                        <th>birthdate</th>
                        <th>is_active</th>
                        <th>phone</th>
                        <th>payment</th>
                        <th>action</th>
                </thead>
                <tbody>
                </tbody>
            </table>
            </div>
</div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>  
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">

$(function () {

var table = $('.data-table').DataTable({
    ajax: "{{ route('employees_payment') }}",
    columns: [
        {data: 'name', name: 'name'},
        {data: 'birthdate', name: 'birthdate'},
        {data: 'is_active', name: 'is_active'},
        {data: 'phone', name: 'phone'},
        {data: 'payment', name: 'payment'},
        {data: 'action', name: 'action'},
    ]
});
   });
   $('body').on('click', '.pay', function () {
      $(this).attr('disabled', true);      
      var Item_id = $(this).data('id');
      $.get("{{ route('employees_pay') }}/"+Item_id ).done(function() {
        $('#'+Item_id).attr('disabled', false); 
        $('#'+Item_id).css("background-color", "#2ecc71").text("Done"); 
});
   });


</script>
  @endsection