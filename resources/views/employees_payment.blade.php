@extends('voyager::master')
@section('content')
<h1></h1>
<div class="container">
<div class="row">
  <h3 class="text-center">Order Need Pay</h3>
<div class="col-md-12">
<table class="table table-bordered data-table">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Client Phone</th>
                        <th>Employee</th>
                        <th>Employee Phone</th>
                        <th>Order Date</th>
                        <th>Price</th>
                        <th>Create Date</th>
                        <th>Action</th>
                </thead>
                <tbody>
                </tbody>
            </table>
</div>
<h3 class="text-center">Total Order Pay</h3>
<div class="col-md-12">
<table class="table table-bordered data-table1">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Admin</th>
                        <th>Total</th>
                </thead>
                <tbody>
                </tbody>
            </table>
  </div>
 </div>
</div>
<script src="{{ asset('js/jquery.js') }}"></script>
<script src="{{ asset('js/jquery.validate.js') }}"></script>
<script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>

<script type="text/javascript">
$(function () {
var table = $('.data-table').DataTable({
    ajax: "{{ route('employees_payment') }}",
    columns: [
        {data: 'name_client', name: 'name_client'},
        {data: 'phone_client', name: 'phone_client'},
        {data: 'name_employee', name: 'name_employee'},
        {data: 'phone_employee', name: 'phone_employee'},
        {data: 'date', name: 'date'},
        {data: 'payment', name: 'payment'},
        {data: 'created_at', name: 'created_at'},
        {data: 'action', name: 'action'},
    ]
});
var table1 = $('.data-table1').DataTable({
    ajax: "{{ route('employees_paymented') }}",
    columns: [
        {data: 'name', name: 'name'},
        {data: 'admin', name: 'admin'},
        {data: 'total', name: 'total'},
    ]
});
   });
   
   $('body').on('click', '.pay', function () {
      $(this).attr('disabled', true);      
      var Item_id = $(this).data('id');
      $.get("{{ route('employees_pay') }}/"+Item_id ).done(function() {
        $('#'+Item_id).attr('disabled', false); 
        $('#'+Item_id).css("background-color", "#2ecc71").text("Done"); 
        $('.data-table1').DataTable().ajax.reload();

});
   });
</script>
@endsection