<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

// http://reference.yourdictionary.com/books-literature/different-types-of-books.html

class CreateBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('category_id')->unsigned()->index();
            $table->integer('user_id')->unsigned()->index();
            $table->enum('purpose', ['away', 'trade', 'sell']);
            $table->double('price');
            $table->integer('sale_off');
            $table->string('address')->nullable();
            $table->decimal('longitude', 9, 6);
            $table->decimal('latitude', 8, 6);
            $table->string('description');
            $table->string('publisher');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('books');
    }
}
