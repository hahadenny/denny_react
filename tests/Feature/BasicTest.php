<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Task;
use DB;

class BasicTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
	public function testgetTasks()
    {
		$data = array();
        $this->json('GET', "/api/tasks", $data, ['Accept' => 'application/json'])
			->assertStatus(200)
            ->assertJsonStructure(
				['*' => [
					"Id",
					"DateAdded",
					"DateUpdated",
					"Creator",
					"Assignee",
					"Description",
					"Priority",
					"DueDate",
					"Status"
				]]);
    }
	
	public function testAddTaskRequireDescription()
	{
		$data = [
            'DateAdded' => date('Y-m-d H:i:s'),
			'DateUpdated' => date('Y-m-d H:i:s'),
			'Creator' => 'David',
			'Assignee' => 'John',
			'Description' => '',
            'Priority' => 'Low',
			'DueDate' => '2022-05-01',
			'Status' => 'Pending'			
	    ];
		
		$this->json('POST', '/api/tasks', $data, ['Accept' => 'application/json'])
			->assertStatus(422)
            ->assertJsonStructure(
				['errors' => [
					"Description"
				]]);
	}
	
	public function testAddTask()
    {
		$data = [
            'DateAdded' => date('Y-m-d H:i:s'),
			'DateUpdated' => date('Y-m-d H:i:s'),
			'Creator' => 'David',
			'Assignee' => 'John',
			'Description' => 'Test Test Test',
            'Priority' => 'Low',
			'DueDate' => '2022-05-01',
			'Status' => 'Pending'			
	    ];
		
		$this->json('POST', '/api/tasks', $data, ['Accept' => 'application/json'])
			->assertStatus(200)
            ->assertJson([
				'message' => 'Task Created Successfully!!'
				]);	
				
		global $taskId;
		$taskId = DB::getPdo()->lastInsertId();
	}		
	
	public function testAddNoDuplicateDescription()
    {
		$data = [
            'DateAdded' => date('Y-m-d H:i:s'),
			'DateUpdated' => date('Y-m-d H:i:s'),
			'Creator' => 'David',
			'Assignee' => 'John',
			'Description' => 'Test Test Test',
            'Priority' => 'Low',
			'DueDate' => '2022-05-01',
			'Status' => 'Pending'			
	    ];
		
		$this->json('POST', '/api/tasks', $data, ['Accept' => 'application/json'])
			->assertStatus(422)
            ->assertJsonStructure(
				['errors' => [
					"Description"
				]]);
	}
	
	public function testgetTaskById()
    {
		global $taskId;
		$data = array();
        $this->json('GET', "/api/tasks/$taskId", $data, ['Accept' => 'application/json'])
			->assertStatus(200)
            ->assertJsonStructure(
				['*' => [
					"Id",
					"DateAdded",
					"DateUpdated",
					"Creator",
					"Assignee",
					"Description",
					"Priority",
					"DueDate",
					"Status"
				]]);
    }
	
	public function testEditTask() 
	{
		global $taskId;
		
		$data = [
			'Description' => 'Test Test Test',
            'Priority' => 'Low',
			'DueDate' => '2022-05-01',
			'Status' => 'Complete'			
	    ];
		
		$this->json('PATCH', "/api/tasks/$taskId", $data, ['Accept' => 'application/json'])
			->assertStatus(200)
			->assertJson([
				'message' => 'Task Updated Successfully!!'
				]);
				
		$task = Task::where(['Id' => $taskId])->get()->first();
		if ($task)
			$this->assertEquals('Complete', $task->Status);
	}	
	
	public function testNonAdminCannotEditName() {
		global $taskId;
		
		$data = [
			'Description' => 'Test Test Test',
			'Assignee' => 'Denny Test',
            'Priority' => 'Low',
			'DueDate' => '2022-05-01',
			'Status' => 'Complete'			
	    ];
		
		$this->withSession(['TEST_USERNAME' => env('TEST_USERNAME'), 'TEST_IS_ADMIN' => 0])->json('PATCH', "/api/tasks/$taskId", $data, ['Accept' => 'application/json'])
			->assertStatus(200)
			->assertJson([
				'message' => 'Task Updated Successfully!!'
				]);
				
		$task = Task::where(['Id' => $taskId])->get()->first();
		if ($task)
			$this->assertEquals('John', $task->Assignee);
	}
	
	public function testAdminCanEditName() {
		global $taskId;
		
		$data = [
			'Description' => 'Test Test Test',
			'Assignee' => 'Denny Test',
            'Priority' => 'Low',
			'DueDate' => '2022-05-01',
			'Status' => 'Complete'			
	    ];
		
		$this->withSession(['TEST_USERNAME' => env('TEST_USERNAME'), 'TEST_IS_ADMIN' => 1])->json('PATCH', "/api/tasks/$taskId", $data, ['Accept' => 'application/json'])
			->assertStatus(200)
			->assertJson([
				'message' => 'Task Updated Successfully!!'
				]);
				
		$task = Task::where(['Id' => $taskId])->get()->first();
		if ($task)
			$this->assertEquals('Denny Test', $task->Assignee);
	}
	
	public function testNonCreatorCannotDelTask() 
	{
		global $taskId;
		
		$data = array();
		
		$this->withSession(['TEST_USERNAME' => env('TEST_USERNAME').' Test', 'TEST_IS_ADMIN' => env('TEST_IS_ADMIN')])->json('DELETE', "/api/tasks/$taskId", $data, ['Accept' => 'application/json'])
			->assertStatus(422)
            ->assertJson([
				'message' => 'You are not the creator of this task.'
				]);		
	}
	
	public function testDelTask() 
	{
		global $taskId;
		
		$data = array();
		
		$this->json('DELETE', "/api/tasks/$taskId", $data, ['Accept' => 'application/json'])
			->assertStatus(200)
            ->assertJson([
				'message' => 'Task Deleted Successfully!!'
				]);		
	}
}
