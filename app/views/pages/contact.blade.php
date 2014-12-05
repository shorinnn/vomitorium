@extends($layout)

@section('content')
 <!-- Page Content -->

    <div class="container">
      <div class="row">

        <div class="col-sm-12">
          <h3>Let's Get In Touch!</h3>
          <p>Lid est laborum dolo rumes fugats untras. Etharums ser quidem rerum facilis dolores nemis omnis fugats vitaes nemo minima rerums unsers sadips amets. Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.</p>
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