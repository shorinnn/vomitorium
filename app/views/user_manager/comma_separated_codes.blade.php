@extends($layout)

@section('content')

<input type='text' class='form-control selectable-txt pull-left copy-to copy-source-1'  data-id='1'
       value='{{ $codes }}' data-clipboard-text="{{$codes}}" style="opacity: 0; height:1px"/>
<button class='btn btn-default copy-to' data-clipboard-text="{{ $codes }}"  data-id='1'>Copy Codes</button>
<br />
<textarea class="form-control  selectable-txt copy-to copy-source-1" 
          rows="15" data-id='1' value='{{ $codes }}' data-clipboard-text="{{$codes}}">{{ $codes }}</textarea>
@stop