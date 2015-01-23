<form id='payment-plan-form'>
    <div class="well">
        <input type="text" class="form-control" id="plan-name" name="name" placeholder="Name" />
        <br />
        <select class="form-control" id="plan-type" name="type" onchange="plan_type(event)">
            <option value="subscription">Subscription</option>
            <option value="one-time">One-time</option>
        </select><br />
        <select class="form-control" id="allows_group_conversations" name="allows_group_conversations">
            <option value="1">Allows Group Conversations</option>
            <option value="0">Doesn't Allow Group Conversations</option>
        </select><br />
        <select class="form-control" id="allows_coach_conversations" name="allows_coach_conversations">
            <option value="1">Allows Coach Conversations</option>
            <option value="0">Doesn't Allow Coach Conversations</option>
        </select><br />
        <select class="form-control" id='currency' name='currency'>
                            <option value='USD'>$ USD</option>
                            <option value='GBP'>&pound; GBP</option>
                            <option value='EUR'>&euro; EUR</option>
                            <option value='AUD'>AUD</option>
                            <option value='CAD'>CAD</option>
                            <option value='SGD'>SGD</option>
                        </select>
        <div class="subscription plan-type">
            <div class="col-lg-4">
                <div class="input-group">
                    <span class="input-group-addon">Cost</span>
                    <input type="text" class="form-control" placeholder="99.99" id="subscription-cost" name="subscription_cost">
                </div>
            </div>
            <div class="col-lg-1 text-right" style="padding-right: 0px">
                Every 
            </div>
            <div class="col-lg-2">
                <input type="text" class="form-control" id="subscription-duration" name="subscription_duration" placeholder='3' />
            </div>
            <div class="col-lg-5">

                <select class="form-control" id='subscription-unit' name='subscription_duration_unit'>
                    <option value="months">Month(s)</option>
                    <option value="days">Day(s)</option>
                </select>
            </div>
        </div>
        <div class="one-time nodisplay plan-type">
            <div class="col-lg-4"></div>
            <div class="col-lg-4">
                <div class="input-group">
                    <span class="input-group-addon">
                        Cost
                    </span>
                    <input type="text" class="form-control" id='plan-cost' name='cost' placeholder="99.99">
                </div>
            </div>
            <div class="col-lg-4"></div>
        </div>
        <div class='clearfix'></div>
        <div class="text-center trial">
            <a href="#" class="btn btn-link" onclick="add_trial(event)">Add Trial Period</a>
            <div class="nodisplay">
                <div class="col-lg-4">
                    <div class="input-group">
                        <span class="input-group-addon">Cost</span>
                        <input type="text" class="form-control" placeholder="1.00" id="trial-cost" name="trial_cost">
                    </div>
                </div>
                <div class="col-lg-1 text-right" style="padding-right: 0px">
                    For 
                </div>
                <div class="col-lg-2">
                    <input type="text" class="form-control" id='trial-duration' name='trial_duration' placeholder="3" />
                </div>
                <div class="col-lg-5">

                    <select class="form-control" id='trial-unit' name='trial_duration_unit'>
                        <option value="months">Month(s)</option>
                        <option value="days">Day(s)</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="clearfix"><br /></div>
        <button type='button' class='btn btn-default' onclick='create_payment_plan()'>Create Payment Plan</button>
    </div>
</form>