<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Task extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Task', function (Blueprint $table) {
			$table->increments('Id');
			$table->dateTime('DateAdded');
			$table->dateTime('DateUpdated');
			$table->string('Creator');
			$table->string('Assignee');
			$table->longText('Description');
			$table->enum('Priority', ['Low', 'Medium', 'High', 'Critical']);			
			$table->date('DueDate');
			$table->enum('Status', ['Pending', 'In Progress', 'Complete']);
        });

		// Insert some test data
        DB::table('Task')->insert([[
            'DateAdded' => DB::raw('NOW()'),
			'DateUpdated' => DB::raw('NOW()'),
			'Creator' => 'David',
			'Assignee' => 'John',
			'Description' => 'Add Custom Titles',
            'Priority' => 'Low',
			'DueDate' => '2022-05-01',
			'Status' => 'Pending'			
	    ],
	    [
            'DateAdded' => DB::raw('NOW()'),
			'DateUpdated' => DB::raw('NOW()'),
			'Creator' => 'Denny',
			'Assignee' => 'James',
			'Description' => 'Add Custom Buttons',
            'Priority' => 'High',
			'DueDate' => '2022-04-01',
			'Status' => 'In Progress'	
	    ]	
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('Task');
    }
}
