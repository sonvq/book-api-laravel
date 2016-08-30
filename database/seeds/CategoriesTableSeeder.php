<?php

use Illuminate\Database\Seeder;

use App\Category;

class CategoriesTableSeeder extends Seeder {

    public function run() {
        
        // http://reference.yourdictionary.com/books-literature/different-types-of-books.html
        DB::statement('SET FOREIGN_KEY_CHECKS = 0'); 
        DB::table('categories')->truncate();

        $category1 = Category::create(array('name' => 'Khoa học viễn tưởng'));
        
        $category2 = Category::create(array('name' => 'Châm biếm'));
        
        $category3 = Category::create(array('name' => 'Kịch'));
        
        $category4 = Category::create(array('name' => 'Hành động và phiêu lưu'));
        
        $category5 = Category::create(array('name' => 'Lãng mạn'));
        
        $category6 = Category::create(array('name' => 'Huyền bí'));
        
        $category7 = Category::create(array('name' => 'Kinh dị'));
        
        $category8 = Category::create(array('name' => 'Sức khỏe'));
        
        $category9 = Category::create(array('name' => 'Hướng dẫn'));
        
        $category10 = Category::create(array('name' => 'Du lịch'));
        
        $category11 = Category::create(array('name' => 'Dành cho trẻ em'));
        
        $category12 = Category::create(array('name' => 'Tôn giáo, tâm linh'));
        
        $category13 = Category::create(array('name' => 'Khoa học'));
        
        $category14 = Category::create(array('name' => 'Lịch sử'));
        
        $category15 = Category::create(array('name' => 'Toán học'));
        
        $category16 = Category::create(array('name' => 'Văn học'));
        
        $category17 = Category::create(array('name' => 'Thơ phú'));
        
        $category18 = Category::create(array('name' => 'Bách khoa toàn thư'));
        
        $category19 = Category::create(array('name' => 'Từ điển'));
        
        $category20 = Category::create(array('name' => 'Truyện tranh'));
        
        $category22 = Category::create(array('name' => 'Nghệ thuật'));
        
        $category23 = Category::create(array('name' => 'Sách dạy nấu ăn'));
        
        $category24 = Category::create(array('name' => 'Nhật kí'));
        
        $category25 = Category::create(array('name' => 'Tạp chí'));
        
        $category26 = Category::create(array('name' => 'Sách cầu nguyện'));
        
        $category27 = Category::create(array('name' => 'Tiểu sử'));
        
        $category28 = Category::create(array('name' => 'Tự truyện'));
        
        $category29 = Category::create(array('name' => 'Ảo tưởng'));
        
    }

}
