@if($a->status=='active')
    <tr class='list list-{{$a->id}}'><td>{{$a->installation}}</td>
@else
    <tr class='suspended list list-{{$a->id}}'><td>{{$a->installation}}</td>
@endif
            <td>
                <a href='http://{{$a->subdomain}}.{{Config::get('app.base_url')}}' target='_blank'>{{$a->subdomain}}.{{Config::get('app.base_url')}}</a>
            </td>
            <td>{{$a->db_name}}</td>
            <td>{{$a->db_username}}</td><td>{{$a->db_pass}}</td>
            <td>
                <div  style='width:135px'>
                @if($a->status=='active')
                <button onclick='ajax_link("{{ action('AccountsController@status', array('id' => $a->id, 'status'=>'inactive')) }}",
                                "Suspend Account?",
                                "suspend({{$a->id}})")' class='btn btn-danger suspend btn-xs' data-tooltip='1' title='Suspend'>
                    <i class='glyphicon glyphicon-remove-circle'></i>
                </button>
                <button onclick='ajax_link("{{ action('AccountsController@status', array('id' => $a->id, 'status'=>'active')) }}",
                                "Activate Account?",
                                "activate({{$a->id}})")' class='btn btn-success activate nodisplay  btn-xs'  data-tooltip='1' title='Activate'>
                    <i class='glyphicon glyphicon-ok-circle'></i>
                </button>
                @else
                <button onclick='ajax_link("{{ action('AccountsController@status', array('id' => $a->id, 'status'=>'inactive')) }}",
                                "Suspend Account?",
                                "suspend({{$a->id}})")' class='btn btn-danger suspend nodisplay btn-xs'  data-tooltip='1' title='Suspend'>
                    <i class='glyphicon glyphicon-remove-circle'></i>
                </button>
                <button onclick='ajax_link("{{ action('AccountsController@status', array('id' => $a->id, 'status'=>'active')) }}",
                                "Activate Account?",
                                "activate({{$a->id}})")' class='btn btn-success activate btn-xs'  data-tooltip='1' title='Activate'>
                    <i class='glyphicon glyphicon-ok-circle'></i>
                </button>
                @endif
                
                <button onclick='ajax_link("{{ route('accounts.destroy', array('id' => $a->id)) }}",
                                "Delete Account? WARNING: CANNOT UNDO ACTION",
                                "del({{$a->id}})","DELETE")' class='btn btn-danger btn-xs' data-tooltip='1' title='Delete'>
                    <i class='glyphicon glyphicon-trash'></i>
                </button>
                                
                <button onclick='admin_info({{$a->id}})' title='Admin Info' class='btn btn-primary btn-xs' data-tooltip='1'>
                    <i class='glyphicon glyphicon-user'></i>
                </button>
                
                <button onclick='external_domains({{$a->id}})' title='External Domains' class='btn btn-primary btn-xs' data-tooltip='1'>
                    <i class='glyphicon glyphicon-retweet'></i>
                </button>
                                
                </div>       
            </td></tr>