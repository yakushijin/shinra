@extends('auth.login')

@section('addcontent')
<script type="text/javascript">
        $("#emailarea").empty();
        $("#emailarea").append('<input id="email" type="email" class="form-control topText" name="email" value="{{$email}}" required autofocus>');
    </script>
@endsection