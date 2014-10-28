<tr class='list-row list-row-{{$announcement->id}}'>
    <td>
        <a class="editable" href="#" id="content" data-type="textarea" data-pk="{{$announcement->id}}" 
           data-name="content" data-url="{{url('announcements/update')}}" data-original-title="Enter content" data-mode='inline'>
            {{$announcement->content}}
        </a>
    </td>
    <td>
        <a class="editable" href="#" id="start_date" data-type="combodate" data-pk="{{$announcement->id}}" 
           data-value="{{date('m/d/Y', strtotime($announcement->start_date))}}"
           data-format="MM/DD/YYYY" data-viewformat="MM/DD/YYYY" data-template="MMM / DD / YYYY"
           data-name="start_date" data-url="{{url('announcements/update')}}" data-original-title="Enter start date" data-mode='popup'>
            {{date('m/d/Y', strtotime($announcement->start_date))}}
        </a>
    </td>
    <td>
        <a class="editable" href="#" id="end_date" data-type="combodate" data-pk="{{$announcement->id}}" 
           data-value="{{date('m/d/Y', strtotime($announcement->end_date))}}"
           data-format="MM/DD/YYYY" data-viewformat="MM/DD/YYYY" data-template="MMM / DD / YYYY"
           data-name="end_date" data-url="{{url('announcements/update')}}" data-original-title="Enter start date" data-mode='popup'>
            {{date('m/d/Y', strtotime($announcement->end_date))}}
        </a>
    </td>
    <td>
        <a class="editable" href="#" id="published" data-type="select" data-pk="{{$announcement->id}}"  data-value="{{$announcement->published}}"
           data-source="[{0:'Inactive'},{1:'Active'}]"
           data-name="published" data-url="{{url('announcements/update')}}" data-original-title="Enter content" data-mode='inline'>
            @if($announcement->published==1)
                Active
            @else
                Inactive
            @endif
        </a>
    </td>
    <td>
        <button data-toggle='tooltip' title='Delete Announcement'  class='do-tooltip btn btn-danger btn-warning delete-btn' 
        data-target='list-row' data-id='{{$announcement->id}}' data-url= "{{url('announcements/'.$announcement->id)}}">
                <i class='glyphicon glyphicon-trash'></i>
          </button>
    </td>
</tr>