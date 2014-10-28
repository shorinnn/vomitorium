<?php

class ReportsController extends BaseController {

	public function index()	{
            $meta['javascripts'] = array('../assets/js/admin/lessons.js','../assets/js/admin/reports.js');
            $meta['header_img_text'] = 'Reports';
            $reports = Report::all();
            return View::make('reports.index')->withMeta($meta)->withReports($reports);
	}
        
        public function create(){
            $template = file_get_contents(Input::file('template')->getRealPath());
            $logo = Input::hasFile('logo') ? '<img src="data:image/'.Input::file('logo')->getClientOriginalExtension().';base64,'.base64_encode(file_get_contents(Input::file('logo')->getRealPath())).'" />' : '';
            //$content = parse_tags($template, $logo, Input::get('title'));
            $content = $template;
            $report = new Report;
            $report->title =  Input::get('title');
            $report->content = $content;
            $report->slug =  Str::slug($report->title);
            if($report->save()){
                $response['success'] = 1;
                $response['url'] = url('reports/'.$report->slug);
                //$response['report'] = $report;
                $response['html'] = "<tr class='list-row list-row-$report->id'><td>$report->title</td><td><a href='".url('reports/'.$report->slug)."'
                    target='_blank'>View Report</a></td>
                   <td><button onclick='del(".$report->id.",\"".action("ReportsController@destroy", array($report->id))."\")' class='btn btn-danger'>Delete</button></td></tr>";
            }
            else{
                $response['success'] = 0;
                $response['error'] = 'An error occurred';
            }
            
            return json_encode($response);
        }
        
        public function destroy($id){
            $r = Report::find($id);
            if($r->delete()){
                $response['status'] = 'success';
                $response['text'] = 'Report deleted';
            }
            else{
                $response['status'] = 'danger';
                $response['text'] = 'An error occurred';
            }
            return json_encode($response);
        }
	

}
