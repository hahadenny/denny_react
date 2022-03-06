<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Session;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Task::OrderBy('Id', 'desc')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
			'Description' => array('required', 'string', 'max:200'),
			'Priority' => array('required', 'in:Low,Medium,High,Critical'),
			'Assignee' => array('required', 'string', 'max:100'),
			'DueDate' => array('required', 'date_format:Y-m-d'),
			'Status' => array('required', 'in:Pending,In Progress,Complete')
		];
				
		$messages = [
		];
		
		$validator = Validator::make($request->all(), $rules, $messages);
		
		$validator->after(function($validator) use ($request) {	
			if ($request->DueDate < date('Y-m-d'))
				$validator->errors()->add('DueDate', 'Due Date cannot be a past date.');
			
			$otask = Task::where('Description', trim($request->Description))->get();
			if (count($otask))
				$validator->errors()->add('Description', "Task exists already. (Task ID: {$otask[0]->Id})");			
		});
		
		if ($validator->fails()) {			
			$result['errors'] = $validator->errors();
			
			return response()->json($result, 422);
		}
		
		$data = new Task();
		if (!Session::has('TEST_USERNAME'))
			Session::put('TEST_USERNAME', env('TEST_USERNAME'));
		$data->Creator = Session::get('TEST_USERNAME');
		$data->Description = $request->Description;
		$data->Priority = $request->Priority;
		$data->Assignee = $request->Assignee;
		$data->DueDate = $request->DueDate;
		$data->Status = $request->Status;
		$data->DateAdded = date('Y-m-d H:i:s');
		$data->DateUpdated = date('Y-m-d H:i:s');
		$data->save();
		
		return response()->json([
			'message'=>'Task Created Successfully!!'
		]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
		if (!Session::has('TEST_IS_ADMIN'))
			Session::put('TEST_IS_ADMIN', env('TEST_IS_ADMIN'));
		$task->IsAdmin = Session::get('TEST_IS_ADMIN');
        return response()->json([
            'task'=>$task
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        $rules = [
			'Description' => array('required', 'string', 'max:200'),
			'Priority' => array('required', 'in:Low,Medium,High,Critical'),
			'Assignee' => array('string', 'max:100'),
			'DueDate' => array('required', 'date_format:Y-m-d'),
			'Status' => array('required', 'in:Pending,In Progress,Complete')
		];
				
		$messages = [
		];
		
		$validator = Validator::make($request->all(), $rules, $messages);
		
		$validator->after(function($validator) use ($request, $task) {	
			if ($request->DueDate < date('Y-m-d'))
				$validator->errors()->add('eDueDate', 'Due Date cannot be a past date.');
			
			$otask = Task::where([['Description', trim($request->Description)], ['Id', '<>', $task->Id]])->get();
			if (count($otask))
				$validator->errors()->add('Description', "Task exists already. (Task ID: {$otask[0]->Id})");			
		});
		
		if ($validator->fails()) {					
			$result['errors'] = $validator->errors();
			
			return response()->json($result, 422);
		}
		
		$data = array();
		$data['Description'] = $request->Description;
		$data['Priority'] = $request->Priority;
		if (!Session::has('TEST_IS_ADMIN'))
			Session::put('TEST_IS_ADMIN', env('TEST_IS_ADMIN'));
		if (Session::get('TEST_IS_ADMIN') && $request->Assignee)
			$data['Assignee'] = $request->Assignee;
		$data['DueDate'] = $request->DueDate;
		$data['Status'] = $request->Status;
		$data['DateUpdated'] = date('Y-m-d H:i:s');
		Task::where('Id', $task->Id)->update($data);
		
		return response()->json([
			'message'=>'Task Updated Successfully!!'
		]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
		if (!Session::has('TEST_USERNAME'))
			Session::put('TEST_USERNAME', env('TEST_USERNAME'));
		if ($task->Creator != Session::get('TEST_USERNAME')) {
			$result['message'] = 'You are not the creator of this task.';
			return response()->json($result, 422);
		}
		
        try {

            $task->delete();

            return response()->json([
                'message'=>'Task Deleted Successfully!!'
            ]);
            
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json([
                'message'=>'Something goes wrong while deleting a task!!'
            ]);
        }
    }
}
