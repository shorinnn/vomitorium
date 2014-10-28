<?php
use LaravelBook\Ardent\Ardent;

class Attachment extends Ardent {
	public static $rules = array(
            'filename' =>'required|unique:attachments'
        );
}
