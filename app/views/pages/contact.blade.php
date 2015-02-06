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
                        <div class="control-group form-group col-lg-4">
                            <label for="input1">Name</label>
                            <input type="text" name="contact_name" class="form-control" id="input1" required 
                                   data-validation-required-message="Required">
                        </div>
                        <div class="form-group col-lg-4">
                            <label for="input2">Email Address</label>
                            <input type="email" name="contact_email" class="form-control" id="input2" required>
                        </div>
                        <div class="form-group col-lg-4">
                            <label for="input3">Subject</label>
                            <input type="phone" name="contact_subject" class="form-control" id="input3" required>
                        </div>

	              <div class="clearfix"></div>
	              <div class="form-group col-lg-12">
	                <label for="input4">Message</label>
	                <textarea name="contact_message" class="form-control" rows="6" id="input4" required></textarea>
	              </div>
	              <div class="form-group col-lg-12">
	                <button type="submit" class="btn btn-default">Contact Us</button>
	              </div>
              </div>  
            </form>
        </div>


      </div><!-- /.row -->

    </div><!-- /.container -->
@stop