@extends('voyager::master')


@section('content')

<h1></h1>
<div class="container">
<div class="row">
@foreach ($data as $customer)
<div class="col-md-4">
<div class="card" style="border-radius:15px">
<div class="card-header text-center" style="padding: 3%;">
Employees Mobile: {{ $customer->phone }}
</div>
  <div class="container">
    <h4><b>Name : {{ $customer->name }}</b></h4>
    <h5><b>Birthdate : {{ $customer->birthdate }}</b></h5>
    <h5><b>Experience : 
    @foreach ( $customer->experience as $experience)
    <span class="badge badge-secondary" style="margin: 3px;">{{ $experience->title }}</span>
      @endforeach
    </b></h5>
    <h5><b>Sex : {{ $customer->sex }}</b></h5>
    <h5><b>ID Card : {{ $customer->id_number }}</b></h5>
    <h5><b>Years Experience : {{ $customer->years_experience }}</b></h5>
    <p>Date : {{$customer->created_at}}</p>
    <p>Accepted : {{ $customer->is_active }}</p>
    <div class="card-footer text-center">
          <div class="btn-wrapper  justify-content-between">
          @if ($customer->is_active==0 && $customer->is_block==0)
          <a href="javascript:void(0)" data-toggle="tooltip"    data-id="{{$customer->id}}"  class="btn btn-danger rejection">Rejection</a>
          <a href="javascript:void(0)" data-toggle="tooltip"  id="{{$customer->id}}"  data-id="{{$customer->id}}" class="btn btn-warning approval">Approval</a>
          <a href="javascript:void(0)" data-toggle="tooltip"  data-id="{{$customer->id}}"   class="btn btn-info blocked {{$customer->id}}">Block</a>
          @endif
          @if ($customer->is_active==1 && $customer->is_block==0)
          <a href="javascript:void(0)" data-toggle="tooltip"     class="btn btn-success ">Accepted </a>
          <a href="javascript:void(0)" data-toggle="tooltip"  data-id="{{$customer->id}}"   class="btn btn-info blocked {{$customer->id}}">Block</a>
          @endif
          @if ($customer->is_blocked==1)
          <a href="javascript:void(0)" data-toggle="tooltip"  id="{{$customer->id}}"  data-id="{{$customer->id}}"   class="btn btn-danger un_block">Un Block</a>
          @endif
          </div>
    </div>
</div>
</div>
</div>
@endforeach

</div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>  
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
  $('body').on('click', '.approval', function () {
      $(this).attr('disabled', true);      
      var Item_id = $(this).data('id');
      $.get("{{ route('approval_employee') }}/"+Item_id ).done(function() {
        $('#'+Item_id).attr('disabled', false); 
        $('#'+Item_id).css("background-color", "#2ecc71").text("Done Approval");
});
   });
   $('body').on('click', '.rejection', function () {
      $(this).attr('disabled', true);      
      var Item_id = $(this).data('id');
      $.get("{{ route('block_employee') }}/"+Item_id ).done(function() {
        $('#'+Item_id).attr('disabled', false); 
        $('#'+Item_id).css("background-color", "#2ecc71").text("Done Rejection");
});
   });
   $('body').on('click', '.blocked', function () {
      $(this).attr('disabled', true);      
      var Item_id = $(this).data('id');
      $.get("{{ route('block_employee') }}/"+Item_id ).done(function() {
        $('.'+Item_id).attr('disabled', false); 
        $('.'+Item_id).css("background-color", "#2ecc71").text("Done"); 
});
   });
   $('body').on('click', '.un_block', function () {
      $(this).attr('disabled', true);      
      var Item_id = $(this).data('id');
      $.get("{{ route('un_block_employee') }}/"+Item_id ).done(function() {
        $('#'+Item_id).attr('disabled', false); 
        $('#'+Item_id).css("background-color", "#2ecc71").text("Done"); 
});
   });
</script>
  @endsection