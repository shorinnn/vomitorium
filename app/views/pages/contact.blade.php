@extends($layout)

@section('content')
 <!-- Page Content -->

    <div class="container">
      <div class="row">

        <div class="col-sm-12">
          
@if( trim(sys_settings('support_html')) != '')
    {{ sys_settings('support_html') }}
@endif
            <form role="form" method="POST" action="{{url('/contact-us')}}" id="contact_form">    
	            <div class="row">
	              <div class="clearfix"></div>
	              <div class="form-group col-lg-12">
	                <label for="input4">Message</label>
	                <textarea name="contact_message" class="form-control" rows="6" id="input4" required></textarea>
	              </div>
	              <div class="form-group col-lg-12">
	                <button type="submit" class="btn btn-default">Submit Ticket</button>
	              </div>
              </div>  
            </form>
        </div>


      </div><!-- /.row -->

    </div><!-- /.container -->
@stop