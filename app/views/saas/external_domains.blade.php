<table class="domains-table">
   @foreach($domains as $domain)
        {{ View::make('saas.external_domain')->with( compact('domain') )}}
   @endforeach
</table><br />
<form method="post" action="{{action('AccountsController@create_external_domain', $account)}}" id="domain_form">
    <input type="text" name="domain" placeholder="http://" />
    <input type="hidden" name="account" value="{{$account}}" />
    <button class="btn btn-primary">Add Domain</button>
</form>