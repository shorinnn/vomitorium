<?php

class PaymentPlansController extends BaseController {

    public function __construct(){
        $this->beforeFilter('admin');
    }
    
    public function index(){
        $processors = PaymentProcessor::where('program_id',Session::get('program_id'))->get();
        $plans = PaymentPlan::where('program_id', Session::get('program_id'))->get();
        $meta['header_img_text'] = 'Payment Plans';
        return View::make('payment_plans.index')->withProcessors($processors)->withPlans($plans)->withMeta($meta);
    }
    
    public function processor(){
        $processor = new PaymentProcessor();
        $processor->name = Input::get('name');
        $processor->field = Input::get('val');
        $processor->field2 = Input::get('val2');
        $processor->program_id = Session::get('program_id');
        if($processor->save()){
            $return['status'] = 'success';
            $return['text'] = 'Payment processor saved';
            $return['html'] = View::make('payment_plans.processor')->withP($processor)->render();
        }
        else{
            $return['status'] = 'danger';
            $return['text'] = 'Could not save payment processor:<br />'.  format_validation_errors($processor->errors()->all());
        }
        return json_encode($return);
    }
    
    public function processors(){
        $processors = PaymentProcessor::where('program_id',Session::get('program_id'))->get();
        $meta['header_img_text'] = 'Payment Processors';
        return View::make('payment_plans.processors')->withProcessors($processors)->withMeta($meta);
    }
    
    public function update_processor(){
        $p = PaymentProcessor::find(Input::get('pk'));
        $field = Input::get('name');
        $p->$field = Input::get('value');
        $p->save();
    }
    public function delete_processor($id){
        PaymentProcessor::find($id)->delete();
        return json_encode(array('status'=>'success','text'=>'Processor deleted'));
        
    }
    
    public function update(){
        $p = Program::find(Input::get('pk'));
        $field = Input::get('name');
        $p->$field = Input::get('value');
        if(!$p->updateUniques()){
            return Response::make(format_validation_errors($p->errors()->all()), 400);
        }
    }
    public function store(){
        $p = new PaymentPlan(Input::all());
        $p->program_id = Session::get('program_id');
        $p->cost = $p->type=='one-time' ? $p->cost : Input::get('subscription_cost');
        if($p->save()){
            $return['status'] = 'success';
            $return['text'] = 'Payment plan saved';
            $return['html'] = View::make('payment_plans.plan')->withPlan($p)
                    ->withProcessor(PaymentProcessor::where('program_id', Session::get('program_id'))->first())
                    ->render();
        }
        else{
            $return['status'] = 'danger';
            $return['text'] = 'Could not save payment plan:<br />'.  format_validation_errors($p->errors()->all());
        }
        return json_encode($return);
    }
    
    function destroy($id){
        $program = Program::find($id);
        $program->delete();
        if($id==Session::get('program_id')) Session::forget('program_id');
        return json_encode(array('status' => 'success', 'text' => 'Program Deleted'));
    }
   
    
    

}
