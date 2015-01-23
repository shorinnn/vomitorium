<tr class='list-domain-{{$domain->id}}'><td>
        <a id="domain" class='editable' data-type="text" data-pk="{{$domain->id}}" data-name="domain" 
           data-url="{{url("accounts/external_domains/$domain->id")}}" data-mode="inline">{{$domain->domain}}
        </a>
    </td>
    <td>
        <button onclick='ajax_link("{{ action('AccountsController@destroy_external_domain', array('id' => $domain->id)) }}",
                            "Delete Domain? WARNING: CANNOT UNDO ACTION",
                            "del(\"domain-{{$domain->id}}\")", "DELETE")' class='btn btn-danger btn-xs' data-tooltip='1' title='Delete'>
            <i class='glyphicon glyphicon-trash'></i>
        </button>
    </td>
</tr>
