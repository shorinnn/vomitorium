<form method="POST" action="{{{ URL::to("users/change_password") }}}" accept-charset="UTF-8" id="change_password_form">
    <fieldset>
        <div class="form-group">
            <label for="password">Password</label>
            <input class="form-control" tabindex="1" placeholder="password" type="password" name="password" id="password" required>
        </div>
        <div class="form-group">
        <label for="confirm_password">
            Confirm Password
        </label>
        <input class="form-control" tabindex="2" placeholder="Confirm Password" type="password" name="password_confirmation" id="password_confirmation" 
               required data-bv-identical="true"
                data-bv-identical-field="password"
                data-bv-identical-message="The password and its confirm are not the same">
        </div>
        <div class="form-group">
            <button tabindex="3" type="submit" class="btn btn-primary">Change Password</button>
            <input type="hidden" name="id" value="{{$id}}" />
        </div>
    </fieldset>
</form>
