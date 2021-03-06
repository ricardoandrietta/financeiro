<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProvisionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		if (!Schema::hasTable('provisions')) {
			Schema::create('provisions', function (Blueprint $table) {
				$table->increments('id');
				$table->unsignedInteger('category_id');
				$table->foreign('category_id')->references('id')->on('categories');
				$table->unsignedInteger('transaction_type_id');
				$table->foreign('transaction_type_id')->references('id')->on('transaction_types');
                $table->unsignedInteger('account_id');
                $table->foreign('account_id')->references('id')->on('accounts');
				$table->unsignedInteger('user_id');
				$table->foreign('user_id')->references('id')->on('users');
				$table->decimal('value', 14, 2);
				$table->unsignedSmallInteger('status')->default(1);
				$table->timestamps();
			});
		}
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('provisions');
    }
}
