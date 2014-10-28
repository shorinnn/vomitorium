             <div class="table-responsive">
            <table class="table table-bordered table-striped">
            <tdead>
                <tr><th>Category</th><th>Actions</th></tr>
            </tdead>
            <tbody>
                @foreach($cats as $c)
                 <tr class="list-row list-row-{{$c->id}}">
                     <td>
                         <a class="editable" href="#" id="category" data-type="text" data-pk="{{$c->id}}" 
                       data-name="category" data-url="{{action("CategoriesController@update", array($c->id))}}" data-original-title="Enter Category" data-mode='inline'>
                         {{$c->category}}</a></td>
                     <td><button class="btn btn-danger" onclick="del('{{$c->id}}','{{action('CategoriesController@destroy',array($c->id))}}')">Delete</button></td>
                 </tr>
                @endforeach
            </tbody>
            </table>
             </div>
            
             {{$cats->links()}}